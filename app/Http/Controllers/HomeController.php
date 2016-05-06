<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function defaultC(WelcomeController $welcomeController){
        $sound=$welcomeController->gavgav();
        $data=array('data'=>array('serge','dimon',$sound));
      return  view()->make('main')->nest('header', 'child.header',$data);
    }
}
