<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class AdvertInAction extends Model
{
    protected $table = 'advert_in_action';
    public function getAll(){
        return $this->get();
    }

    public function getCitiesByDate($date){
        return $this->date($date)->region()->get();
    }

    function scopeDate($query,$date){
        if(!empty($date)) $query->where('advert_in_action.date_start','<=',$date)->where('advert_in_action.date_end','>=',$date);
    }

    public function scopeRegion($query){
        $query->where('advert_in_action.geo_type','R')->join('m_cities', 'advert_in_action.geo_id', '=', 'm_cities.region_id')
            ->select('m_cities.city_name', 'm_cities.region_id as region', DB::raw('sum(advert_in_action.advert_cost) as cost'))
            ->groupBy('m_cities.city_name')->orderBy('m_cities.city_name','asc');
    }
}
