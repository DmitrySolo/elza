<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ARPage extends Model
{
    public function get($id){
        $res = $this->page($id)->first();
        return $res;
    }
    public function add($arr){
        return $this->insertGetId(
            [
                "site_id"=>$arr['site_id'],
                "page_http" =>$arr['page_http'],
                "rule_id"=>$arr['rule_id']
            ]
        );
    }

    public function updateHTTP($id,$http){
        return $this->site($id)->update(['page_http'=>$http]);
    }

    public function scopePage($query,$id){
        $query->where('ar_pages.page_id','=',$id);
    }
}