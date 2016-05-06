<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Nathanmac\Utilities\Parser\Facades\Parser;

class CDEKController extends Controller
{
    private $home="http://gw.edostavka.ru:11443/";
    //private $home="http://lk.cdek.ru:11443/";
    private $account="b62fd71f538cd2de349dc5b033ec1bae";
    private $secure_password="f3e259e1d137f7f2c8fe6bcfc4109095";
    static $masks=array(
            'dopUslugi'=>array(
                2=> "СТРАХОВАНИЕ",
                3=> "ДОСТАВКА В ВЫХОДНОЙ ДЕНЬ",
                4=> "ОТПРАВКА В ВЫХОДНОЙ ДЕНЬ",
                5=> "ТЯЖЕЛЫЙ ГРУЗ",
                6=> "НЕГАБАРИТНЫЙ ГРУЗ",
                7=> "ОПАСНЫЙ ГРУЗ",
                8=> "ОЖИДАНИЕ БОЛЕЕ 15 МИН. У ОТПРАВИТЕЛЯ",
                9=> "ОЖИДАНИЕ БОЛЕЕ 15 МИН. У ПОЛУЧАТЕЛЯ",
                10=> "ХРАНЕНИЕ НА СКЛАДЕ",
                13=> "ПРОЧЕЕ",
                14=> "УДАЛЕННЫЙ РАЙОН",
                15=> "ПОВТОРНАЯ ПОЕЗДКА",
                16=> "ЗАБОР В ГОРОДЕ ОТПРАВИТЕЛЕ",
                17=> "ДОСТАВКА В ГОРОДЕ ПОЛУЧАТЕЛЕ",
                20=> "ПЕНЯ",
                23=> "ОБРЕШЕТКА ГРУЗА",
                24=> "УПАКОВКА 1",
                25=> "УПАКОВКА 2",
                26=> "АРЕНДА КУРЬЕРА",
                27=> "СМС УВЕДОМЛЕНИЕ",
                29=> "СОРТИРОВКА",
                30=> "ПРИМЕРКА НА ДОМУ",
                31=> "ДОСТАВКА ЛИЧНО В РУКИ",
                32=> "СКАН ДОКУМЕНТОВ",
                33=> "ПОДЪЕМ НА ЭТАЖ РУЧНОЙ",
                34=> "ПОДЪЕМ НА ЭТАЖ ЛИФТОМ",
                35=> "ПРОЗВОН",
                36=> "ЧАСТИЧНАЯ ДОСТАВКА",
                37=> "ОСМОТР ВЛОЖЕНИЯ",
                38=> "ОТПРАВКА В ВЕЧЕРНЕЕ ВРЕМЯ",
                39=> "ДОСТАВКА В ВЕЧЕРНЕЕ ВРЕМЯ",
                40=> "ТЕПЛОВОЙ РЕЖИМ",
                41=> "ВОЗВРАТ ДОКУМЕНТОВ",
                42 => "АГЕНТСКОЕ ВОЗНАГРАЖДЕНИЕ"),
    );

