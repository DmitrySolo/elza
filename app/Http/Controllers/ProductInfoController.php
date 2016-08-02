<?php

namespace App\Http\Controllers;

use App\Models\DocProduct;
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
    function getStats($dateFirst,$dateLast,ProductInfoCategory $category,ProductInfo $brand){
        $stats=array();
        $stats['category_all']=['sum_price'=>0,'sum_quantity'=>0,'profit'=>0];
        $stats['brand_all']=['sum_price'=>0,'sum_quantity'=>0,'profit'=>0];
        $data=$category->getProductStats($dateFirst,$dateLast);
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
        $data=$brand->getProductStats($dateFirst,$dateLast);

        foreach ($data as $item){
            $stats['brand'][]=$item;
            $stats['brand_all']['sum_price']+=$item->sum_price;
            $stats['brand_all']['sum_quantity']+=$item->sum_quantity;
            $stats['brand_all']['profit']+=$item->profit;
        }
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
