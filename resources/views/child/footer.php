<?if(isset($rds)){?>
    <div style="padding:6px 0 6px 10%">
        <h5 style="display: inline-block">Итого по выбранному периоду: Профит:<span style="color:green"> <?=$rds['endTotal']?>р.</span>, в ожидании: <span style="color: #31b0d5;"><?=$rds['processTotal']?>р.</span></h5>
Вручено: <span style="color:green"><?=$rds['st4']?>%</span> Отказов: <span style="color: red"><?=$rds['st5']?>%</span><button class="btn-default btn btn-xs btn-warning" data-toggle="modal" data-target="#reason">(Причины)</button></div>
                    <div id="reasonCtn">
                        <?php foreach($rds['reason'] as $reason=>$count) echo "$reason : $count <br>";?>
                    </div>
    <script type="text/javascript">
            $('document').ready(
                function(){
                jQuery('#reasonContainer').html(jQuery('#reasonCtn').html());
                });
    </script>
<?
}else{?>
ELZA WORK IN PROGRESS
<?}