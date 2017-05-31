<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArPage extends Model
{
    public function get($id){
        $res = $this->page($id)->first();
        return $res;
    }
    public function getAll(){
        $res = $this->get();
        return $res;
    }
    public function add($arr){
        return $this->insertGetId(
            [
                "site_id"=>$arr['site_id'],
                "page_url" =>$arr['page_url'],
                "rule_id"=>$arr['rule_id']
            ]
        );
    }

    public function getByRule($rule_id){
        $res = $this->rule($rule_id)->get();
        return $res;
    }
    public function updateURL($id,$url){
        return $this->site($id)->update(['page_url'=>$url]);
    }

    public function scopePage($query,$id){
        $query->where('ar_pages.page_id','=',$id);
    }
    public function scopeRule($query,$id){
        $query->where('ar_pages.rule_id','=',$id);
    }
}