<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MCategory extends Model
{
    function getCategories(){
        return $this->get();
    }

    function newCategory($group_id,$name){
        if($this->where('category_group_id',$group_id)->count())
            return $this->insertGetId(['category_group_id'=>$group_id,'category_name'=>$name]);
        return 0;
    }

    function deleteCategory($id){
        $this->where('category_id', $id)->delete();
    }
}