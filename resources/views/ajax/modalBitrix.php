<table class="table table-condensed">
    <tr>
        <td colspan="2"><h2>Заказ №<?=$bitrix["ACCOUNT_NUMBER"]?></h2></td>
    </tr>
<tr>
    <td>Дата:</td>
    <td><?=$bitrix["DATE_INSERT"]?></td>
</tr>
<tr>
    <td>Сумма:</td>
    <td><?=intval($bitrix["PRICE"])?> р.</td>
</tr>
    <tr>
        <td>Клиент:</td>
        <td><?=$bitrix["USER_NAME"]?> <?=$bitrix["USER_LAST_NAME"]?></td>
    </tr>
    <tr>
        <td>Электронная почта:</td>
        <td><?=$bitrix["USER_EMAIL"]?></td>
    </tr>
    <tr>
        <td>Телефон:</td>
        <td><?=$bitrix["PHONE"]?></td>
    </tr>
    <tr>
        <td>Город:</td>
        <td><?=$bitrix["CITY"]?></td>
    </tr>
    <tr>
        <td>Статус:</td>
        <td><?=$bitrix["STATUS_NAME"]?></td>
    </tr>
    <tr>
        <td>Описание статуса:</td>
        <td><?=$bitrix["STATUS_DESCRIPTION"]?></td>
    </tr>
    <tr>
        <td>Доставка:</td>
        <td><?=$bitrix["DELIVERY_NAME"]?></td>
    </tr>
    <tr>
        <td colspan="2">Товары:</td>
    </tr>
    <?foreach($bitrix["PRODUCT"] as $product){?>
        <tr>
            <td rowspan="4"><img src="<?=$product["PREVIEW_PICTURE"]?>" height="100px"></td>
            <td><?=$product["NAME"]?></td>
        </tr>
        <tr><td>Код: <?=$product["XML_ID"]?></td></tr>
        <tr><td>Цена: <?=intval($product["PRICE"])?> р.</td></tr>
        <tr><td>Количество: <?=intval($product["QUANTITY"])?> шт.</td></tr>
    <?}?>
<tr>
    <td>Документ:</td>
    <td>
        <?foreach($docs as $doc){?>
            <a class="showALLbyRDS" href="#allAboutRDS" data-rds="<?=$doc->number?>"> <?=$doc->number?></a>
        <?}?>
    </td>
</tr>
</table>
<tbody class="table table-condensed" id="allAboutRDS"></tbody>
