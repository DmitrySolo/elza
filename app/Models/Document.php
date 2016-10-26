<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    /**
     * @param $number
     * @return Document|bool
     */

    public $perPage=300000;
    public function get($number){
        $res = $this->rdc($number)->first();
        return $res;
    }
    public function getByClientId($client){
        return $this->where('client_id',$client)->get();
    }
    public function getWithClient($number){
        $res = $this->rdc($number)->client()->first();
        return $res;
    }
    public function getWithClientAndGoodsByID($number){
        return $res = $this->rdc($number)->GoodsAndClients()->get();
    }
    public function getWithClientAndGoodsByMultiID($numbers){
        return $res = $this->rdcmulti($numbers)->GoodsAndClients()->get();
    }

    /**
     * GCR - Goods, Clients, Returns
     * @param $numbers
     * @return mixed
     */
    public function getWithGCRByMultiID($numbers){
        return $res = $this->rdcmulti($numbers)->GoodsClientsReturns()->get();
    }

    public function import($arr){
        if(!$this->get($arr['document_number'])) $this->insert(
            [
                "number" =>$arr['document_number'],
                "date"=>$arr['document_date'],
                "description"=>$arr['document_description'],
                "author"=>$arr['document_author'],
                "client_id"=>$arr['document_client']
            ]
        );
    }

    public function scopeRDC($query,$number){
        $query->where('number','=',$number);
    }
    public function scopeRDCMulti($query,$numbers){
        $query->whereIn('number', $numbers);
    }
    public function scopeClient($query){
        $query->join('clients', 'documents.client_id', '=', 'clients.id')
            ->select('documents.*', 'clients.name', 'clients.address', 'clients.phone', 'clients.city');
    }
    public function scopeGoodsAndClients($query){
        $query->join('doc_products', 'documents.number', '=', 'doc_products.doc_number')
            ->join('clients', 'documents.client_id', '=', 'clients.id')
            ->select('documents.*', 'clients.name', 'clients.address','clients.city', 'clients.phone','doc_products.*');
    }
    public function scopeGoodsClientsReturns($query){
        $query->join('doc_products', 'documents.number', '=', 'doc_products.doc_number')
            ->leftJoin('returns', 'doc_products.doc_number', '=', 'returns.doc_number')
            ->leftJoin('ret_products', function($join)
            {
                $join->on('returns.ret_number', '=', 'ret_products.ret_number')
                    ->on('doc_products.sku', '=', 'ret_products.ret_sku');
            })
            ->join('clients', 'documents.client_id', '=', 'clients.id')
            ->select('documents.*', 'clients.name', 'clients.address','clients.city', 'clients.phone','doc_products.*','returns.*','ret_products.*');
    }

    public function getList($page=0,$arrFilter)
    {
        $cl= new Client;
        $cities=$cl->getCities();
        //echo $pages=ceil($this->count()/$this->perPage);//TODO g
        $resList= $this->join('clients', 'documents.client_id', '=', 'clients.id')
            ->orderBy('number', 'desc')
            ->select('documents.*', 'clients.*');
                if(isset($arrFilter['data'])){
                foreach($arrFilter['data'] as $key=>$value){
                   if($value)
                    $resList->where($key, $value);
                }}
//      dd($resList);
        if(isset($arrFilter['nameLike']) && !empty($arrFilter['nameLike'])){
           $resList->where('name', 'LIKE', '%'.$arrFilter['nameLike'].'%');
        }
        if(isset($arrFilter['number']) && !empty($arrFilter['number'])){
            $resList->where('number', 'LIKE', $arrFilter['number'].'%');
        }

        $p1 = date('Y-m').'-01';
        $p2 = date("Y-m-d");
        if(isset($arrFilter['period'])){
            if (!empty($arrFilter['period']['start'])) {
                $p1 = $arrFilter['period']['start'];
            }
            if (!empty($arrFilter['period']['finish'])) {
                $p2 = $arrFilter['period']['finish'];
            }
        }
        $queryRes=$resList->whereBetween('date', [$p1, $p2])
            ->skip(($page-1)*$this->perPage)->take($this->perPage)
            ->get();
        return array('query'=>$queryRes,'cities'=>$cities);
    }
}
