<form id="bitrixListForm" method="post">
    <div class="form-group row">
        <div class="col-xs-12">
            <label>Дата начала:<input class="date form-control" type="text" name="dateFirst" autocomplete="off" value="<?=$data['dateFirst']?>"></label>
            <label>Дата окончания:<input class="date form-control" type="text" name="dateLast" autocomplete="off" value="<?=$data['dateLast']?>"></label>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-xs-12">
            <input type="submit" name="date" class="btn btn-default">
        </div>
    </div>
</form>
<table class="table table-striped">
    <thead> <tr> <th>Дата</th> <th>Номер</th> <th>Клиент</th> <th width="40%">Статус</th> <th></th> </tr> </thead>
    <?php foreach($data['ORDERS'] as $order):?>
        <tr>
            <td><?=$order["DATE_INSERT"]?></td>
            <td><?=$order["ACCOUNT_NUMBER"]?></td>
            <td><?=$order["USER_NAME"]?> <?=$order["USER_LAST_NAME"]?></td>
            <td><?=$order["STATUS_NAME"]?> <?=$order["STATUS_DESCRIPTION"]?></td>
            <td><button class="btn btn-sm btn-primary BitrixModal" data-toggle="modal" data-target="#BitrixModal" id="allAboutBitrix" href="#allAboutBitrix" data-number="<?=$order["ACCOUNT_NUMBER"]?>">Подробнее</button></td>
        </tr>
    <?php endforeach;?>
</table>