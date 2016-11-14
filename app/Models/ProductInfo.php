<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ProductInfo extends Model
{
    function getProducts(){
        return $this->get();
    }

    function newProduct($params){
        if($this->where('sku',$params['sku'])->count()==0)
            $this->insert($params);
    }

    function getProductStats($dateFirst,$dateLast,$city){
        return $this->select( 'product_infos.brand',
            DB::raw('sum(doc_products.price*doc_products.quantity) as sum_price'),
            DB::raw('sum(doc_products.quantity) as sum_quantity'),
            DB::raw('sum((doc_products.price*doc_products.quantity)-doc_products.base_price) as profit')/*,
            DB::raw('sum(ret_products.ret_price*ret_products.ret_quantity) as sum_ret_price'),
            DB::raw('sum(ret_products.ret_quantity) as sum_ret_quantity'),
            DB::raw('sum((ret_products.ret_price*ret_products.ret_quantity)-ret_products.ret_base_price) as ret_profit')*//*,
            DB::raw('avg(doc_products.price) as avg_price'),DB::raw('max(doc_products.price*doc_products.quantity) as max_price'),
            DB::raw('max(doc_products.quantity) as max_quantity'),
            DB::raw('min(doc_products.price) as min_price'),DB::raw('min(doc_products.quantity) as min_quantity')*/)
            ->join('doc_products', 'product_infos.sku', '=', 'doc_products.sku')
            ->join('documents', 'doc_products.doc_number', '=', 'documents.number')
            /*->leftJoin('returns', 'doc_products.doc_number', '=', 'returns.docu_number')
            ->leftJoin('ret_products', function($join)
            {
                $join->on('returns.ret_number', '=', 'ret_products.ret_number')
                    ->on('doc_products.sku', '=', 'ret_products.ret_sku');
            })*/
            ->join('clients', 'documents.client_id', '=', 'clients.id')
            ->date($dateFirst,$dateLast)
            ->city($city)
            ->groupBy('product_infos.brand')->orderBy('sum_price','desc')->get();
    }

    function scopeDate($query,$dateFirst,$dateLast){
        if(empty($dateFirst)&&!empty($dateLast))
            $query->where('documents.date','<=',$dateLast);
        if(!empty($dateFirst)&&empty($dateLast))
            $query->where('documents.date','>=',$dateFirst);
        if(!empty($dateFirst)&&!empty($dateLast))
            $query->where('documents.date','>=',$dateFirst)->where('documents.date','<=',$dateLast);
    }

    function scopeCity($query,$city){
        if($city!='!empty!') $query->where('clients.city',$city);
    }
}