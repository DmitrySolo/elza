<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\OrderTask;
use App\Models\Task;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class BitrixController extends Controller
{
    public function search($user_name,$address,$site='www.santehsmart.ru'){
        return $this->_bitrix_curl($site,array(
            //'action'=>'order',
            'action'=>'search',
            //'order_id'=>'1002839',
            'user_name'=>$user_name,
            'user_address'=>$address
        ));
    }
    public function searchByRDS($number,$site='www.santehsmart.ru'){
        $db_rds=new Document();
        $rds=$db_rds->getWithClient($number);
        return $this->_bitrix_curl($site,array(
            //'action'=>'order',
            'action'=>'search',
            //'order_id'=>'1002839',
            'user_name'=>$rds->name,
            'user_address'=>$rds->address,
            'user_phone'=>$rds->phone
        ));
    }
    public function orders($date_begin,$date_end='',$site='www.santehsmart.ru'){
        $arParams=array(
            'action'=>'list',
            'date_begin'=>$date_begin,
        );
        if(!empty($date_end))$arParams['date_end']=$date_end;
        return $this->_bitrix_curl($site,$arParams);
    }
    public function get($number,$site='www.santehsmart.ru'){
        return$this->_bitrix_curl($site,array(
            'action'=>'order',
            'order_id'=>$number,
        ));
    }
    public function getNewOrders($site='www.santehsmart.ru'){
        $task=new Task();
        $date_begin=date('d.m.Y',time()-(60*60*24));
        $date_end=date('d.m.Y');
        $local=$task->getTaskOrders(date('Y-m-d',time()-(60*60*24)));
        //dd($local);
        $arParams=array(
            'action'=>'list',
            'date_begin'=>$date_begin,
            'date_end'=>$date_end,
        );
        $bitrix=$this->_bitrix_curl($site,$arParams);
        //if($site!='www.santehsmart.ru')dd($bitrix);
        $arOrders=array();
        foreach($bitrix['ORDERS'] as $order){
            if(!in_array($order['ACCOUNT_NUMBER'],$local)){
                $arOrders[]=[
                    'site'=>$site,
                    'order_id'=>$order['ACCOUNT_NUMBER'],
                    'order_date'=>date('Y-m-d H:i:s',strtotime($order["DATE_INSERT"])),
                    'status'=>$order["STATUS_NAME"],
                    'phone'=>$order["PHONE"]
                ];
            }
        }
        return $arOrders;
    }
    public function getLastOrders($site='www.santehsmart.ru'){
        $task=new Task();
        $first=$task->getFirstOrder();
        $time_begin=time()-(60*60*24);
        if(isset($first->order_date)&&!empty($first->order_date))
            $time_begin=strtotime($first->order_date);
        $date_begin=date('d.m.Y',$time_begin);
        $date_end=date('d.m.Y');
        $local=$task->getTaskOrders(date('Y-m-d',$time_begin));
        //dd($local);
        $arParams=array(
            'action'=>'list',
            'date_begin'=>$date_begin,
            'date_end'=>$date_end,
        );
        $bitrix=$this->_bitrix_curl($site,$arParams);
        //dd($bitrix);
        //if($site!='www.santehsmart.ru')dd($bitrix);
        $arOrders=array();
        foreach($bitrix['ORDERS'] as $order){
            $arOrders[]=[
                'site'=>$site,
                'order_id'=>$order['ACCOUNT_NUMBER'],
                'order_date'=>date('Y-m-d H:i:s',strtotime($order["DATE_INSERT"])),
                'status'=>$order["STATUS_NAME"],
                'status_id'=>$order["STATUS_ID"],
                'status_over'=>strstr($order["STATUS_DESCRIPTION"],"[Завершен]")!=false,
                'status_middle'=>strstr($order["STATUS_DESCRIPTION"],"[Отложен]")!=false,
                'phone'=>$order["PHONE"]
            ];
        }
        return ["ORDERS"=>$arOrders,"LOCAL"=>$local];
    }
    public function getNewO(){
        $model=new Task();
        $model->reviewBitrix($this->getLastOrders());
        $model->reviewBitrix($this->getLastOrders('ove-cfo.ru'));
    }
    public function statusSet($number,$status,$site='www.santehsmart.ru'){
        return $this->_bitrix_curl($site,array(
            'action'=>'status_set',
            'order_id'=>$number,
            'status'=>$status,
        ));
    }
    public function statusGet($number,$site='www.santehsmart.ru'){
        return $response = $this->_bitrix_curl($site,array(
            'action'=>'status_get',
            'order_id'=>$number,
        ));
    }
    public function statusList($site='www.santehsmart.ru'){
        return  $this->_bitrix_curl($site,array('action'=>'status_list_get'));
    }

    private function _bitrix_curl($site,$arParams){
        $json_send=json_encode($arParams);
        $ch=curl_init("http://$site/testzone/elza.json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('json'=>$json_send));
        $out=curl_exec($ch);
        curl_close($ch);
        $data=json_decode($out,true);
        return $data;
    }
}
