<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MCategoriesGroup extends Model
{
    protected $table = 'm_categories_group';
    function getGroups(){
        return $this->select('m_categories_group.*')->get();
    }
}