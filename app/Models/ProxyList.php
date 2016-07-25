<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProxyList extends Model
{
    protected $table = 'proxy_list';
    function getProxyList(){
        return $this->get();
    }
}