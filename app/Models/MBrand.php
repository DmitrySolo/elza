<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MBrand extends Model
{
    function getBrands(){
        return $this->get();
    }

    function newBrand($name){
        return $this->insertGetId(['brand_name'=>$name]);
    }

    function deleteBrand($id){
        $this->where('brand_id', $id)->delete();
    }
}