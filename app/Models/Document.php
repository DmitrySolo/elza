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
    public function getByID($id){
        $res = $this->id($id)->first();
        return $res;
    }
    public function getWithOld($number,$old){
        $res = $this->rdc($number)->where('old',$old)->first();
        return $res;
    }
    public function getByClientId($client){
        return $this->where('client_id',$client)->get();
    }
    public function getWithClient($number){
        $res = $this->rdc($number)->client()->first();
        return $res;
    }
    public function getWithClientByID($id){
        $res = $this->id($id)->client()->first();
        return $res;
    }
    public function getWithClientAndGoodsByNumber($number){
        return $res = $this->rdc($number)->GoodsAndClients()->get();
    }
    public function getWithClientAndGoodsById($id){
        return $res = $this->id($id)->GoodsAndClients()->get();
    }
    public function getWithClientAndGoodsByMultiNumber($numbers){
        return $res = $this->rdcmulti($numbers)->GoodsAndClients()->get();
    }
    public function getWithoutTrack(){
        return $this->where('track',0)->get();
    }

    public function setTrack($number,$track){
        return $this->rdc($number)->update(['track'=>$track]);
    }

    public function getWithGcrByRdsID($rdsIDs){
        return $res = $this->rdcmultiid($rdsIDs)->GoodsClientsReturns()->get();
    }

    public function import($arr){
        $retId=0;
        if(!$this->getWithOld($arr['document_number'],$arr['document_old'])) $retId=$this->insertGetId(
            [
                "number" =>$arr['document_number'],
                "date"=>$arr['document_date'],
                "description"=>$arr['document_description'],
                "author"=>$arr['document_author'],
                "old"=>$arr['document_old'],
                "client_id"=>$arr['document_client']
            ]
        );
        return $retId;
    }

    public function scopeRDC($query,$number){
        $query->where('documents.number','=',$number);
    }
    public function scopeID($query,$id){
        $query->where('documents.doc_id','=',$id);
    }
    public function scopeRDCMulti($query,$numbers){
        $query->whereIn('documents.number', $numbers);
    }
    public function scopeRDCMultiID($query,$ids){
        $query->whereIn('documents.doc_id', $ids);
    }
    public function scopeClient($query){
        $query->join('clients', 'documents.client_id', '=', 'clients.id')
            ->select('documents.*', 'clients.name', 'clients.address', 'clients.phone', 'clients.city');
    }
    public function scopeGoodsAndClients($query){
        $query->join('doc_products', 'documents.doc_id', '=', 'doc_products.doc_id')
            ->join('clients', 'documents.client_id', '=', 'clients.id')
            ->select('documents.*', 'clients.name', 'clients.address','clients.city', 'clients.phone','doc_products.*');
    }
    public function scopeGoodsClientsReturns($query){
        $query->join('doc_products', 'documents.doc_id', '=', 'doc_products.doc_id')
            ->leftJoin('returns', 'doc_products.doc_number', '=', 'returns.docu_number')
            ->leftJoin('ret_products', function($join)
            {
                $join->on('returns.ret_number', '=', 'ret_products.ret_number')
                    ->on('doc_products.sku', '=', 'ret_products.ret_sku');
            })
            ->join('clients', 'documents.client_id', '=', 'clients.id')
            ->select('documents.*', 'clients.name', 'clients.address','clients.city', 'clients.phone','doc_products.*','ret_products.*');
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
