<form id="searchStatsForm" method="get">
    <div class="form-group row">
        <div class="col-xs-12">
            <label>Дата начала:<input class="dateRDS form-control" type="text" name="dateFirst" autocomplete="off" value="<?=$form['dateFirst']?>"></label>
            <label>Дата окончания:<input class="dateRDS form-control" type="text" name="dateLast" autocomplete="off" value="<?=$form['dateLast']?>"></label>
            <label>Город:<select class="form-control" name="city">
                    <option value="!empty!">Выберите город</option>
                    <?php foreach($cities as $city){?>
                        <option <?php if(isset($form['city'])){
                            if($city->city==$form['city']) echo 'selected';
                        }?>><?php echo $city->city;?></option>
                    <?php }?>
                </select></label>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-xs-12">
            <input type="submit" name="date" class="btn btn-default">
        </div>
    </div>
</form>
<h2>Анализ результатов по категориям</h2>
<?if(isset($data['category'])):?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>№</th><th>Категория</th><th>Сумма</th><th>Количество</th><th>Профит</th><th>% профита</th><th>Ср. профит</th>
        </tr>
    </thead>
    <?$n=0?>
        <?php foreach($data['category'] as $head=>$categories):?>
            <tr class="success"><td><?=++$n?>.</td><td><?=$head?></td><td></td><td></td><td></td><td></td><td></td></tr>
            <tr>
                <td></td>
                <td>
                    <ol>
                    <?php foreach($categories as $num=>$category):?>
                        <li <?=empty($num)?' style="text-decoration: underline"':''?>>
                            <?=$category->info_category?>
                        </li>
                    <?php endforeach; ?>
                    </ol>
                </td>
                <td>
                    <ul class="stats">
                        <?php foreach($categories as $num=>$category):?>
                            <li <?=empty($num)?' style="text-decoration: underline"':''?>>
                                <b><?=number_format($category->sum_price, 2, ',', ' ')?> руб.</b>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </td>
                <td>
                    <ul class="stats">
                        <?php foreach($categories as $num=>$category):?>
                            <li <?=empty($num)?' style="text-decoration: underline"':''?>>
                                <?=$category->sum_quantity?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </td>
                <td>
                    <ul class="stats">
                        <?php foreach($categories as $num=>$category):?>
                            <li <?=empty($num)?' style="text-decoration: underline"':''?>>
                                <b><?=number_format($category->profit, 2, ',', ' ')?> руб.</b>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </td>
                <td>
                    <ul class="stats">
                        <?php foreach($categories as $num=>$category):?>
                            <li <?=empty($num)?' style="text-decoration: underline"':''?>>
                                <?=empty($category->sum_price)?0:round($category->profit/$category->sum_price*100,2)?>%
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </td>
                <td>
                    <ul class="stats">
                        <?php foreach($categories as $num=>$category):?>
                            <li <?=empty($num)?' style="text-decoration: underline"':''?>>
                                <b><?=number_format(empty($category->sum_quantity)?0:($category->profit/$category->sum_quantity), 2, ',', ' ')?> руб.</b>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </td>
            </tr>
        <?php endforeach; ?>
    <tr class="danger">
        <td></td><td align="right">ИТОГО:</td>
        <td><b><?=number_format($data['category_all']['sum_price'], 2, ',', ' ')?> руб.</b></td>
        <td><?=$data['category_all']['sum_quantity']?></td>
        <td><b><?=number_format($data['category_all']['profit'], 2, ',', ' ')?> руб.</b></td>
        <td><?=empty($data['category_all']['sum_price'])?0:round($data['category_all']['profit']/$data['category_all']['sum_price']*100,2)?>%</td>
        <td><b><?=number_format(empty($data['category_all']['sum_quantity'])?0:($data['category_all']['profit']/$data['category_all']['sum_quantity']), 2, ',', ' ')?> руб.</b></td>
    </tr>
</table>
<?php else: ?>
    <h3>нет данных</h3>
