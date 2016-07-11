<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MCity extends Model
{
    function getCities(){
        return $this->region()->get();
    }

    public function scopeRegion($query){
        $query->join('m_regions', 'm_cities.region_id', '=', 'm_regions.region_id')
            ->select('m_cities.*', 'm_regions.*');
    }
}