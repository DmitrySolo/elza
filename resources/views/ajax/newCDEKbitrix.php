<?php
echo "Доп. информация:<br>";
if(isset($data["DELIVERY_METHOD"])&&!empty($data["DELIVERY_METHOD"])){
    echo "Способ доставки: ".$data["DELIVERY_METHOD"].'<br>';
}
if(isset($data["DELIVERY_ADDRESS"])&&!empty($data["DELIVERY_ADDRESS"])){
    echo "Адрес доставки: ".$data["DELIVERY_ADDRESS"];
}
if(!isset($data)||empty($data)){
    echo 'НЕТ ИНФОРМАЦИИ';
}