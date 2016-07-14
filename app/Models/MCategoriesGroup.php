<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MCategoriesGroup extends Model
{
    protected $table = 'm_categories_group';
    function getGroups(){
        return $this->get();
    }

    function newGroup($name){
        return $this->insertGetId(['category_group_name'=>$name]);
    }

    function deleteGroup($id){
        $this->where('category_group_id', $id)->delete();
    }
}