<?php

namespace App\Http\Controllers;

use App\Models\AdvertInAction;
use App\Models\Client;
use App\Models\DocProduct;
use App\Models\Document;
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
    function getStats($dateFirst,$dateLast,$city,ProductInfoCategory $category,ProductInfo $brand,Client $client,Document $RDS,CDEKController $cdek,AdvertInAction $adv){
        $stats=array();
        $stats['category_all']=['sum_price'=>0,'sum_quantity'=>0,'profit'=>0];
        $stats['brand_all']=['sum_price'=>0,'sum_quantity'=>0,'profit'=>0];
        $stats['city_all']=['sum_price'=>0,'sum_quantity'=>0,'profit'=>0,'delivery'=>0,'adv'=>0,'result'=>0];
        $stats['cities']=array();
        $data=$category->getProductStats($dateFirst,$dateLast,$city);
        foreach ($data as $item){
            $head=$item->info_category;
            preg_match("/(.+?)\//", $head,$matches);
            $head=isset($matches[1])?$matches[1]:$head;
            $stats['category'][$head][]=$item;
            if($head==$item->info_category){
                $stats['category_all']['sum_price']+=$item->sum_price;
                $stats['category_all']['sum_quantity']+=$item->sum_quantity;
                $stats['category_all']['profit']+=$item->profit;
            }
        }

        $data=$brand->getProductStats($dateFirst,$dateLast,$city);
        foreach ($data as $item){
            $stats['brand'][]=$item;
            $stats['brand_all']['sum_price']+=$item->sum_price;
            $stats['brand_all']['sum_quantity']+=$item->sum_quantity;
            $stats['brand_all']['profit']+=$item->profit;
        }

        $advList=array();
        $db_adv=$adv->getCitiesByDate($dateFirst);
        foreach ($db_adv as $el_adv){
            $advList[$el_adv->city_name]=$el_adv->cost;
        }

        $data=$client->getProductStats($dateFirst,$dateLast);
        foreach ($data as $item){
            if(!empty($dateFirst)) {
                $stats['cities'][$item->city]['deliveryServicesCostTotal'] = 0;
                if(!empty($item->city)) {
                    $filter['period'] = array(
                        'start' => $dateFirst,
                        'finish' => $dateLast,
                    );
                    $filter['data'] = array('city' => $item->city);
                    $list = $RDS->getList(1, $filter);
                    $rdsList = array();
                    foreach ($list['query'] as $rds) {
                        $rdsList[] = $rds->number;
                    }
                    $lInfo = $cdek->getListInfo($rdsList);
                    foreach ($list['query'] as $rds) {
                        if (isset($lInfo[$rds->number])) {
                            if (!isset($lInfo[$rds->number]['DeliverySumTotal'])) $lInfo[$rds->number]['DeliverySumTotal'] = 400;
                            $rds->status_code = $lInfo[$rds->number]['Status']['Code'];
                            if ($rds->status_code == 4 || $rds->status_code == 5) {
                                $stats['cities'][$item->city]['deliveryServicesCostTotal'] += $lInfo[$rds->number]['DeliverySumTotal'];
                            }
                        }
                    }
                }
            }

            $stats['cities'][$item->city]['adv']=isset($advList[$item->city])?$advList[$item->city]:0;
            $stats['cities'][$item->city]['result']=$item->profit-$stats['cities'][$item->city]['deliveryServicesCostTotal']-$stats['cities'][$item->city]['adv'];
            $stats['cities'][$item->city]['color']=($stats['cities'][$item->city]['result']<0)?'red':'green';

            $stats['city'][]=$item;
            $stats['city_all']['sum_price']+=$item->sum_price;
            $stats['city_all']['sum_quantity']+=$item->sum_quantity;
            $stats['city_all']['profit']+=$item->profit;
            $stats['city_all']['delivery']+=$stats['cities'][$item->city]['deliveryServicesCostTotal'];
            $stats['city_all']['adv']+=$stats['cities'][$item->city]['adv'];
            $stats['city_all']['result']+=$stats['cities'][$item->city]['result'];
        }
        //dd($stats['cities']);
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
