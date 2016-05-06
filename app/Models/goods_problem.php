<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class goods_problem extends Model
{
    public function setProblem($arr){
        $problem=$this->insertGetId(
            [
                "at_" => date('Y-m-d H:i:s',time()),
                "document_id"=>$arr["doc"],
                "description"=>$arr["desc"],
            ]
        );
        return $problem;
    }
    public function getProblemByDocs($docArr){

        return $this->whereIn('document_id', $docArr)->get();
    }
    public function BeginTaskHistory($arr,$taskID){
        DB::table('tasks_history')->insert(
            ['task_id' => $taskID, 'step_description' => $arr['step'],'time_setted'=>$arr['setted_time'],'step_reason'=>'Поступление устной заявки от клиента','step_count'=>1]
        );
    }
}
