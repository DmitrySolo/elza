<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class HeaderController extends Controller
{
    public function sayHello(){
        return view('header');
    }
}
