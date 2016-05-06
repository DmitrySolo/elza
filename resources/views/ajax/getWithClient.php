<?php if(isset($data['pp']['DeliverySumTotal'])){
    $delSum=$data['pp']['DeliverySumTotal'];
    if($data['pp']['Status']['Code']==5){
        $data['goods']['total_price']=0;
        $data['goods']['total_base_price']=0;
        $data['goods']['delivery_cost']=0;
    }else{

    }
}else{
    $delSum=400;
}
;?>
<tr><td>
        <button type="button" class="btn btn-warning"  data-toggle="collapse" href="#collapseAppeal" aria-expanded="false" aria-controls="collapseExample">Регистрация жалобы</button>

        <?php if($data['client']['address']):?><button type="button" class="btn btn-info">Написать письмо</button><?endif?>
        <button type="button" class="btn btn-info">Отслеживать</button>
        <button type="button" class="btn btn-warning">Регистрация притензии к ТК</button>
    </td></tr>
    <tr>
        <td>
            <div class="collapse" id="collapseAppeal">
                <div id="AppealReg" class="well">
                   <input id="sendAttacheAppeal" type="checkbox" value="1" checked><label for="sendAttacheAppeal">Отправить письмо формой претензии на
                        <?php if($data['client']['address'])echo "<input type='text' value=".$data['client']['address'].'>'; else echo "<input type='text'>"?>
                    </label><br>
                    <span>Описание жалобы</span><br>
                    <input  id="goodsProblemDoc" name="docnum" type="hidden" value="<?php echo $data['doc']['number']?>">
                    <textarea id="goodsproblemDescription" name="description"></textarea>
                   <div>
                    <label>Выставите Статус:</label>
                    <select name="step">
                        <option value="Ожидание письма от клиента">Ожидание письма от клиента</option>
                        <option value="Ожидание ответа от товароведа">Ожидание ответа от товароведа</option>
                    </select>
                   </div>
                    <div>
                        <label>Выставите Время обещанное клиенту:</label>
                        <select name="setTime">
                            <option value="30">30 минут</option>
                            <option value="60">1 час</option>
                            <option value="120">2 час</option>
                            <option value="180">3 час</option>
                            <option value="240">4 час</option>
                            <option value="300">5 часов</option>
                            <option value="360">6 часов</option>
                            <option value="480">В течении 24 часов</option>
                            <option value="960">В течении 48 часов</option>
                            <option value="1520">В течении 3х рабочих дней</option>
                            <option value="1920">В течении 4 рабочих дней</option>
                            <option value="2400">В течении 5 рабочих дней</option>
                        </select>
                    </div>
                    <button class="btn btn-default" id="registerGoodsProblem1">OK</button>
                </div>
                <div id="resultsAppealReg"></div>
            </div>
        </td>
    </tr>
<tr><td colspan=""><h2 style="width: 100%; text-align: center"><?php echo $data['doc']['number'];?>
        от <?php
        $date = DateTime::createFromFormat('Y-m-d', $data['doc']['date']);
        echo $date->format('d-m-Y');

        ;?> </h2></h1></td>
<td>
    <h3>Общий Итог:<?php $style=($data['goods']['total_price']
            -$data['goods']['total_base_price']- $delSum
            +$data['goods']['delivery_cost']<0)?"color:red":"color:green"; ?>
        <span style="<?php echo $style;?>"><?php echo $data['goods']['total_price']
                -$data['goods']['total_base_price']- $delSum
                +$data['goods']['delivery_cost'];?> Р.</span>
    </h3>
</td>
</tr>
<tr><td class="info" colspan="4" align="center"><h5>Клиент:</h5></td></tr>
<tr>
    <td>ФИО:</td>
    <td><?php echo $data['client']['name']?></td>
</tr>
<tr>
    <td>Город:</td>
    <td><?php echo $data['client']['city']?></td>
</tr>
<tr>
    <td>Почта:</td>
    <td><?php echo $data['client']['address']?></td>
</tr>
<tr>
    <td>Телефон:</td>
    <td><?php echo $data['client']['phone']?></td>
</tr>
<tr><td class="info" colspan="4" align="center"><h5>Товары в заказе:</h5></td></tr>
<?php foreach($data['goods']['products'] as $product):?>
<tr>
    <td <?php if(isset($product['real_quantity']) && $product['real_quantity']!=$product['rds_quantity']) echo "style='color:red;font-weight:900'";?>>
        <a href="http://www.santehsmart.ru/catalog/?q=<? echo $product['sku']?>&s=НАЙТИ" target="_blank">
            <em><? echo $product['sku'].' '.$product['product_name'].'</em></a> '
            .' '.$product['rds_quantity'].' шт'.' (Получено : '.$product['real_quantity'].')';?>
    </td>
    <td style="color: steelblue"><b><? echo $product['base'].' р.'?></b></td>
    <td style="color: #28a4c9"><b><? echo $product['price'].' р.'?></b></td>
    <td style="color: #28a4c9"><b><? echo $itog=($product['price']- $product['base']>0)?"<span style='color:green'>".($product['price']- $product['base'])."</span>":
                "<span style='color:red'>".($product['price']- $product['base'])."</span>";?></b></td>
</tr>
<?php endforeach; ?>
<tr>
    <td>Клиент оплатил доставку стоимостью:</td>
    <td><?php echo $data['goods']['delivery_cost'];?></td>
</tr>
<tr>
    <td>Итого сумма сделки:</td>
    <td><?php echo $data['goods']['total_price'];?></td>
</tr>
<?php if(!isset($data['pp']['ERROR_CDEK'])):?>
<tr>
    <td class="info" colspan="4" align="center"><h5>СДЭК</h5></td>
</tr>
<tr>
    <td>Статус:</td>
    <?php if($data['pp']['Status']['Description']){};
            switch($data['pp']['Status']['Code']){
                case 4: $label="label label-success";
                    break;
                case 5: $label="label label-danger";
                    break;
                default:  $label="label label-info";
            }

    ?>
    <td><span  class="<?php echo $label; ?>"><?php echo $data['pp']['Status']['Description'];?></span></td>
</tr>
<tr>
    <td>Город:</td>
    <td><?php echo $data['pp']['Status']['CityName'];?></td>
</tr>
<tr>
    <td  >Доставка обошлась:</td>
    <td><?php echo $data['pp']['DeliverySum'];
                $delItog=$data['goods']['delivery_cost']-$data['pp']['DeliverySum'];
                $class=($delItog>0)?"style='color:green'":"style='color:red'";
                echo "<span $class > ($delItog)</span>";
        ?>

    </td>
</tr>
<tr>
    <td colspan="4" align="center">Дополнительные услуги обошлись:</td>
    <?php foreach( $data['pp']['services'] as $service_name=>$price):?>
<tr><td> <?php echo $service_name ?> </td>
    <td><?php echo $price ?></td></tr>
    <? endforeach;?>
</tr>
<?php if(!empty($data['pp']['Reason']['Description'])):?>
    <tr>
        <td>
          Причина возврата:
        </td>
        <td ><div class="alert alert-danger">
            <?php echo $data['pp']['Reason']['Description'];?></div>
        </td>
    </tr>
<?endif;?>
    <?else:?>
    <tr>Товар не доставлялся СДЭКОМ</tr>
<?endif;?>
<?php
