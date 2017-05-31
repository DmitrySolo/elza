<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArResult extends Model
{
    public function get($id){
        $res = $this->result($id)->latest()->first();
        return $res;
    }
    public function getAllLatest(){
        $res = $this->distinct()->latest()->get();
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
    /*public function scopeGroupId($query){
        $query->where('ar_results.result_id','=',function($query){
            $query->select('paper_type_id')
                ->from(with(new ProductCategory)->getTable())
                ->whereIn('category_id', ['223', '15'])
                ->where('active', 1);
        });
    }*/
}