<?php endif; ?>
<h2>Анализ результатов по брендам</h2>
<?if(isset($data['brand'])):?>
<table class="table table-bordered">
    <thead>
    <tr>
        <th>№</th><th>Бренд</th><th>Сумма</th><th>Количество</th><th>Профит</th><th>% профита</th><th>Ср. профит</th>
    </tr>
    </thead>
    <?$n=0?>
    <?php foreach($data['brand'] as $brand):?>
        <tr class="warning">
            <td><?=++$n?>.</td>
            <td><?=empty($brand->brand)?'Без бренда':$brand->brand?></td>
            <td><b><?=number_format($brand->sum_price, 2, ',', ' ')?> руб.</b></td>
            <td><?=$brand->sum_quantity?></td>
            <td><b><?=number_format($brand->profit, 2, ',', ' ')?> руб.</b></td>
            <td><?=empty($brand->sum_price)?0:round($brand->profit/$brand->sum_price*100,2)?>%</td>
            <td><b><?=number_format(empty($brand->sum_quantity)?0:($brand->profit/$brand->sum_quantity), 2, ',', ' ')?> руб.</b></td>
        </tr>
    <?php endforeach; ?>
    <tr class="danger">
        <td></td><td align="right">ИТОГО:</td>
        <td><b><?=number_format($data['brand_all']['sum_price'], 2, ',', ' ')?> руб.</b></td>
        <td><?=$data['brand_all']['sum_quantity']?></td>
        <td><b><?=number_format($data['brand_all']['profit'], 2, ',', ' ')?> руб.</b></td>
        <td><?=empty($data['brand_all']['sum_price'])?0:round($data['brand_all']['profit']/$data['brand_all']['sum_price']*100,2)?>%</td>
        <td><b><?=number_format(empty($data['brand_all']['sum_quantity'])?0:($data['brand_all']['profit']/$data['brand_all']['sum_quantity']), 2, ',', ' ')?> руб.</b></td>
    </tr>
</table>
<?php else: ?>
    <h3>нет данных</h3>
<?php endif; ?>
<h2>Анализ результатов по городам</h2>
<?if(isset($data['city'])):?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>№</th><th>Город</th><th>Сумма</th><th>Количество</th><th>Профит</th><th>% профита</th><th>Ср. профит</th>
        </tr>
        </thead>
        <?$n=0?>
        <?php foreach($data['city'] as $city):?>
            <tr class="warning">
                <td><?=++$n?>.</td>
                <td><a href="/stats?city=<?=$city->city?>&dateFirst=<?=$form['dateFirst']?>&dateLast=<?=$form['dateLast']?>"><?=empty($city->city)?'Без города':$city->city?></a></td>
                <td><b><?=number_format($city->sum_price, 2, ',', ' ')?> руб.</b></td>
                <td><?=$city->sum_quantity?></td>
                <td><b><?=number_format($city->profit, 2, ',', ' ')?> руб.</b></td>
                <td><?=empty($city->sum_price)?0:round($city->profit/$city->sum_price*100,2)?>%</td>
                <td><b><?=number_format(empty($city->sum_quantity)?0:($city->profit/$city->sum_quantity), 2, ',', ' ')?> руб.</b></td>
            </tr>
        <?php endforeach; ?>
        <tr class="danger">
            <td></td><td align="right">ИТОГО:</td>
            <td><b><?=number_format($data['city_all']['sum_price'], 2, ',', ' ')?> руб.</b></td>
            <td><?=$data['city_all']['sum_quantity']?></td>
            <td><b><?=number_format($data['city_all']['profit'], 2, ',', ' ')?> руб.</b></td>
            <td><?=empty($data['city_all']['sum_price'])?0:round($data['city_all']['profit']/$data['city_all']['sum_price']*100,2)?>%</td>
            <td><b><?=number_format(empty($data['city_all']['sum_quantity'])?0:($data['city_all']['profit']/$data['city_all']['sum_quantity']), 2, ',', ' ')?> руб.</b></td>
        </tr>
    </table>
<?php else: ?>
    <h3>нет данных</h3>
<?php endif; ?>