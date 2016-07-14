<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MCity extends Model
{
    function getCitiesWithRegions(){
        return $this->region()->get();
    }
    function getCities(){
        return $this->get();
    }

    public function scopeRegion($query){
        $query->join('m_regions', 'm_cities.region_id', '=', 'm_regions.region_id')
            ->select('m_cities.*', 'm_regions.*');
    }

    function newCity($region_id,$name){
        if($this->where('region_id',$region_id)->count())
            $this->insertGetId(['region_id'=>$region_id,'city_name'=>$name]);
    }

    function deleteCity($name){
        $this->where('city_name', $name)->delete();
    }

    function deleteCityByRegion($region_id){
        $this->where('region_id', $region_id)->delete();
    }
}