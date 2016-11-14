<form class="form-inline">
    <div class="form-group row">
       <div class="col-xs-3"> <selectl><select placeholder="Город" class="form-control" type="text" name="city">
                                    <?php foreach($data['cities'] as $city){?>
                                          <option <?php if(isset($_GET['city'])&&!empty($_GET['city'])){
                                              if($city->city==$_GET['city']) echo 'selected';
                                          }?>><?php echo $city->city;?></option>
                                    <?php }?>
                             </selectl></div>
        <div class="col-xs-3"><label><input placeholder="РДС Только номер" class="form-control" type="text" name="number" <?php if(isset($_GET['number'])&&!empty($_GET['number'])) echo "value='{$_GET['number']}'";?>></label></div>
            <div class="col-xs-3"> <label><input placeholder="Клиент" class="form-control" type="text" name="name" <?php if(isset($_GET['name'])&&!empty($_GET['name'])) echo "value='{$_GET['name']}'";?>></label></div>
            <div class="col-xs-3"> <input placeholder="Дата начала" type="text" class="dateRDS form-control" name="start" <?php if(isset($_GET['start'])&&!empty($_GET['start'])) echo "value='{$_GET['start']}'";
                    else{
                        $y= date('Y');
                        $m=date('m');
                        echo "value=".$y."-".$m."-1";}
                ?>></div>
            <div class="col-xs-3"> <input placeholder="Дата окончания" type="text" class="dateRDS form-control" name="finish" <?php if(isset($_GET['finish'])&&!empty($_GET['finish'])) echo "value='{$_GET['finish']}'";?>></div>
        <div class="col-xs-8 col-xs-push-6"><label><input placeholder="Номер СДЭК" class="form-control" type="text" name="dispatch" <?php if(isset($_GET['dispatch'])&&!empty($_GET['dispatch'])) echo "value='{$_GET['dispatch']}'";?>></label></div>
    </div>
    <div class="form-group row">

        <div class="col-xs-3"><label><input placeholder="Автор" type="text" name="author" class="form-control" <?php if(isset($_GET['author'])&&!empty($_GET['author'])) echo "value='{$_GET['author']}'";?>></label></div>
        <div class="col-xs-3 col-xs-push-3"><input type="submit" name="submit" class="btn btn-default"></div>
        <div class="col-xs-3 col-xs-push-6"><label><input type="checkbox" name="rds" value="rds" <?php if(!isset($_GET['rds'])&&!isset($_GET['rdi']) || isset($_GET['rds'])) echo "checked";?>>&nbsp;РДС</label></div>
        <div class="col-xs-3 col-xs-push-6"><label><input type="checkbox" name="rdi" value="rdi" <?php if(!isset($_GET['rds'])&&!isset($_GET['rdi']) || isset($_GET['rdi'])) echo "checked";?>>&nbsp;РДИ</label></div>
    </div>
</form>
<table id="myTable"  class="table table-striped tablesorter">
    <thead> <tr> <th>Дата</th> <th>Номер</th> <th>Клиент</th> <th>Автор</th><th>Город</th><th>Итого</th><th></th><th></th> </tr> </thead>
    <?php
    foreach($data['query'] as $rds):?>
        <tr>
            <td><?php $date = DateTime::createFromFormat('Y-m-d', $rds->date);
                echo $date->format('d-m-Y');?></td>
            <td><?php echo $rds->number;?></td>
            <td><?php echo $rds->name?></td>
            <td><?php echo $rds->author?></td>
            <td><?php echo $rds->city?></td>
            <td><?if(isset($rds->total)){
                    switch($rds->status_code){
                        case 81: $label="label status-return";
                            break;
                        case 777: $label="label label-primary";
                            break;
                        case 4: $label="label label-success";
                            if($rds->total<=0)$label="label label-warning";
                            break;
                        case 5: $label="label label-danger";
                            break;
                        default:  $label="label label-info";
                    }
                    ?><span class="<?=$label?>"><?=$rds->total?></span><?}?></td>
            <td><button class="btn btn-sm btn-primary RDSmodal" data-toggle="modal" data-target="#RDSModal" id="allAboutRDS" href="#allAboutRDS" data-rds="<? echo $rds->number ?>">Подробнее</button>
            </td>
            <td><?if(!isset($rds->total)&&$rds->city!='Воронеж'){?>
                <button class="btn btn-sm btn-warning newCDEKmodal" data-toggle="modal" data-target="#newCDEKModal" id="newCDEK" href="#newCDEK" data-rds="<? echo $rds->number ?>">Оформить</button>
            <?}?></td>
        </tr>
    <?php endforeach;?>
</table>