var notReqStyles={
    border:"solid 1px red"
};
var ReqStyles={
    border:""
};
var sendData =  {}

function solValidate(formChilds){
    var isvalid=true;
    formChilds.each(function(i){
        $(this).css(ReqStyles);
        if($(this).attr("required")){
            if($(this).val()==''){
                $(this).css(notReqStyles);
                isvalid = false
            }if($(this).data('minlength')){
                console.log($(this).val().length)
                if($(this).data('minlength')>$(this).val().length){
                    $(this).css(notReqStyles);
                    isvalid = false
                }
            }
        }
        var name= $(this).attr("name")
        var val=$(this).val()
        sendData[name] = val;
        console.log(sendData);
    })
    return isvalid
}
$(document).on('click',".validateAndSend",
    function(){

        var formId=$(this).data('form');
        var url=$(this).data('url');
        var formInputs=$('#'+formId+' [name]');
        console.log(formInputs);
        //solValidate(textareas);

        if(solValidate(formInputs)) {
            console.log('Sended!!!')
            $.ajax({
                    method: "POST",
                    url: url,
                    data: sendData
                })
                .done(function (html) {
                    $("#newCDEKContent").html(html);
                });
        }
    }
);