    public function test(){
        //$ch=curl_init("http://gw.edostavka.ru:11443/new_orders.php");
        //$ch=curl_init("http://gw.edostavka.ru:11443/status_report_h.php");
        $ch=curl_init("http://gw.edostavka.ru:11443/info_report.php");
        $date=date("Y-m-d");
        /*$account="f6c3b39e4a505d6797b4dc01c6fe0279";
        $secure_password="fba640bcdfe840354b69ad2ef222fee5";*/
        $account="b62fd71f538cd2de349dc5b033ec1bae";
        $secure_password="f3e259e1d137f7f2c8fe6bcfc4109095";
        $secure=md5("$date&$secure_password");
        /*$post1=array(
            'xml_request'=>"<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<DeliveryRequest Number=\"237\" Date=\"$date\" Account=\"$account\" Secure=\"$secure\" OrderCount=\"1\">
    <Order Number=\"5408\"
        DeliveryRecipientCost=\"0\"
        SendCityCode=\"270\"
        RecCityCode=\"44\"
        RecipientName=\"Васина Юлия Александровна\"
        Phone=\"7810999\"
        Comment=\"Офис группы компаний Ланит. При приезде позвонить на мобильный телефон.\"
        TariffTypeCode=\"5\"
        RecientCurrency=\"RUB\"
        ItemsCurrency=\"RUB\">
        <Address PvzCode=\"MSK2\" Street=\"Боровая\" House=\"д. 7, стр. 2\" Flat=\"оф.10\" />
        <Package Number=\"1\" BarCode=\"101\" Weight=\"630\">
           <Item WareKey=\"25000050368\" Cost=\"49\" Payment=\"49\" Weight=\"68\" Amount=\"1\" Comment=\"Дидактические игры-занятия в ДОУ 	 	   Ст.возраст Вып. 1\"/>
           <Item WareKey=\"25000348563\" Cost=\"79\" Payment=\"79\" Weight=\"95\" Amount=\"1\" Comment=\"ДошкВоспитаниеИРазвитие(Айрис-Пр.)	(о) 	   Сюжетно-роле\"/>
           <Item WareKey=\"25000373314\" Cost=\"79\" Payment=\"79\" Weight=\"135\" Amount=\"1\" Comment=\"ДошкВоспитаниеИРазвитие(Айрис-Пр.)	(о) 	   Метод.работа\"/>
           <Item WareKey=\"25000390270\" Cost=\"79\" Payment=\"79\" Weight=\"219\" Amount=\"1\" Comment=\"Дошкольники_УчимРазвиваемВоспитываем 	   Родительские \"/>
        </Package>
        <AddService ServiceCode=\"30\"></AddService>
        <Schedule>
           <Attempt ID=\"1\" Date=\"$date\" TimeBeg=\"09:00:00\" TimeEnd=\"13:00:00\" />
           <Attempt ID=\"2\" Date=\"$date\" TimeBeg=\"14:00:00\" TimeEnd=\"18:00:00\" RecipientName=\"Прокопьев 	Анатолий Сергеевич\" />
        </Schedule>
    </Order>
</DeliveryRequest>"
        );*/
        /*$post=array(
            'xml_request'=>"<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<StatusReport Date=\"$date\" Account=\"$account\" Secure=\"$secure\" ShowHistory=\"1\" ShowReturnOrder=\"1\" ShowReturnOrderHistory=\"1\">".
//"<ChangePeriod DateFirst=\"2015-12-01\" DateLast=\"2016-02-28\"/><Order ReturnDispatchNumber=\"1022124947\" />".
"    <Order Number=\"РДС-002863\" />
</StatusReport>"
        );
        $post2=array(
            'xml_request'=>"<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<InfoRequest Date=\"$date\" Account=\"$account\" Secure=\"$secure\">
    <Order DispatchNumber=\"1023054988\" />
</InfoRequest>"
        );
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $post2,
        ));
        $xml=curl_exec($ch);
        $data=Parser::xml($xml);
        $data['xml']=$xml;
        dd($data);*/
        dd($this->basicCDEK('РДС-002863'));
    }

