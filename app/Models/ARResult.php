<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ARResult extends Model
{
    public function get($id){
        $res = $this->result($id)->latest()->first();
        return $res;
    }
    public function add($arr){
        return $this->insertGetId(
            [
                "page_id"=>$arr['page_id'],
                "result_code" =>$arr['result_code']
            ]
        );
    }


    public function scopeResult($query,$id){
        $query->where('ar_results.result_id','=',$id);
    }
}