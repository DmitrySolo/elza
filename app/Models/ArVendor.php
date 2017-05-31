<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArVendor extends Model
{
    public function get($id){
        $res = $this->vendor($id)->first();
        return $res;
    }
    public function getAll(){
        $res = $this->get();
        return $res;
    }
    public function add($arr){
        return $this->insertGetId(
            [
                "vendor_name"=>$arr['vendor_name'],
                "vendor_mail" =>$arr['vendor_mail'],
                "vendor_check"=>$arr['vendor_check']
            ]
        );
    }

    public function getByName($name){
        $res = $this->vendorname($name)->first();
        return $res;
    }
    public function updateCheck($id,$check){
        return $this->site($id)->update(['vendor_check'=>$check]);
    }

    public function scopeVendor($query,$id){
        $query->where('ar_vendors.vendor_id','=',$id);
    }
    public function scopeVendorName($query,$name){
        $query->where('ar_vendors.vendor_name','=',$name);
    }
}