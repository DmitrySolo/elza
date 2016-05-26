<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderTask extends Model
{
    public function setOrderTask($arr){
        $order_id=$this->insertGetId(
            [
                "created_at" => date('Y-m-d H:i:s',time()),
                "site"=>$arr["site"],
                "order_id"=>$arr["order_id"],
                "description"=>$arr["desc"],
                "phone"=>$arr["phone"],
                "order_date"=>$arr["order_date"],
            ]
        );
        return $order_id;
    }
    public function BeginTaskHistory($arr,$taskID){
        DB::table('tasks_history')->insert(
            ['task_id' => $taskID, 'step_description' => $arr['step'],'time_setted'=>$arr['setted_time'],'step_reason'=>'Поступление устной заявки от клиента','step_count'=>1]
        );
    }
}
