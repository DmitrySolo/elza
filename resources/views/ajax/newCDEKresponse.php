<div class="container">
    <?foreach($response['orders'] as $order){?>
    <div class="row">
        <div class="col-lg-12">
            <b>Номер заказа: </b><span><?=$order['Number']?></span>
        </div>
    </div>
    <?php if(!empty($order['deliveryCost'])){?>
    <div class="row">
        <div class="col-lg-12">
            <b>Наличные курьеру: </b><span><?=$order['deliveryCost']?>р.</span>
        </div>
    </div>
    <?php }?>
    <div class="row">
        <div class="col-lg-12">
            <b>Клиент: </b><span><?=$order['name']?></span>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <b>Телефон: </b><span><?=$order['phone']?></span>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <b>Эл. почта: </b><span><?=$order['email']?></span>
        </div>
    </div>
    <?php
        if(isset($response['response']['Order'])) {
            foreach ($response['response']['Order'] as $resp) {
                if ($resp['@attributes']['Number'] == $order['Number']) {
                    if(isset($resp['@attributes']['DispatchNumber'])){?>
                        <div class="row">
                            <div class="col-lg-12">
                                <b>Номер штрих-кода: </b><span><?=$resp['@attributes']['DispatchNumber']?></span>
                            </div>
                        </div>
                    <?php }elseif(isset($resp['@attributes']['ErrorCode'])){
                        ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <b>ОШИБКА: </b><span><?=$resp['@attributes']['Msg']?> (<?=$resp['@attributes']['ErrorCode']?>)</span>
                            </div>
                        </div>
                    <?php }
                    break;
                }
            }
        }
        ?>
            <iframe src="//<?=$_SERVER['HTTP_HOST']?>/print/<?=$order['Number']?>/print.pdf" width="100%" height="600px"></iframe>
        <?php
    ?>
    <?}?>
</div>