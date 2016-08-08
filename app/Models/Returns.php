<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Returns extends Model
{
    protected $table = 'returns';
    public function get($number){
        $res = $this->pnk($number)->first();
        return $res;
    }
    public function getWithClient($number){
        $res = $this->pnk($number)->client()->first();
        return $res;
    }
    public function getWithClientAndGoodsByID($number){
        return $res = $this->pnk($number)->GoodsAndClients()->get();
    }
    public function getWithClientAndGoodsByMultiID($numbers){
        return $res = $this->pnkmulti($numbers)->GoodsAndClients()->get();
    }

    public function import($arr){
        if(!$this->get($arr['document_number'])) $this->insert(
            [
                "ret_number" =>$arr['document_number'],
                "ret_date"=>$arr['document_date'],
                "ret_desc"=>$arr['document_description'],
                "ret_author"=>$arr['document_author'],
                "ret_client"=>$arr['document_client']
            ]
        );
    }

    public function scopePNK($query,$number){
        $query->where('ret_number','=',$number);
    }
    public function scopePNKMulti($query,$numbers){
        $query->whereIn('ret_number', $numbers);
    }
    public function scopeClient($query){
        $query->join('clients', 'returns.client', '=', 'clients.id')
            ->select('returns.*', 'clients.name', 'clients.address', 'clients.phone', 'clients.city');
    }
    public function scopeGoodsAndClients($query){
        $query->join('ret_products', 'returns.ret_number', '=', 'ret_products.ret_number')
            ->join('clients', 'returns.client', '=', 'clients.id')
            ->select('returns.*', 'clients.name', 'clients.address','clients.city', 'clients.phone','ret_products.*');
    }
}