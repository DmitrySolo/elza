<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocProduct extends Model
{
    public function getAll($number){
        return $this->rdc($number)->get();
    }
    public function get($number,$sku){
        return $this->rdc($number)->product($sku)->first();
    }

    public function import($arr){
        if($product=$this->get($arr['document_number'],$arr['product_code'])) {
            if($product->price!=$arr['product_price']||$product->base_price!=$arr['product_base_price'])
            $this->product($arr['document_number'], $arr['product_code'])->update(
                [
                    "doc_number" => $arr['document_number'],
                    "sku" => $arr['product_code'],
                    "product_name" => $arr['product_name'],
                    "quantity" => $arr['product_quantity'],
                    "price" => $arr['product_price'],
                    "base_price" => $arr['product_base_price']
                ]
            );
        }else $this->insert(
            [
                "doc_number" =>$arr['document_number'],
                "sku"=>$arr['product_code'],
                "product_name"=>$arr['product_name'],
                "quantity"=>$arr['product_quantity'],
                "price"=>$arr['product_price'],
                "base_price"=>$arr['product_base_price']
            ]
        );
    }

    public function scopeRDC($query,$number){
        $query->where('doc_number','=',$number);
    }

    public function scopeProduct($query,$sku){
        $query->where('sku','=',$sku);
    }
}
