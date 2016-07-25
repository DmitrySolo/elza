<?php
namespace App\Http\Controllers;


use App\Models\Sr;

class SrController extends Controller{
    public function getStat(Sr $sr, array $filter=[]){
        $arResult = $sr->getStat($filter);
        $arResult['result'] = json_decode($arResult['result']);
        return $arResult;
    }
}