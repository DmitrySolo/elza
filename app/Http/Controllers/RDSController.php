<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class RDSController extends Controller
{
    public function getRDSList(Document $RDS,$page=0,Request $request){
        if(isset($request->submit)){
            $filter['period']=array(

                  'start'=>$request->start,
                  'finish'=>$request->finish,
              );
            $filter['data']=array(
                'city'=>$request->city,
                'author'=>$request->author,
                'name'=>$request->name,
                'number'=>$request->number
            );
            $list=$RDS->getList(1,$filter);
            return view('child.rdsList',['data'=>$list]);
        }else {
            $filter=array();
             $list=$RDS->getList(1,$filter);
            return view('child.rdsList',['data'=>$list]);

        }
    }

    public function updateRDSdelivery(Document $RDS){
        $list=$RDS->getList(1,array());
        dd($list);
    }
}
