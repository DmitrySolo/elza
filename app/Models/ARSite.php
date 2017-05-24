<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ARSite extends Model
{
    public function get($id){
        $res = $this->site($id)->first();
        return $res;
    }
    public function add($arr){
        return $this->insertGetId(
            [
                "site_city" =>$arr['site_city'],
                "site_name"=>$arr['site_name'],
                "site_classes"=>$arr['site_classes']
            ]
        );
    }

    public function getByName($name){
        $res = $this->sitename($name)->first();
        return $res;
    }
    public function updateClasses($id,$classes){
        return $this->site($id)->update(['site_classes'=>$classes]);
    }

    public function scopeSite($query,$id){
        $query->where('ar_sites.site_id','=',$id);
    }
    public function scopeSiteName($query,$name){
        $query->where('ar_sites.site_name','=',$name);
    }
}