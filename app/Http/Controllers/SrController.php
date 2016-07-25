<?php
namespace App\Http\Controllers;


use App\Models\Sr;

class SrController extends Controller{
    private function _filterArr(array $filter){
        return $filteredResult;
    }
    public function getStat(Sr $sr, array $filter=[]){
        if(!$filter){
            $arResult = $sr->getStat();
            $arResult['result'] = json_decode($arResult['result']);
        }
        else{
            $arResult=$this->_filterArr($filter);
        }
        return $arResult;
    }
}