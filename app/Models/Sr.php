<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sr extends Model
{
    protected $table = 'parse_searchresult_history';
    public function getStat( array $filter= [] ){
        if(!$filter){
            $dbResult = $this->orderBy('at_', 'desc')->first();
            $arResult['date'] = $dbResult->at_;
            $arResult['result'] = $dbResult->search_result;
        }
        else{

        }
        return $arResult;
    }
}