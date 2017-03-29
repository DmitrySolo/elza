<div class="container">
    <form class="form-horizontal cdek-form" method="post" action="/addcdek">
        <div class="cdek-group">
            <div class="form-group">
                <div class="col-xs-6">
                    <label><span>Номер документа</span><input type="text" class="form-control" placeholder="Номер документа" name="Number" value="<?=isset($input)?$input['Number']:''?>"></label>
                </div>
                <div class="col-xs-6">
                    <label><span>Стоимость доставки</span><input type="text" class="form-control" placeholder="Стоимость доставки" name="deliveryCost" value="<?=isset($input)?$input['deliveryCost']:''?>"></label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-6 cdek-add-info">
                </div>
            </div>
        </div>
        <div class="cdek-group where">
            <h3>Местоположение</h3>
            <div class="form-group">
                <div class="col-xs-3">
                    <label><span>Город</span>
                        <select name="city" class="form-control cdek-city-select">
                            <option value="">Выберите город</option>
                            <?$cityName=(isset($input['CityName']))?$input['CityName']:'';?>
                            <?foreach ($cities as $city){?>
                            <option value="<?=$city->cdek_city_id?>"<?=($cityName==$city->cdek_city_name)?' selected':''?>><?=$city->cdek_city_name?></option>
                            <?}?>
                        </select>
                    </label>
                </div>
                <div class="col-xs-3">
                    <label><span>Улица</span><input type="text" class="form-control" placeholder="Улица" name="street" value="<?=isset($input)?$input['street']:''?>"></label>
                </div>
                <div class="col-xs-3">
                    <label><span>Дом</span><input type="text" class="form-control" placeholder="Дом" name="house" value="<?=isset($input)?$input['house']:''?>"></label>
                </div>
                <div class="col-xs-3">
                    <label><span>Квартира/офис</span><input type="text" class="form-control" placeholder="Квартира/офис" name="flat" value="<?=isset($input)?$input['flat']:''?>"></label>
                </div>
            </div>
        </div>
        <div class="cdek-group who">
            <h3>Клиент</h3>
            <div class="form-group">
                <div class="col-xs-6">
                    <label><span>Имя получателя</span><input type="text" class="form-control long" placeholder="Имя получателя" name="name" value="<?=isset($input)?$input['name']:''?>"></label>
                </div>
                <div class="col-xs-3">
                    <label><span>Телефон получателя</span><input type="text" class="form-control" placeholder="Телефон получателя" name="phone" value="<?=isset($input)?$input['phone']:''?>"></label>
                </div>
                <div class="col-xs-3">
                    <label><span>Электронная почта</span><input type="email" class="form-control" placeholder="Электронная почта" name="email" value="<?=isset($input)?$input['email']:''?>"></label>
                </div>
            </div>
        </div>
        <div class="cdek-group what">
            <h3>Погрузка</h3>
            <?if(isset($input['PACKAGES'])){
                foreach($input['PACKAGES'] as $package=>$pk){?>
                    <div class="form-group package" data-package="<?=$package?>">
                        <div class="col-xs-4">
                            <label><span>Вес</span><input type="text" class="form-control" placeholder="Вес комплекта" value="<?=$pk['weight']?>" name="PACKAGES[<?=$package?>][weight]"></label>
                        </div>
                        <div class="col-xs-1">
                            <label>Габариты:</label>
                        </div>
                        <div class="col-xs-7">
                            <label><span>Длина</span><input type="text" class="form-control" name="PACKAGES[<?=$package?>][size_a]"></label>
                            <label><span>Ширина</span><input type="text" class="form-control" name="PACKAGES[<?=$package?>][size_b]"></label>
                            <label><span>Высота</span><input type="text" class="form-control" name="PACKAGES[<?=$package?>][size_c]"></label>
                            <a class="package-del" href="#" data-package="<?=$package?>">X</a>
                        </div>
                    </div>
                    <?$item=0;
                    if(isset($pk['Items'])){
                        foreach($pk['Items'] as $itemNum=>$it){
                            $item=$itemNum;
                            ?>
                            <div class="form-group product" data-package="<?=$package?>" data-item="<?=$item?>">
                                <div class="col-xs-12">
                                    <label><input type="text" style="width: 70px" class="form-control" placeholder="Артикул товара" value="<?=$it['sku']?>" name="PACKAGES[<?=$package?>][Items][<?=$item?>][sku]"></label>
                                    <label><input type="text" style="width: 600px" class="form-control" placeholder="Наименование товара" value="<?=$it['name']?>" name="PACKAGES[<?=$package?>][Items][<?=$item?>][name]"></label>
                                    <label><input type="text" style="width: 60px" class="form-control" placeholder="Кол-во" value="<?=$it['amount']?>" name="PACKAGES[<?=$package?>][Items][<?=$item?>][amount]"></label>
                                    <label><input type="text" style="width: 60px" class="form-control" placeholder="Цена" value="<?=$it['price']?>" name="PACKAGES[<?=$package?>][Items][<?=$item?>][price]"></label>
                                    <label><input type="text" style="width: 170px" class="form-control" placeholder="Оплата при получении" value="<?=$it['payment']?>" name="PACKAGES[<?=$package?>][Items][<?=$item?>][payment]"></label>
                                    <label><input type="text" style="width: 50px" class="form-control" placeholder="Вес" value="<?=$it['weight']?>" name="PACKAGES[<?=$package?>][Items][<?=$item?>][weight]"></label>
                                </div>
                            </div>
                        <?}
                    }?>
                    <div class="form-group">
                        <div class="col-xs-4">
                            <a class="product-add" href="#" data-package="<?=$package?>" data-item="<?=($item+1)?>">Добавить товар</a>
                        </div>
                    </div>
                <?  }
            }?>
            <div class="form-group">
                <div class="col-xs-4">
                    <a class="package-add" href="#" data-package="<?=$packagenum?>">Добавить место</a>
                </div>
            </div>
        </div>
        <div class="cdek-group options">
            <h3>Настройки доставки</h3>
            <div class="form-group">
                <div class="col-xs-6">
                    <label>
                        <select name="tariff" class="form-control">
                            <option value="">Выберите тариф</option>
                            <?foreach ($tariffs as $tariff){?>
                                <option value="<?=$tariff->cdek_tariff_code?>" data-door="<?=$tariff->cdek_tariff_door?>"><?=$tariff->cdek_tariff_name?></option>
                            <?}?>
                        </select>
                    </label>
                </div>
                <div class="col-xs-6 cdek-pvz"></div>
            </div>
            <div class="form-group">
                <div class="col-xs-12 cdek-calc"></div>
            </div>
            <div class="checkbox">
                <div class="col-xs-6">
                    <label>Скан документов</label>
                </div>
            </div>
            <div class="checkbox">
                <div class="col-xs-6">
                    <label><input type="checkbox" name="SERVICES[]" value="37" checked>Осмотр вложения</label>
                </div>
            </div>
            <div class="checkbox">
                <div class="col-xs-6">
                    <label><input type="checkbox" name="SERVICES[]" value="36">Частичная доставка</label>
                </div>
            </div>
            <div class="checkbox">
                <div class="col-xs-6">
                    <label><input type="checkbox" name="SERVICES[]" value="17">Доставка в городе-получателе</label>
                </div>
            </div>
        </div>
        <div class="cdek-group">
            <div class="form-group">
                <div class="col-xs-4">
                    <input type="submit" class="btn btn-primary" name="newcdek" value="Создать">
                </div>
            </div>
        </div>
    </form>
</div>