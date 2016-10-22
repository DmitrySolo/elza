<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CdekCity extends Model
{
    public function getList(){
        return $this->cdeklist()->get();
    }

    public function scopeCdekList($query){
        $query->orderBy('cdek_city_name');
    }
}
