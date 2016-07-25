<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
    protected $table = 'parse_searchresult_history';
    function set($params){
        $params['at_']=date('Y-m-d H:i:s');
        $id=$this->insertGetId($params);
        return $id;
    }
    function get($params){
        return $this->where(['search_parameters'=>$params])->orderBy('at_', 'desc')->first();
    }
    function setToDate($date,$params){
        if(count($get=$this->where('at_',$date)->get())){
            $result=empty($get[0]->search_result)?array():json_decode($get[0]->search_result,true);
            $params['search_result']=array_merge_recursive($params['search_result'], $result);
            $params['search_result']=json_encode($params['search_result'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
            $this->where('at_',$date)->update($params);
        }else{
            $params['at_']=$date;
            $params['search_result']=json_encode($params['search_result'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
            $this->insert($params);
        }
    }
}