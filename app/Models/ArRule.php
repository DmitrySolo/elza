<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArRule extends Model
{
    public function get($id){
        $res = $this->rule($id)->first();
        return $res;
    }
    public function add($arr){
        return $this->insertGetId(
            [
                "vendor_id"=>$arr['vendor_id'],
                "rule_correct" =>$arr['rule_correct'],
                "rule_sku"=>$arr['rule_sku']
            ]
        );
    }


    public function getBySku($sku){
        $res = $this->rulesku($sku)->first();
        return $res;
    }
    public function getByVendor($vendor_id){
        $res = $this->vendor($vendor_id)->get();
        return $res;
    }
    public function updateCorrect($id,$correct){
        return $this->rule($id)->update(['rule_correct'=>$correct]);
    }
    public function updateCorrectBySku($sku,$correct){
        return $this->rulesku($sku)->update(['rule_correct'=>$correct]);
    }


    public function scopeRule($query,$id){
        $query->where('ar_rules.rule_id','=',$id);
    }
    public function scopeRuleSku($query,$sku){
        $query->where('ar_rules.rule_sku','=',$sku);
    }
    public function scopeVendor($query,$id){
        $query->where('ar_rules.vendor_id','=',$id);
    }
}