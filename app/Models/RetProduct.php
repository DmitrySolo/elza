<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class RetProduct extends Model
{
    public function getAll($number){
        return $this->pnk($number)->get();
    }
    public function get($number,$sku){
        return $this->pnk($number)->product($sku)->first();
    }

    public function import($arr){
        if($product=$this->get($arr['document_number'],$arr['product_code'])) {
            if($product->ret_price!=$arr['product_price']||$product->ret_base_price!=$arr['product_base_price'])
            $this->product($arr['document_number'], $arr['product_code'])->update(
                [
                    "ret_number" => $arr['document_number'],
                    "ret_sku" => $arr['product_code'],
                    "ret_product_name" => $arr['product_name'],
                    "ret_quantity" => $arr['product_quantity'],
                    "ret_price" => $arr['product_price'],
                    "ret_base_price" => $arr['product_base_price']
                ]
            );
        }else $this->insert(
            [
                "ret_number" =>$arr['document_number'],
                "ret_sku"=>$arr['product_code'],
                "ret_product_name"=>$arr['product_name'],
                "ret_quantity"=>$arr['product_quantity'],
                "ret_price"=>$arr['product_price'],
                "ret_base_price"=>$arr['product_base_price']
            ]
        );
    }

    function getProducts(){
        return $this->select('ret_sku')->distinct('ret_sku')->get();
    }

    public function scopePNK($query,$number){
        $query->where('ret_number','=',$number);
    }

    public function scopeProduct($query,$sku){
        $query->where('ret_sku','=',$sku);
    }
}