    public function getNumberByReturn($number,$dateFirst,$dateLast){
        $arInfo=array(
            array(
                'OBJECT'=>'ChangePeriod',
                'ATTRIBUTES'=>['DateFirst'=>$dateFirst,'DateLast'=>$dateLast]
            )
        );
        $info=$this->_getCDEK($this->_status_report($arInfo,['ShowReturnOrder'=>1]));
        foreach($info['Order'] as $order){
            if(isset($order['@attributes']['ReturnDispatchNumber']))
            if($order['@attributes']['ReturnDispatchNumber']==$number) return $order['@attributes']['Number'];
        }
        return "";
    }
    public function getNumberByDispatch($number){
        $arInfo=array(
            array(
                'OBJECT'=>'Order',
                'ATTRIBUTES'=>['DispatchNumber'=>$number]
            )
        );
        $info=$this->_getCDEK($this->_status_report($arInfo));
        if(isset($info['Order']['@attributes']['Number'])) {
            return $info['Order']['@attributes']['Number'];
        }
        return "";
    }
    public function getListInfo($arOrders){
        $arResult=array();
        $arReport=array();
        foreach($arOrders as $number){
            $arReport[]=array(
                'OBJECT'=>'Order',
                'ATTRIBUTES'=>['Number'=>$number]
            );
        }
        $report=$this->_getCDEK($this->_status_report($arReport));
        if(isset($report['Order'])) {
            $arInfo = array();
            if (isset($report['Order']['@attributes'])) $report['Order'] = array($report['Order']);
            foreach ($report['Order'] as $order) {
                if (isset($order['@attributes']['Number'])) {
                    $arResult[$order['@attributes']['Number']] = array(
                        'Number' => $order['@attributes']['Number'],
                        'Status' => $order['Status']['@attributes'],
                        'Reason' => $order['Reason']['@attributes']
                    );
                    if(isset($order['Package'])){
                        $arResult[$order['@attributes']['Number']]['Package']=$order['Package'];
                    }

                    $arInfo[] = array(
                        'OBJECT' => 'Order',
                        'ATTRIBUTES' => ['DispatchNumber' => $order['@attributes']['DispatchNumber']]
                    );
                }
            }
            $info = $this->_getCDEK($this->_info_report($arInfo));
            if (isset($info['Order']['@attributes'])) $info['Order'] = array($info['Order']);
            foreach ($info['Order'] as $order) {
                $sum = intval($order['@attributes']['DeliverySum']);
                foreach ($order['AddedService'] as $service) {
                    $sum += intval($service['@attributes']['Sum']);
                }
                $arResult[$order['@attributes']['Number']]['DeliverySum'] = $order['@attributes']['DeliverySum'];
                $arResult[$order['@attributes']['Number']]['AddedService'] = $order['AddedService'];
                $arResult[$order['@attributes']['Number']]['DeliverySumTotal'] = $sum;
            }
        }
        return $arResult;
    }
    public function newDelivery($orders){
        /*$orders=[
            [
                'Number'=>'РДС-3894945',
                'phone'=>'8-960-123-45-67',
                'email'=>'sergk393@inbox.ru',
                'name'=>'Юзер Альбертович',
                'city'=>44,
                'flat'=>1,
                'house'=>2,
                'street'=>'Южнопользовательская',
                'tariff'=>136,
                'pvz'=>'MSK3',
                'PACKAGES'=>[
                    [
                        'weight'=>500,
                        'size_a'=>30,
                        'size_b'=>50,
                        'size_c'=>60,
                        'Items'=>[
                            [
                                'amount'=>1,
                                'sku'=>11245,
                                'name'=>'полотенцесушитель',
                                'price'=>750,
                                'payment'=>750,
                                'weight'=>400
                            ],
                            [
                                'amount'=>1,
                                'sku'=>23224,
                                'name'=>'смеситель',
                                'price'=>850,
                                'payment'=>850,
                                'weight'=>100
                            ]
                        ]
                    ]
                ],
                'SERVICES'=>[32,37]
            ]
        ];*/
        $response=$this->_getCDEK($this->_delivery_request($orders));
        return ['response'=>$response,'orders'=>$orders];
    }
    public function basicCDEK($number){
        $arResult=['Number'=>$number];
        $arReport=array(
            array(
                'OBJECT'=>'Order',
                'ATTRIBUTES'=>['Number'=>$number]
            )
        );
        $report=$this->_getCDEK($this->_status_report($arReport));
        //dd($report);
        if(isset($report['@attributes']['ErrorCode'])){
            $arr['Status']['Description']='';
            $arr['Status']['CityName']='';
            $arr['DeliverySumTotal']='';
            $arr['ERROR_CDEK']="Накладная СДЭК $number не найдена: ".$report['@attributes']['ErrorCode'];
            return $arr;
        }
        $order=$report['Order'];
        if(!isset($order['@attributes'])){
            $order=end($order);
        }
        $arResult['DispatchNumber']=$order['@attributes']['DispatchNumber'];
        $arResult['Status']=$order['Status']['@attributes'];
        $arResult['Reason']=$order['Reason']['@attributes'];
        if(isset($order['Package'])){
            $arResult['Package']=$order['Package'];
        }

        $arInfo=array(
            array(
                'OBJECT'=>'Order',
                'ATTRIBUTES'=>['DispatchNumber'=>$arResult['DispatchNumber']]
            )
        );
        $info=$this->_getCDEK($this->_info_report($arInfo));
        $sum=intval($info['Order']['@attributes']['DeliverySum']);
        foreach($info['Order']['AddedService'] as $service){
            $sum+=intval($service['@attributes']['Sum']);
        }
        $arResult['DeliverySum']=$info['Order']['@attributes']['DeliverySum'];
        $arResult['AddedService']=$info['Order']['AddedService'];
        $arResult['DeliverySumTotal']=$sum;
        //return $arResult;
        return $arResult;
    }

