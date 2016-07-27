$(document).ready(function(){
    $('#findRDSbtn').click(
        function(){
            var rds=$('#findRDSinput').val();
            var get= "/ajax/doc/РДС-"+rds;


            $.ajax({
                    url: get,
                    cache: false
                })
                .done(function( html ) {
                    $( "#results" ).append( html );
                });

        }
    );
    $(document).on('click',"#registerGoodsProblem1",
        function(){
            console.log('we');
            var rds=$('#goodsProblemDoc').val();
            var desc=$('#goodsproblemDescription').val();
            var status=$('select[name="step"]').val();
            var stime=$('select[name="setTime"]').val();

            console.log('we');
            $.ajax({
                    method: "POST",
                    url: "/ajax/addProblemTask",
                    data: { docnum: rds, description: desc,step: status,setTime:stime}
                })
                .done(function( html ) {
                    $("#AppealReg").remove();
                    $( "#resultsAppealReg" ).append( html );
                });

        }
    );
    $('.taskMore').click(
        function(){
            $( "#taskcontent" ).html('');
            var id =$(this).data('id');
            var type =$(this).data('type');
            console.log(id);
            $.ajax({
                    method: "POST",
                    url: "/task",
                    data: { id: id,
                            type:type
                    }
                })
                .done(function( html ) {
                    $( "#taskcontent" ).html(html);
                    $('#TaskForm').validator();
                    console.log($('#tttt'))
                });

        }
    );
    $(document).on('click','.showALLbyRDS',
        function(){
            var rds=$(this).data('rds');
            console.log(rds);
            $.ajax({
                    method: "POST",
                    url: "/ajax/getfortask",
                    data: { rds: rds
                    }
                })
                .done(function( html ) {
                    $( "#allAboutRDS" ).html(html);
                });

        }
    );
    $(document).on('click','.showCDEK',
        function(){
            var rds=$(this).data('rds');
            console.log(rds);
            $.ajax({
                method: "POST",
                url: "/ajax/getcdek",
                data: { rds: rds
                }
            })
            .done(function( html ) {
                $( "#allAboutCDEK" ).html(html);
            });
        }
    );

    var dpParams={
        dateFormat: "dd.mm.yy",
        firstDay: 1,
        dayNamesMin: [ "Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб" ],
        monthNames: [ "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь" ]
    };

    $('input.date').datepicker(dpParams);
    dpParams.dateFormat='yy-mm-dd';
    $('input.dateRDS').datepicker(dpParams);
    $('.cdek-city-select').change();
});
$(document).on('click','.RDSmodal',
    function(){
        $( "#RDSContent" ).html('');
        var rds=$(this).data('rds');
        console.log(rds);
        $.ajax({
            method: "POST",
            url: "/ajax/getfortask",
            data: { rds: rds }
        })
        .done(function( html ) {
            $( "#RDSContent" ).html(html);
        });
    }
);
$(document).on('submit','#bitrixListForm',
    function(){
        $('main.main table').html('Подождите...');
        $.post({
            url: '/ajax/bitrixlist',
            data: $('#bitrixListForm').serializeArray()
        }).always(function(data,status) {
            if(status!='success')window.alert(status);
            else $('main.main table').html($('<div/>').html(data).find('table').html());
        });

        return false;
    }
);

var g_log;
var updLog=function (str) {
    if(str)g_log+='<br>'+str;
    $('main.main table').html(g_log);
    $('main.main').scrollTop(9999999);
};
var go=function (result,date,num) {
    if(result.steps[num]) {
        var el = result.steps[num];
        el.date = date;
        console.log(el);
        updLog(el.text);
        //go(result,date,num+1);
        /*var text = el.text;
         var city_name = el.city_name;*/
        $.post({
            url: '/ajax/runSearchStep',
            data: el
        }).always(function (data, status) {
            if (status != 'success')window.alert(status);
            else {
                console.log(data);
                if (data.error&&data.error.yandex){
                    if(data.img) {
                        g_log+=': <b>enter code</b>';

                        var img = $('<img>').attr('src',data.img).addClass('image').addClass('form__captcha');
                        var key = $('<input>').attr('value',data.key).attr('type','hidden').addClass('form__key');
                        var retpath = $('<input>').attr('value',data.retpath).attr('type','hidden').addClass('form__retpath');
                        var code = $('<input>').attr('type','text').addClass('form__rep');
                        $('main.main table').append(img).append(key).append(retpath).append(code);
                        $('main.main').scrollTop(9999999);
                        code.keypress(function(event) {
                            var keycode = (event.keyCode ? event.keyCode : event.which);
                            if(keycode == '13'){
                                $.get({
                                    url: '/checkcaptcha',
                                    data: {
                                        key:$('.form__key').val(),
                                        retpath:$('.form__retpath').val(),
                                        rep:$('.form__rep').val()
                                    }
                                }).always(function (data, status) {
                                    if (status != 'success')updLog('Ошибка!');
                                    go(result,date,num);
                                });
                                updLog();
                            }
                        }).focus();
                    }else{
                        updLog('Повтор операции (ошибка Yandex)...');
                        go(result,date,num);
                    }
                }
                else if(data.error&&data.error.google){
                    updLog('Повтор операции (ошибка Google)...');
                    go(result,date,num);
                }
                else {
                    g_log+=': '+data.status;
                    go(result,date,num+1);
                }
            }
        });
    }else{
        updLog('Подготовка статистических данных...');
        $.get({
            url: '/ajax/searchStats',
            data: result.params
        }).always(function (data, status) {
            if (status != 'success')updLog('Ошибка!');
            else updLog('Готово! ('+data+')');
        });
    }
};
$(document).on('submit','#searchListForm',
    function(){
        $('main.main table').html('Подождите...');
        $.post({
            url: '/ajax/searchSteps',
            data: $('#searchListForm').serializeArray()
        }).always(function(data,status) {
            if(status!='success')window.alert(status);
            else {
                var result=data;
                var date=result.date;
                g_log='Обработка...';
                go(result,date,0);
            }
        });

        return false;
    }
);

