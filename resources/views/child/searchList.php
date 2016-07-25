<form id="searchListForm" method="post">
    <div class="form-group row">
        <div class="col-xs-12">
            <label>Категория:<input class="form-control" type="text" name="category" autocomplete="off" value="<?=$data['category']?>"></label>
            <label>Город:<input class="form-control" type="text" name="city" autocomplete="off" value="<?=$data['city']?>"></label>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-xs-12">
            <input type="submit" name="search" class="btn btn-default">
        </div>
    </div>
</form>
<table class="table table-striped">
    <thead> <tr> <th>Запрос</th> <th>Сервис</th> <th>Сайт</th> </tr> </thead>
    <?php /*dd($result);*/ foreach($result as $region=>$cities):?>
        <?php foreach($cities as $city=>$queries):?>
            <?php foreach($queries as $query=>$services):?>
                <?php foreach($services as $service_name=>$service):?>
                    <?php foreach($service as $service_group=>$sites):?>
                        <?php foreach($sites as $site):?>
                            <tr>
                                <td><?=$query?></td>
                                <td><?=$service_group?> <?=$service_name?></td>
                                <td><?=$site?></td>
                            </tr>
                        <?php endforeach;?>
                    <?php endforeach;?>
                <?php endforeach;?>
            <?php endforeach;?>
        <?php endforeach;?>
    <?php endforeach;?>
</table>