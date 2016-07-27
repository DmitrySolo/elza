<?php

namespace App\Http\Controllers;

use App\Models\DocProduct;
use App\Models\ProductInfo;

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