$(document).on('click','.BitrixModal',
    function(){
        $( "#BitrixContent" ).html('');
        var number=$(this).data('number');
        var site=$(this).data('site');
        $.ajax({
            method: "POST",
            url: "/ajax/getforbitrix",
            data: {
                number: number,
                site: site
            }
        })
        .always(function( html,status,err ) {
            if(status!='success')$( "#BitrixContent" ).html(err);
            else $( "#BitrixContent" ).html(html);
        });
    }
);
$(document).on('click','.package-add',
    function(){
        var pack=$(this).data('package');
        var content=$(this).parent().parent();
        $.ajax({
                method: "POST",
                url: "/ajax/addcdekelem",
                data: { package: pack }
            })
            .always(function( html,status,err ) {
                if(status!='success')window.alert(err);
                else {
                    content.replaceWith(html);
                }
            });
    }
);
$(document).on('click','.product-add',
    function(){
        var pack=$(this).data('package');
        var item=$(this).data('item');
        var content=$(this).parent().parent();
        $.ajax({
                method: "POST",
                url: "/ajax/addcdekelem",
                data: { package: pack,item: item }
            })
            .always(function( html,status,err ) {
                if(status!='success')window.alert(err);
                else {
                    content.replaceWith(html);
                }
            });
    }
);
var getPVZ=function(){
    var city=$('.cdek-city-select').val();
    var form=$('.cdek-form').serialize();
    $.ajax({
            method: "POST",
            url: "/ajax/pvzlist",
            data: { cityID: city , form: form }
        })
        .always(function( html,status,err ) {
            if(status!='success')$( ".cdek-pvz" ).html(err);
            else $( ".cdek-pvz" ).html(html);
        });
};
$(document).on('change','.form-group.package input',
    function(){
        getPVZ();
    }
);
$(document).on('click','.newCDEKmodal',
    function(){
        var rds=$(this).data('rds');
        console.log(rds);
        $.ajax({
                method: "POST",
                url: "/ajax/addcdek",
                data: { rds: rds }
            })
            .done(function( html ) {
                $( "#newCDEKContent" ).html(html);
                getPVZ();
            });
    }
);

$(document).ready(function() {

    var app = {

        initialize : function () {
            this.setUpListeners();
        },

        setUpListeners: function () {
            $(document).on('submit', 'form.cdek-form', app.submitForm).on('keydown', '.has-error', app.removeError);
        },

        submitForm: function (e) {
            //e.preventDefault();

            var form = $(this);

            // если валидация не проходит - то дальше не идём
            if ( app.validateForm(form) === false )	return false;
        },

        validateForm: function (form){

            var inputs = form.find('input,select'),
                submit = form.find('input[type=submit]'),
                valid = true;

            submit.tooltip('destroy');
            form.find('.form-group').removeClass('has-error').removeClass('has-success');

            $.each(inputs, function(index, el) {
                var input = $(el),
                    formGroup = input.parents('.form-group'),
                    val = input.val();

                if(input.attr('type')!='checkbox') {
                    if (val.length === 0) {
                        formGroup.addClass('has-error').removeClass('has-success');
                        valid = false;
                    } else {
                        if (!formGroup.hasClass('has-error'))
                            formGroup.addClass('has-success');
                    }
                }
            });

            var errstr='';
            if(!valid)errstr='Имеются незаполненные поля\n';

            if(form.find('.package').size()==0){
                errstr+='Отсутствует комплект';
                valid = false;
            }else if(form.find('.product').size()==0){
                errstr+='Отсутствуют товары';
                valid = false;
            }

            if(errstr.length>0) {
                submit.tooltip({
                    trigger: 'manual',
                    placement: 'right',
                    title: errstr
                }).tooltip('show');
            }

            return valid;

        },

        removeError: function() {
            $(this).removeClass('has-error');
        }

    };

    app.initialize();

});

$(document).ready(function()
    {
        $("#myTable").tablesorter();
    }
);