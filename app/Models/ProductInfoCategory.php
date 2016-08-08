<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ProductInfoCategory extends Model
{
    function getCategories(){
        return $this->get();
    }

    function newCategory($name){
        if($this->where('info_category',$name)->count()==0)
            $this->insert(['info_category'=>$name]);
    }

    function clear(){
        $this->truncate();
    }

    function getProductStats($dateFirst,$dateLast,$city){
        return $this->select( 'product_info_categories.info_category',
            DB::raw('sum(doc_products.price*doc_products.quantity) as sum_price'), DB::raw('sum(doc_products.quantity) as sum_quantity'),
            DB::raw('sum((doc_products.price*doc_products.quantity)-doc_products.base_price) as profit')/*,
            DB::raw('avg(doc_products.price) as avg_price'),DB::raw('max(doc_products.price*doc_products.quantity) as max_price'),
            DB::raw('max(doc_products.quantity) as max_quantity'),
            DB::raw('min(doc_products.price) as min_price'),DB::raw('min(doc_products.quantity) as min_quantity')*/)
            ->join('product_infos', 'product_infos.category', 'LIKE', DB::raw( "CONCAT(product_info_categories.info_category, '%')" ))
            ->join('doc_products', 'product_infos.sku', '=', 'doc_products.sku')
            ->join('documents', 'doc_products.doc_number', '=', 'documents.number')
            ->join('clients', 'documents.client_id', '=', 'clients.id')
            ->date($dateFirst,$dateLast)
            ->city($city)
            ->groupBy('product_info_categories.info_category')->orderBy('product_info_categories.info_category','asc')->get();
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