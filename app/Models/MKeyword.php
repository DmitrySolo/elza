<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MKeyword extends Model
{
    function getKeywords(){
        return $this->get();
    }

    function newKeyword($name){
        return $this->insertGetId(['keyword_name'=>$name]);
    }

    function deleteKeyword($id){
        $this->where('keyword_id', $id)->delete();
    }
}