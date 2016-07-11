<form id="bitrixListForm" method="post">
    <div class="form-group row">
        <div class="col-xs-12">
            <label>Категория:<input class="form-control" type="text" name="category" autocomplete="off" value="<?=$data['category']?>"></label>
            <label>Город:<input class="form-control" type="text" name="city" autocomplete="off" value="<?=$data['city']?>"></label>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-xs-12">
            <input type="submit" name="date" class="btn btn-default">
        </div>
    </div>
</form>
<table class="table table-striped">
    <thead> <tr> <th>Запрос</th> <th>Сервис</th> <th>Сайт</th> </tr> </thead>
    <?php foreach($result as $query):?>
        <?php foreach($query["SEARCH"] as $search):?>
            <?php foreach($search["RESULT"] as $service_group=>$service):?>
                <?php foreach($service as $service_name=>$sites):?>
                    <?php foreach($sites as $site):?>
                        <tr>
                            <td><?=$query["QUERY"]?></td>
                            <td><?=$service_group?> <?=$service_name?></td>
                            <td><?=$site?></td>
                        </tr>
                    <?php endforeach;?>
                <?php endforeach;?>
            <?php endforeach;?>
            <?php foreach($search["RESULT_CITY"] as $service_group=>$service):?>
                <?php foreach($service as $service_name=>$sites):?>
                    <?php foreach($sites as $site):?>
                        <tr>
                            <td><?=$query["QUERY"]?> <?=$search["REGION"]?></td>
                            <td><?=$service_group?> <?=$service_name?></td>
                            <td><?=$site?></td>
                        </tr>
                    <?php endforeach;?>
                <?php endforeach;?>
            <?php endforeach;?>
        <?php endforeach;?>
    <?php endforeach;?>
</table>