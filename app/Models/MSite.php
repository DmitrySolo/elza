<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MSite extends Model
{
    function setSite($name,$service_name,$service_group){
        $column="site_$service_name"."_$service_group";
        if($this->where('site_name',$name)->increment('site_found'))
            $this->where('site_name',$name)->increment($column);
        else $this->insertGetId(['site_name'=>$name,$column=>1]);
    }

    function clearPoints(){
        $this->update(['site_found' => 0,
            'site_google_advert' => 0,'site_google_search' => 0,
            'site_yandex_advert' => 0,'site_yandex_search' => 0]
        );
    }
}