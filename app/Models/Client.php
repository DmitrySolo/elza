<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    public function get($id){
        return $this->client($id)->first();
    }
    public function getDocsByPhone($phone){
        return $this->phoneDoc($phone)->get();
    }

    public function import($arr){

        if($product=$this->get($arr['code'])) {
            $this->client($arr['code'])->update(
                [
                    "name" => $arr['name'],
                    "address" => $arr['email'],
                    "phone" => $arr['phone'],
                    "city" => $arr['city']
                ]
            );
        }else $this->insert(
            [
                "id" => $arr['code'],
                "name" => $arr['name'],
                "address" => $arr['email'],
                "phone" => $arr['phone'],
                "city" => $arr['city']
            ]
        );
    }

    public function scopeClient($query,$id){
        $query->where('id','=',$id);
    }
    public function scopePhoneDoc($query,$phone){
        $query->where('phone','=',$phone)->join('documents', 'clients.id', '=', 'documents.client_id')
            ->select('clients.*', 'documents.number', 'documents.date');
    }
    public function getCities(){
       return $res=$this->select('city')->distinct()->get();
    }
}