    public function getPVZ($cityID){
        $ch=curl_init($this->home."pvzlist.php?cityid=$cityID");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $xml=curl_exec($ch);
        $data=Parser::xml($xml);
        //dd($data);
        $arResult=array();
        if(isset($data['Pvz'])){
            if (isset($data['Pvz']['@attributes'])) $data['Pvz'] = array($data['Pvz']);
            foreach($data['Pvz'] as $pvz){
                $arPVZ=[
                    'Code'=>$pvz['@attributes']['Code'],
                    'Name'=>$pvz['@attributes']['Name'].' ('.$pvz['@attributes']['Address'].')',
                    'coordX'=>$pvz['@attributes']['coordX'],
                    'coordY'=>$pvz['@attributes']['coordY']
                ];
                if(isset($pvz['WeightLimit']['@attributes']['WeightMin']))
                    $arPVZ['WeightMin']=intval($pvz['WeightLimit']['@attributes']['WeightMin']);
                else $arPVZ['WeightMin']=0;
                if(isset($pvz['WeightLimit']['@attributes']['WeightMax']))
                    $arPVZ['WeightMax']=intval($pvz['WeightLimit']['@attributes']['WeightMax']);
                else $arPVZ['WeightMax']=0;
                $arResult[]=$arPVZ;
            }
        }
        return $arResult;
    }

