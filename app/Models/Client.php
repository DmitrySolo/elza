<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Client extends Model
{
    public function get($id){
        return $this->client($id)->first();
    }
    public function getDocsByPhone($phone){
        return $this->phoneDoc($phone)->get();
    }

    public function import($arr){

        if($product=$this->get($arr['code'])) {
            $this->client($arr['code'])->update(
                [
                    "name" => $arr['name'],
                    "address" => $arr['email'],
                    "phone" => $arr['phone'],
                    "city" => $arr['city']
                ]
            );
        }else $this->insert(
            [
                "id" => $arr['code'],
                "name" => $arr['name'],
                "address" => $arr['email'],
                "phone" => $arr['phone'],
                "city" => $arr['city']
            ]
        );
    }

    public function scopeClient($query,$id){
        $query->where('id','=',$id);
    }
    public function scopePhoneDoc($query,$phone){
        $query->where('phone','=',$phone)->join('documents', 'clients.id', '=', 'documents.client_id')
            ->select('clients.*', 'documents.number', 'documents.date');
    }
    public function getCities(){
       return $res=$this->select('city')->distinct()->get();
    }

    function getProductStats($dateFirst,$dateLast){
        return $this->select( 'clients.city',
            DB::raw('sum(doc_products.price*doc_products.quantity) as sum_price'),
            DB::raw('sum(doc_products.quantity) as sum_quantity'),
            DB::raw('sum((doc_products.price*doc_products.quantity)-doc_products.base_price) as profit')/*,
            DB::raw('sum(ret_products.ret_price*ret_products.ret_quantity) as sum_ret_price'),
            DB::raw('sum(ret_products.ret_quantity) as sum_ret_quantity'),
            DB::raw('sum((ret_products.ret_price*ret_products.ret_quantity)-ret_products.ret_base_price) as ret_profit')*/)
            ->join('documents', 'clients.id', '=', 'documents.client_id')
            ->join('doc_products', 'documents.number', '=', 'doc_products.doc_number')
            /*->leftJoin('returns', 'doc_products.doc_number', '=', 'returns.docu_number')
            ->leftJoin('ret_products', function($join)
            {
                $join->on('returns.ret_number', '=', 'ret_products.ret_number')
                    ->on('doc_products.sku', '=', 'ret_products.ret_sku');
            })*/
            ->date($dateFirst,$dateLast)
            ->groupBy('clients.city')->orderBy('sum_price','desc')->get();
    }

    function scopeDate($query,$dateFirst,$dateLast){
        if(empty($dateFirst)&&!empty($dateLast))
            $query->where('documents.date','<=',$dateLast);
        if(!empty($dateFirst)&&empty($dateLast))
            $query->where('documents.date','>=',$dateFirst);
        if(!empty($dateFirst)&&!empty($dateLast))
            $query->where('documents.date','>=',$dateFirst)->where('documents.date','<=',$dateLast);
    }
}
