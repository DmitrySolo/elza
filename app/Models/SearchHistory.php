<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
    protected $table = 'parse_searchresult_history';
    function set($params){
        $params['at_']=date('Y-m-d H:i:s');
        $id=$this->insertGetId($params);
        return $id;
    }
}