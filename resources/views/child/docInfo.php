<h3>Документ</h3>
<?if(!isset($data['ERROR_DOC'])){?>
<dl>
    <dt>Номер документа:</dt><dd><?=$data['Number']?></dd>
    <dt>Дата документа:</dt><dd><?=$data['Date']?></dd>
    <dt>Клиент:</dt><dd><?=$data['ClientName']?></dd>
    <dt>Телефон/Адрес:</dt><dd><?=$data['ClientPhone']?> <?=$data['ClientAddress']?></dd>
    <dt>Товары:</dt>
    <?foreach($data['Products'] as $product){?>
        <dd><?=$product['sku']?> <?=$product['name']?> <?=$product['price']?>р.</dd>
    <?}?>
</dl>
<?}else{?>
    <dt><?=$data['ERROR_DOC']?></dt>
<?}?>
<h3>СДЭК</h3>
<?if(!isset($data['ERROR_CDEK'])){?>
<dl>
    <dt>Статус:</dt><dd><?=$data['Status']['Description']?>, <?=$data['Status']['CityName']?></dd>
    <dt>Стоимость:</dt><dd><?=$data['DeliverySumTotal']?>р.</dd>
</dl>
<?}else{?>
    <dt><?=$data['ERROR_CDEK']?></dt>
<?}?>
<pre>
<?print_r($data['bitrix'])?>
</pre>
