<?php

namespace App\Http\Controllers;

use App\Models\AdvertInAction;
use App\Models\Client;
use App\Models\DocProduct;
use App\Models\Document;
use App\Models\MCity;
use App\Models\ProductInfo;
use App\Models\ProductInfoCategory;

class ProductInfoController extends Controller
{
    function updateProducts(){
        $doc_product=new DocProduct();

        $db_products=$doc_product->getProducts();
        $arProducts=array();
        foreach ($db_products as $db_get){
            $arProducts[]=$db_get->sku;
        }

        return $this->updateByArray($arProducts);
    }

    function updateProductBySku($sku){
        return $this->updateByArray([$sku]);
    }

    function updateByArray($arProducts){
        $product=new ProductInfo();
        $response=$this->_bitrix_curl(['action'=>'products_info','products'=>$arProducts]);

        $n=0;
        if(isset($response['PRODUCTS'])&&!empty($response['PRODUCTS']))
        foreach ($response['PRODUCTS'] as $p){
            $params=array(
                'sku'=>$p['XML_ID'],
                'category'=>$p['NAV_PATH'],
                'brand'=>$p['VENDOR']
            );
            $product->newProduct($params);
            $n++;
        }
        return $n;
    }

    function updateCategories(){
        $section=new ProductInfoCategory();
        $response=$this->_bitrix_curl(['action'=>'section_list']);

        $n=0;
        foreach ($response['SECTIONS'] as $p){
            $section->newCategory($p['FULL_NAME']);
            $n++;
        }
        return $n;
    }
    function getStats($dateFirst,$dateLast,$city,ProductInfoCategory $category,ProductInfo $brand,Client $client){
        $stats=array();
        $stats['category_all']=['sum_price'=>0,'sum_quantity'=>0,'profit'=>0];
        $stats['brand_all']=['sum_price'=>0,'sum_quantity'=>0,'profit'=>0];
        $stats['city_all']=['sum_price'=>0,'sum_quantity'=>0,'profit'=>0,'delivery'=>0,'adv'=>0,'result'=>0];
        $data=$category->getProductStats($dateFirst,$dateLast,$city);
        foreach ($data as $item){
            $head=$item->info_category;
            if(!empty($head)) {
                preg_match("/(.+?)\//", $head, $matches);
                $head = isset($matches[1]) ? $matches[1] : $head;
            }
            $stats['category'][$head][]=$item;
            if($head==$item->info_category){
                $stats['category_all']['sum_price']+=$item->sum_price;
                $stats['category_all']['sum_quantity']+=$item->sum_quantity;
                $stats['category_all']['profit']+=$item->profit;
                if(!empty($item->sum_ret_price)){//если товар возвратный, то убираем прибыль
                    $stats['category_all']['sum_price']-=$item->sum_ret_price;
                    $stats['category_all']['sum_quantity']-=$item->sum_ret_quantity;
                    $stats['category_all']['profit']-=$item->ret_profit;
                }
            }
        }

        $data=$brand->getProductStats($dateFirst,$dateLast,$city);
        foreach ($data as $item){
            $stats['brand'][]=$item;
            $stats['brand_all']['sum_price']+=$item->sum_price;
            $stats['brand_all']['sum_quantity']+=$item->sum_quantity;
            $stats['brand_all']['profit']+=$item->profit;
            if(!empty($item->sum_ret_price)){//если товар возвратный, то убираем прибыль
                $stats['brand_all']['sum_price']-=$item->sum_ret_price;
                $stats['brand_all']['sum_quantity']-=$item->sum_ret_quantity;
                $stats['brand_all']['profit']-=$item->ret_profit;
            }
        }

        $cdek = new CDEKController();
        $adv = new AdvertInAction();
        $RDS = new Document();
        $cityRegion = new MCity();
        $db_city=$cityRegion->getCitiesWithRegions();
        $cityList=array();
        $regionCount=array();
        foreach ($db_city as $el_city){
            $cityList[$el_city->city_name]=array(
                'id'=>$el_city->region_id,
                'name'=>$el_city->region_full_name
            );
            $regionCount[$el_city->region_id][]=$el_city->city_name;
        }
        //print_r($regionCount);
        $advList=array();
        $db_adv=$adv->getCitiesByDate($dateFirst);
        foreach ($db_adv as $el_adv){
            $advList[$el_adv->city_name]=$el_adv->cost;
        }

        $data=$client->getProductStats($dateFirst,$dateLast);
        foreach ($data as $item){
            foreach ($regionCount as &$oneRegion){
                foreach ($oneRegion as &$oneCity){
                    if($oneCity==$item->city)$oneCity=1;
                }
            }
        }
        foreach ($regionCount as &$oneRegion){
            foreach ($oneRegion as $key=>$oneCity){
                if($oneCity!=1)unset($oneRegion[$key]);
            }
        }
        //print_r($regionCount);
        foreach ($regionCount as $key=>$oneRegion){
            $regionCount[$key]=count($oneRegion);
        }
        //print_r($regionCount);
        foreach ($data as $key=>$item){
            if(!empty($item->sum_ret_price)){//если товар возвратный, то убираем прибыль
                $item->sum_price-=$item->sum_ret_price;
                $item->sum_quantity-=$item->sum_ret_quantity;
                $item->profit-=$item->ret_profit;
            }

            $cityItem=array(
                'region_id'=>null,
                'city'=>$item->city,
                'cityName'=>empty($item->city)?'Окно в Европу':$item->city,
                'sum_price'=>$item->sum_price,
                'sum_quantity'=>$item->sum_quantity,
                'profit'=>$item->profit,
                'profit_percent'=>empty($item->sum_price)?0:round($item->profit/$item->sum_price*100,2),
                'profit_average'=>empty($item->sum_quantity)?0:($item->profit/$item->sum_quantity),
                'deliveryServicesCostTotal'=>0,
                'adv'=>0,
                'result'=>['value'=>0,'color'=>'']
            );

            if(!empty($dateFirst)) {
                if(!empty($item->city)) {
                    $filter['period'] = array(
                        'start' => $dateFirst,
                        'finish' => $dateLast,
                    );
                    $filter['data'] = array('city' => $item->city);
                    $list = $RDS->getList(1, $filter);
                    $rdsArrays = array();
                    foreach ($list['query'] as $rds) {
                        $rdsArrays[$rds->number][($rds->old)?'Old':'New']=['number'=>$rds->number,'doc_id'=>$rds->doc_id,'old'=>$rds->old];
                    }
                    $lInfo = $cdek->getListInfo($rdsArrays);
                    foreach ($list['query'] as $rds) {
                        $order_subfolder=$rds->old?'Old':'New';
                        if (isset($lInfo[$rds->number][$order_subfolder])) {
                            if (!isset($lInfo[$rds->number][$order_subfolder]['DeliverySumTotal'])) $lInfo[$rds->number][$order_subfolder]['DeliverySumTotal'] = 400;
                            $rds->status_code = $lInfo[$rds->number][$order_subfolder]['Status']['Code'];
                            if ($rds->status_code == 4 || $rds->status_code == 5) {
                                $cityItem['deliveryServicesCostTotal'] += $lInfo[$rds->number][$order_subfolder]['DeliverySumTotal'];
                                foreach($lInfo[$rds->number][$order_subfolder]['AddedService'] as $service){
                                    $cityItem['deliveryServicesCostTotal'] += $service['@attributes']['Sum'];
                                }
                            }
                        }
                    }
                }
            }

            $cityItem['adv']=isset($advList[$item->city])?($advList[$item->city]):0;

            $k=sprintf("%02d", $key);
            $keyCode="z_city_$k";
            if(isset($cityList[$item->city])){
                $keyCode="a_region_{$cityList[$item->city]['id']}_z_$k";
                $cityItem['region_id']=$cityList[$item->city]['id'];

                $regionCode="a_region_{$cityList[$item->city]['id']}_a";
                if(!isset($stats['city'][$regionCode])) {
                    $stats['city'][$regionCode] = array(
                        'is_region' => true, 'region_id' => $cityItem['region_id'],
                        'city' => $item->city, 'cityName' => $cityList[$item->city]['name'],
                        'sum_price' => 0, 'sum_quantity' => 0, 'profit' => 0, 'profit_percent' => 0,
                        'profit_average' => 0, 'deliveryServicesCostTotal' => 0, 'adv' => $cityItem['adv'],
                        'result' => ['value' => 0, 'color' => '']
                    );
                    $stats['city_all']['adv']+=$cityItem['adv'];
                }
                $cityItem['adv']=0;

                $r=$stats['city'][$regionCode];

                $r['sum_price']+=$cityItem['sum_price'];
                $r['sum_quantity']+=$cityItem['sum_quantity'];
                $r['profit']+=$cityItem['profit'];
                $r['profit_percent']=empty($r['sum_price'])?0:round($r['profit']/$r['sum_price']*100,2);
                $r['profit_average']=empty($r['sum_quantity'])?0:($r['profit']/$r['sum_quantity']);
                $r['deliveryServicesCostTotal']+=$cityItem['deliveryServicesCostTotal'];
                //$r['adv']+=$cityItem['adv'];
                $r['result']['value']=$r['profit']-$r['deliveryServicesCostTotal']-$r['adv'];
                $r['result']['color']=($r['result']['value']<0)?'red':'green';

                $stats['city'][$regionCode]=$r;
            }

            $cityItem['result']['value']=$item->profit-$cityItem['deliveryServicesCostTotal']-$cityItem['adv'];
            $cityItem['result']['color']=($cityItem['result']['value']<0)?'red':'green';

            //сортировка записей
            //сначала региональные (a_region), затем остальные города (z_city)
            //в регионах первым делом записывается результат, а потом данные городов (z_$k)
            $stats['city'][$keyCode]=$cityItem;

            $stats['city_all']['sum_price']+=$cityItem['sum_price'];
            $stats['city_all']['sum_quantity']+=$cityItem['sum_quantity'];
            $stats['city_all']['profit']+=$cityItem['profit'];
            $stats['city_all']['delivery']+=$cityItem['deliveryServicesCostTotal'];
            //$stats['city_all']['adv']+=$cityItem['adv'];
            $stats['city_all']['result']+=$cityItem['result']['value'];
        }

        ksort($stats['city']);

        return $stats;
    }

    private function _bitrix_curl($arParams){
        $json_send=json_encode($arParams);
        $ch=curl_init("http://www.santehsmart.ru/testzone/elza.json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('json'=>$json_send));
        $out=curl_exec($ch);
        curl_close($ch);
        $data=json_decode($out,true);
        return $data;
    }
}
