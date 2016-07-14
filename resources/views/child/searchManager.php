<form id="searchManagerForm" method="post">
    <div class="form-group row">
        <div class="col-xs-12">
            <label>Новое ключевое слово:<input class="form-control" type="text" name="new_keyword" autocomplete="off" value=""></label>
            <label>Удалить ключевое слово:
                <select class="form-control" name="delete_keyword">
                    <option value="">Выберите ключевое слово</option>
                    <?php foreach($keywords as $keyword):?>
                        <option value="<?=$keyword->keyword_id?>"><?=$keyword->keyword_name?></option>
                    <?php endforeach;?>
                </select>
            </label>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-xs-12">
            <label>Группа категорий:
                <select class="form-control" name="category_group_id">
                    <option value="">Выберите группу</option>
                    <?php foreach($groups as $group):?>
                        <option value="<?=$group->category_group_id?>"><?=$group->category_group_name?></option>
                    <?php endforeach;?>
                </select>
            </label>
            <label>Новая группа:<input class="form-control" type="text" name="new_category_group" autocomplete="off" value=""></label>
            <label>Новая категория:<input class="form-control" type="text" name="new_category" autocomplete="off" value=""></label>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-xs-12">
            <label>Удалить группу:
                <select class="form-control" name="delete_category_group">
                    <option value="">Выберите группу</option>
                    <?php foreach($groups as $group):?>
                        <option value="<?=$group->category_group_id?>"><?=$group->category_group_name?></option>
                    <?php endforeach;?>
                </select>
            </label>
            <label>Удалить категорию:
                <select class="form-control" name="delete_category">
                    <option value="">Выберите категорию</option>
                    <?php foreach($categories as $category):?>
                        <option value="<?=$category->category_id?>"><?=$category->category_name?></option>
                    <?php endforeach;?>
                </select>
            </label>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-xs-12">
            <label>Новый бренд:<input class="form-control" type="text" name="new_brand" autocomplete="off" value=""></label>
            <label>Удалить бренд:
                <select class="form-control" name="delete_brand">
                    <option value="">Выберите бренд</option>
                    <?php foreach($brands as $brand):?>
                        <option value="<?=$brand->brand_id?>"><?=$brand->brand_name?></option>
                    <?php endforeach;?>
                </select>
            </label>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-xs-12">
            <label>Регион:
                <select class="form-control" name="region_id">
                    <option value="">Выберите регион</option>
                    <?php foreach($regions as $region):?>
                        <option value="<?=$region->region_id?>"><?=$region->region_name?></option>
                    <?php endforeach;?>
                </select>
            </label>
            <label>Новый регион:<input class="form-control" type="text" name="new_region" autocomplete="off" value=""></label>
            <label>Новый город:<input class="form-control" type="text" name="new_city" autocomplete="off" value=""></label>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-xs-12">
            <label>Удалить регион:
                <select class="form-control" name="delete_region">
                    <option value="">Выберите регион</option>
                    <?php foreach($regions as $region):?>
                        <option value="<?=$region->region_id?>"><?=$region->region_name?></option>
                    <?php endforeach;?>
                </select>
            </label>
            <label>Удалить города этого региона<input class="form-control" type="checkbox" name="delete_region_cities" autocomplete="off" value=""></label>
            <label>Удалить город:
                <select class="form-control" name="delete_city">
                    <option value="">Выберите город</option>
                    <?php foreach($cities as $city):?>
                        <option value="<?=$city->city_name?>"><?=$city->city_name?></option>
                    <?php endforeach;?>
                </select>
            </label>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-xs-12">
            <input type="submit" name="date" class="btn btn-default">
        </div>
    </div>
</form>
<table class="table table-striped">
    <thead> <tr> <th>№</th> <th>Регион</th> <th>Запрос</th> </tr> </thead>
    <?php $region='';
    $count=0;
    $city_count=0;
    foreach($cities as $city):
        do {
            $city_name=$city_count?$city->city_name:'';
            $keyword_count=0;
                foreach ($keywords as $keyword):
                    do {
                        $keyword_name = $keyword_count ? $keyword->keyword_name : '';
                        ?>
                        <?php foreach ($groups as $group):
                            $region_out = '';
                            if ($region != $city->region_name && $city_name != '') {
                                $region_out = $city->region_name;
                                $region = $city->region_name;
                            }
                            ?>
                            <tr>
                                <td><?= ++$count ?></td>
                                <td><?= $region_out ?></td>
                                <td><?= $group->category_group_name ?> <?= $keyword_name ?> <?= $city_name ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php foreach ($brands as $brand): ?>
                            <tr>
                                <td><?= ++$count ?></td>
                                <td><?= $region_out ?></td>
                                <td><?= $brand->brand_name ?> <?= $keyword_name ?> <?= $city_name ?></td>
                            </tr>
                        <?php endforeach;
                        $keyword_count++;
                    }while($keyword_count==1);?>
                <?php endforeach;
            $city_count++;
        }while($city_count==1);
        ?>
    <?php endforeach;?>
</table>