    private function _getCDEK($arParams){
        $ch=curl_init($this->home.$arParams['PATH']);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $arParams['POST'],
        ));
        $xml=curl_exec($ch);
        $data=Parser::xml($xml);
        return $data;
    }

    private function _info_report($arParams){
        $arTitle=array(
            'TITLE'=>"InfoRequest"
        );
        return array(
            'PATH' => "info_report.php",
            'POST' => array('xml_request'=>$this->_simple_xml($arTitle,$arParams))
        );
    }

    private function _status_report($arParams,$attributes=array()){
        $arTitle=array(
            'TITLE'=>"StatusReport",
            'ATTRIBUTES'=>$attributes//['ShowHistory'=>1,'ShowReturnOrder'=>1,'ShowReturnOrderHistory'=>1]
        );
        return array(
            'PATH' => "status_report_h.php",
            'POST' => array('xml_request'=>$this->_simple_xml($arTitle,$arParams))
        );
    }

    private function _delivery_request($orders,$attributes=array()){
        $attributes['orderCount']=count($orders);
        $date=date("Y-m-d");

        $account="f6c3b39e4a505d6797b4dc01c6fe0279";
        $secure_password="fba640bcdfe840354b69ad2ef222fee5";
        $secure=md5("$date&$secure_password");
        $attributes['Date']=$date;
        $attributes['Account']=$account;
        $attributes['Secure']=$secure;

        $attributes['Number']='shop_'.date("Ymd").'_00000000'.rand(11,99);

        $arParams=array();
        foreach($orders as $order){
            $attributes['Number']=$order['Number'];

            $arOrder=[
                'OBJECT'=>'Order',
                'ATTRIBUTES'=>[
                    'DateInvoice'=>$date,
                    'Number'=>$order['Number'],
                    'Phone'=>$order['phone'],
                    'RecipientEmail'=>$order['email'],
                    'RecipientName'=>$order['name'],
                    'SellerName'=>'СантехСмарт',
                    'SellerAddress'=>'Донбасская, 21, 1',
                    'ShipperName'=>'СантехСмарт',
                    'ShipperAddress'=>'Донбасская, 21, 1',
                    'SendCityCode'=>506,//Воронеж
                    'RecCityCode'=>$order['city'],//44,Москва
                    'TariffTypeCode'=>$order['tariff']
                ],
                'INNER'=>[
                    [
                        'OBJECT'=>'Address',
                        'ATTRIBUTES'=>[
                            'Flat'=>$order['flat'],
                            'House'=>$order['house'],
                            'Street'=>$order['street'],
                            'PvzCode'=>$order['pvz']
                        ]
                    ]
                ]
            ];
            foreach($order['PACKAGES'] as $pack_num=>$package){
                $arPackage=[
                    'OBJECT'=>'Package',
                    'ATTRIBUTES'=>[
                        'Number'=>$pack_num,
                        'BarCode'=>$pack_num,
                        'Weight'=>$package['weight'],
                        'SizeA'=>$package['size_a'],
                        'SizeB'=>$package['size_b'],
                        'SizeC'=>$package['size_c']
                    ]
                ];
                $products=array();
                foreach($package['Items'] as $it_num=>$item){
                    $products[]=[
                        'OBJECT'=>'Item',
                        'ATTRIBUTES'=>[
                            'Amount'=>$item['amount'],
                            'Comment'=>$item['name'],
                            'Cost'=>$item['price'],
                            'WareKey'=>$order['Number'].'-'.($it_num+1),
                            'Weight'=>$item['weight'],
                            'WeightBrutto'=>$item['weight'],
                            'Payment'=>$item['payment']
                        ]
                    ];
                }
                $arPackage['INNER']=$products;
                $arOrder['INNER'][]=$arPackage;
            }
            if(isset($order['SERVICES']))
            foreach($order['SERVICES'] as $service){
                $arService=[
                    'OBJECT'=>'AddService',
                    'ATTRIBUTES'=>[
                        'ServiceCode'=>$service
                    ]
                ];
                $arOrder['INNER'][]=$arService;
            }
            $arParams[]=$arOrder;
        }
        $arTitle=array(
            'TITLE'=>"DeliveryRequest",
            'ATTRIBUTES'=>$attributes
        );
        //dd($arParams);
        return array(
            'PATH' => "new_orders.php",
            'POST' => array('xml_request'=>$this->_simple_xml($arTitle,$arParams))
        );
    }

    private function _simple_xml($arTitle,$arParams){
        $date=date("Y-m-d");
        $account=$this->account;
        $secure_password=$this->secure_password;
        $secure=md5("$date&$secure_password");

        $title=$arTitle['TITLE'];

        $arTitle['ATTRIBUTES']['Date']=isset($arTitle['ATTRIBUTES']['Date'])?$arTitle['ATTRIBUTES']['Date']:$date;
        $arTitle['ATTRIBUTES']['Account']=isset($arTitle['ATTRIBUTES']['Account'])?$arTitle['ATTRIBUTES']['Account']:$account;
        $arTitle['ATTRIBUTES']['Secure']=isset($arTitle['ATTRIBUTES']['Secure'])?$arTitle['ATTRIBUTES']['Secure']:$secure;
        $xml="<?xml version=\"1.0\" encoding=\"UTF-8\" ?><$title";
        if(isset($arTitle['ATTRIBUTES'])) {
            foreach ($arTitle['ATTRIBUTES'] as $attr_name => $attr_value) {
                $xml .= " $attr_name=\"$attr_value\"";
            }
        }
        $xml.=">";
        $this->_simple_xml_objects($arParams,$xml);
        $xml.="</$title>";
        return $xml;
    }
    private function _simple_xml_objects($arParams,&$xml){
        foreach($arParams as $arItem){
            $obj=$arItem['OBJECT'];
            $xml.="<$obj";
            foreach($arItem['ATTRIBUTES'] as $attr_name=>$attr_value){
                $xml.=" $attr_name=\"$attr_value\"";
            }
            if(isset($arItem['INNER'])){
                $xml.=" >";
                $this->_simple_xml_objects($arItem['INNER'],$xml);
                $xml.=" </$obj>";
            }else $xml.=" />";
        }
    }
}
