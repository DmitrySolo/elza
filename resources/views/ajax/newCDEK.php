<div class="container">
    <form class="form-horizontal cdek-form" method="post" action="/addcdek">
        <div class="cdek-group">
            <div class="form-group">
                <div class="col-xs-12">
                    <label><input type="text" class="form-control" placeholder="Номер документа" name="Number" value="<?=isset($input)?$input['Number']:''?>"></label>
                </div>
            </div>
        </div>
        <input type="hidden" name="deliveryCost" value="<?=isset($input)?$input['deliveryCost']:''?>">
        <div class="cdek-group where">
            <h3>Местоположение</h3>
            <div class="form-group">
                <div class="col-xs-3">
                    <label>
                        <select name="city" class="form-control cdek-city-select">
                            <option value="">Выберите город</option>
                            <?$cityName=(isset($input['CityName']))?$input['CityName']:'';?>
                            <option value="432"<?=($cityName=='Астрахань')?' selected':''?>>Астрахань</option>
                            <option value="337"<?=($cityName=='Белгород')?' selected':''?>>Белгород</option>
                            <option value="220"<?=($cityName=='Брянск')?' selected':''?>>Брянск</option>
                            <option value="94"<?=($cityName=='Владимир')?' selected':''?>>Владимир</option>
                            <option value="426"<?=($cityName=='Волгоград')?' selected':''?>>Волгоград</option>
                            <option value="427"<?=($cityName=='Волжский')?' selected':''?>>Волжский</option>
                            <option value="1230"<?=($cityName=='Губкин')?' selected':''?>>Губкин</option>
                            <option value="863"<?=($cityName=='Дзержинск')?' selected':''?>>Дзержинск</option>
                            <option value="250"<?=($cityName=='Екатеринбург')?' selected':''?>>Екатеринбург</option>
                            <option value="362"<?=($cityName=='Елец')?' selected':''?>>Елец</option>
                            <option value="184"<?=($cityName=='Зеленоград')?' selected':''?>>Зеленоград</option>
                            <option value="281"<?=($cityName=='Иркутск')?' selected':''?>>Иркутск</option>
                            <option value="424"<?=($cityName=='Казань')?' selected':''?>>Казань</option>
                            <option value="142"<?=($cityName=='Калуга')?' selected':''?>>Калуга</option>
                            <option value="967"<?=($cityName=='Коломна')?' selected':''?>>Коломна</option>
                            <option value="165"<?=($cityName=='Кострома')?' selected':''?>>Кострома</option>
                            <option value="521"<?=($cityName=='Красногорск')?' selected':''?>>Красногорск</option>
                            <option value="435"<?=($cityName=='Краснодар')?' selected':''?>>Краснодар</option>
                            <option value="699"<?=($cityName=='Курск')?' selected':''?>>Курск</option>
                            <option value="320"<?=($cityName=='Липецк')?' selected':''?>>Липецк</option>
                            <option value="357"<?=($cityName=='Лиски')?' selected':''?>>Лиски</option>
                            <option value="44"<?=($cityName=='Москва')?' selected':''?>>Москва</option>
                            <option value="265"<?=($cityName=='Мурманск')?' selected':''?>>Мурманск</option>
                            <option value="920"<?=($cityName=='Мытищи')?' selected':''?>>Мытищи</option>
                            <option value="414"<?=($cityName=='Нижний Новгород')?' selected':''?>>Нижний Новгород</option>
                            <option value="436"<?=($cityName=='Новороссийск')?' selected':''?>>Новороссийск</option>
                            <option value="1276"<?=($cityName=='Обнинск')?' selected':''?>>Обнинск</option>
                            <option value="268"<?=($cityName=='Омск')?' selected':''?>>Омск</option>
                            <option value="149"<?=($cityName=='Орел')?' selected':''?>>Орел</option>
                            <option value="261"<?=($cityName=='Оренбург')?' selected':''?>>Оренбург</option>
                            <option value="1309"<?=($cityName=='Острогожск')?' selected':''?>>Острогожск</option>
                            <option value="504"<?=($cityName=='Пенза')?' selected':''?>>Пенза</option>
                            <option value="248"<?=($cityName=='Пермь')?' selected':''?>>Пермь</option>
                            <option value="450"<?=($cityName=='Петрозаводск')?' selected':''?>>Петрозаводск</option>
                            <option value="393"<?=($cityName=='Псков')?' selected':''?>>Псков</option>
                            <option value="537"<?=($cityName=='Россошь')?' selected':''?>>Россошь</option>
                            <option value="438"<?=($cityName=='Ростов-на-Дону')?' selected':''?>>Ростов-на-Дону</option>
                            <option value="159"<?=($cityName=='Рязань')?' selected':''?>>Рязань</option>
                            <option value="430"<?=($cityName=='Самара')?' selected':''?>>Самара</option>
                            <option value="137"<?=($cityName=='Санкт-Петербург')?' selected':''?>>Санкт-Петербург</option>
                            <option value="428"<?=($cityName=='Саратов')?' selected':''?>>Саратов</option>
                            <option value="15256"<?=($cityName=='Севастополь')?' selected':''?>>Севастополь</option>
                            <option value="395"<?=($cityName=='Смоленск')?' selected':''?>>Смоленск</option>
                            <option value="437"<?=($cityName=='Сочи')?' selected':''?>>Сочи</option>
                            <option value="132"<?=($cityName=='Старый Оскол')?' selected':''?>>Старый Оскол</option>
                            <option value="298"<?=($cityName=='Тамбов')?' selected':''?>>Тамбов</option>
                            <option value="245"<?=($cityName=='Тверь')?' selected':''?>>Тверь</option>
                            <option value="431"<?=($cityName=='Тольятти')?' selected':''?>>Тольятти</option>
                            <option value="269"<?=($cityName=='Томск')?' selected':''?>>Томск</option>
                            <option value="150"<?=($cityName=='Тула')?' selected':''?>>Тула</option>
                            <option value="256"<?=($cityName=='Уфа')?' selected':''?>>Уфа</option>
                            <option value="45"<?=($cityName=='Химки')?' selected':''?>>Химки</option>
                            <option value="259"<?=($cityName=='Челябинск')?' selected':''?>>Челябинск</option>
                            <option value="146"<?=($cityName=='Ярославль')?' selected':''?>>Ярославль</option>
                        </select>
                    </label>
                </div>
                <div class="col-xs-3">
                    <label><input type="text" class="form-control" placeholder="Улица" name="street" value="<?=isset($input)?$input['street']:''?>"></label>
                </div>
                <div class="col-xs-3">
                    <label><input type="text" class="form-control" placeholder="Дом" name="house" value="<?=isset($input)?$input['house']:''?>"></label>
                </div>
                <div class="col-xs-3">
                    <label><input type="text" class="form-control" placeholder="Квартира/офис" name="flat" value="<?=isset($input)?$input['flat']:''?>"></label>
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
                            <option value="63">Магистральный супер-экспресс склад-склад</option>
                            <option value="136">Посылка склад-склад</option>
                        </select>
                    </label>
                </div>
                <div class="col-xs-6 cdek-pvz"></div>
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
        </div>
        <div class="cdek-group who">
            <h3>Клиент</h3>
            <div class="form-group">
                <div class="col-xs-6">
                    <label><input type="text" class="form-control long" placeholder="Имя получателя" name="name" value="<?=isset($input)?$input['name']:''?>"></label>
                </div>
                <div class="col-xs-3">
                    <label><input type="text" class="form-control" placeholder="Телефон получателя" name="phone" value="<?=isset($input)?$input['phone']:''?>"></label>
                </div>
                <div class="col-xs-3">
                    <label><input type="email" class="form-control" placeholder="Электронная почта" name="email" value="<?=isset($input)?$input['email']:''?>"></label>
                </div>
            </div>
        </div>
        <div class="cdek-group what">
            <h3>Погрузка</h3>
            <?if(isset($input['PACKAGES'])){
                foreach($input['PACKAGES'] as $package=>$pk){?>
                    <div class="form-group package" data-package="<?=$package?>">
                        <div class="col-xs-4">
                            <label><input type="text" class="form-control" placeholder="Вес комплекта" value="<?=$pk['weight']?>" name="PACKAGES[<?=$package?>][weight]"></label>
                        </div>
                        <div class="col-xs-8">
                            <label>Габариты:<input type="text" class="form-control" value="<?=$pk['size_a']?>" name="PACKAGES[<?=$package?>][size_a]"></label>
                            <label><input type="text" class="form-control" value="<?=$pk['size_b']?>" name="PACKAGES[<?=$package?>][size_b]"></label>
                            <label><input type="text" class="form-control" value="<?=$pk['size_c']?>" name="PACKAGES[<?=$package?>][size_c]"></label>
                        </div>
                    </div>
                    <?$item=0;
                    if(isset($pk['Items'])){
                        foreach($pk['Items'] as $itemNum=>$it){
                            $item=$itemNum;
                            ?>
                            <div class="form-group product" data-package="<?=$package?>" data-item="<?=$item?>">
                                <div class="col-xs-12">
                                    <label><input type="text" style="width: 70px;" class="form-control" placeholder="Артикул товара" value="<?=$it['sku']?>" name="PACKAGES[<?=$package?>][Items][<?=$item?>][sku]"></label>
                                    <label><input type="text" style="width: 600px;" class="form-control" placeholder="Наименование товара" value="<?=$it['name']?>" name="PACKAGES[<?=$package?>][Items][<?=$item?>][name]"></label>
                                    <label><input type="text" style="width: 60px;" class="form-control" placeholder="Кол-во" value="<?=$it['amount']?>" name="PACKAGES[<?=$package?>][Items][<?=$item?>][amount]"></label>
                                    <label><input type="text" style="width: 60px;" class="form-control" placeholder="Цена" value="<?=$it['price']?>" name="PACKAGES[<?=$package?>][Items][<?=$item?>][price]"></label>
                                    <label><input type="text" style="width: 170px;" class="form-control" placeholder="Оплата при получении" value="<?=$it['payment']?>" name="PACKAGES[<?=$package?>][Items][<?=$item?>][payment]"></label>
                                    <label><input type="text" style="width: 50px;" class="form-control" placeholder="Вес" value="<?=$it['weight']?>" name="PACKAGES[<?=$package?>][Items][<?=$item?>][weight]"></label>
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
                    <a class="package-add" href="#" data-package="<?=$packagenum?>">Добавить комплект</a>
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