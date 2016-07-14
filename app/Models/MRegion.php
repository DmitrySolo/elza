<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MRegion extends Model
{
    function getRegions(){
        return $this->get();
    }
    function getCities(){
        return $this->city()->get();
    }

    public function scopeCity($query){
        $query->join('m_cities', 'm_regions.region_id', '=', 'm_cities.region_id')
            ->select('m_regions.*', 'm_cities.*');
    }

    function newRegion($name){
        return $this->insertGetId(['region_name'=>$name]);
    }

    function deleteRegion($id){
        $this->where('region_id', $id)->delete();
    }
}