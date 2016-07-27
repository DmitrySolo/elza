<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductInfo extends Model
{
    function getProducts(){
        return $this->get();
    }

    function newProduct($params){
        if($this->where('sku',$params['sku'])->count()==0)
            $this->insert($params);
    }
}