<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Task extends Model
{
    private $taskTypeTaxonomy=[1=>'order_tasks',2=>'',3=>'goods_problems',4=>''];
    private $taskTypePrefix=[1=>'order_task',2=>'',3=>'goods_problem',4=>''];

    public function getByID($id,$type){

        return $task=$this->join('task_types', 'tasks.task_type', '=', 'task_types.task_type_id')
            ->join( $this->taskTypeTaxonomy[$type], 'tasks.task_content', '=', $this->taskTypeTaxonomy[$type].'.'.$this->taskTypePrefix[$type].'_id')
            ->join('tasks_history', 'task_id', '=', 'tasks.id')
            ->orderBy('step_count', 'desc')
            ->orderBy('history_id', 'desc')
            ->select('tasks.*', 'task_types.*',$this->taskTypeTaxonomy[$type].'.*','tasks_history.*')
            ->id($id)->first();
    }

    public function scopeID($query,$id){
        $query->where('tasks.id','=',$id);
    }
    public function setTask($arr){
        $id=$this->insertGetId(
            [
                "at_" =>date('Y-m-d H:i:s',time()),
                "user_id"=>"0",
                "task_type"=>$arr['type'],
                "priority_index"=>"0",
                "waiting"=>"0",
                "task_content"=>$arr['content']
            ]
        );
        return $id;
    }

    public function getOrderTask($order_id){
        return $task=$this->join('task_types', 'tasks.task_type', '=', 'task_types.task_type_id')
            ->join( 'order_tasks', 'tasks.task_content', '=', 'order_tasks'.'.order_task_id')
            ->join('tasks_history', 'task_id', '=', 'tasks.id')
            ->orderBy('step_count', 'desc')
            ->orderBy('history_id', 'desc')
            ->select('tasks.*', 'task_types.*','order_tasks.*','tasks_history.*')
            ->where('order_id','=',$order_id)
            ->first();
    }

    public function reviewTasks(){
        $tasks=$this->join('task_types', 'tasks.task_type', '=', 'task_types.task_type_id')
            ->select('tasks.*', 'task_types.max_human_minutes')->where('tasks.is_complete','!=','Y')
            ->get();

        foreach($tasks as $task){
            if($task->task_type==3){
                // echo $task->task_content;
                // $maxStep=DB::table('tasks_history')
                $setTime= DB::table('tasks_history')
                    ->orderBy('step_count', 'desc')
                    ->select('time_setted')->where('task_id',$task->id)
                    ->first();
                $task->max_human_minutes=$setTime->time_setted;

            }
            $this->where('id','=',$task->id)->increment('waiting', 5);
            $resIndex=$task->max_human_minutes/($task->waiting+5);
            $this->where('id',$task->id)->update(['priority_index'=>$resIndex]);
        }
        return $this->join('task_types', 'tasks.task_type', '=', 'task_types.task_type_id')
            ->orderBy('priority_index', 'asc')
            ->select('tasks.*', 'task_types.task_name')
            ->get();
    }
    public function reviewBitrix($arParams){
        $ot = new OrderTask();
        foreach ($arParams["ORDERS"] as $order) {
            $time_setted=($order['status_id']=='X')?5400:0;
            if (in_array($order['order_id'], $arParams["LOCAL"])) {
                $task = $this->getOrderTask($order['order_id']);
                //echo $order['status'], '***', $task->step_description,'<br>';
                if ($order['status'] != $task->step_description) {
                    $arr = array();
                    $arr['task_id'] = $task->task_id;
                    $arr['step_count'] = $task->step_count;
                    $arr['waiting'] = $task->waiting;
                    $arr['time_setted'] = $time_setted?$time_setted:$task->time_setted;
                    $arr['step_reason'] = "Обновление статуса заказа";
                    $arr['status'] = $order['status'];
                    $this->updateTaskResponsibility($arr['task_id'],0);
                    $this->changeTaskStatus($arr);
                }
            } else {
                $arrp = array();
                $arrp['order_id'] = $order['order_id'];
                $arrp['desc'] = $order['status'];
                $arrp['phone'] = $order['phone'];
                $arrp['site'] = $order['site'];
                $arrp['order_date'] = $order['order_date'];
                $arrp['step'] = $order['status'];
                $arrp['setted_time'] = $time_setted?$time_setted:60;
                $order_id = $ot->setOrderTask($arrp);
                $arr = array();
                $arr['type'] = 1;
                $arr['content'] = $order_id;
                $task_id = $this->setTask($arr);
                $ot->BeginTaskHistory($arrp, $task_id);
            }
            if($order['status_over']){
                $task = $this->getOrderTask($order['order_id']);
                $arr = array();
                $arr['task_id'] = $task->task_id;
                $arr['step_count'] = $task->step_count;
                $arr['waiting'] = $task->waiting;
                $arr['step_reason'] = "Завершение заказа";
                $arr['status'] = $order['status'];
                $this->completeTask($arr);
            }
        }
    }
    public function getTasks(){
        return $this->join('task_types', 'tasks.task_type', '=', 'task_types.task_type_id')
            ->Join('tasks_history', 'task_id', '=', 'tasks.id')
            ->where('priority_index','>',0)
            ->where('tasks.is_complete','!=','Y')
            ->orderBy('priority_index', 'asc')
            ->orderBy('tasks_history.step_count', 'asc')
            ->select('tasks.*', 'task_types.*','tasks_history.*')
            ->get();
    }
    public function getTaskOrders($begin_date){
        return $this->join('order_tasks', 'tasks.task_content', '=', 'order_tasks.order_task_id')
            ->where('order_tasks.order_date','>=',$begin_date)
            ->select('tasks.*', 'order_tasks.order_id','order_tasks.order_date')
            ->get()->pluck('order_id')->all();
    }
    public function updateTaskResponsibility($taskId,$userID){
        $this->where('id',$taskId)
            ->update(array('user_id' => Auth::id(),
                    'waiting'=>1)
            );
    }
    public function setTaskStep($stepArr,$taskID){
        DB::table('tasks_history')->where('task_id',$taskID)
            ->where('step_count',$stepArr['step_count'])
            ->update(['time_spended'=>$stepArr['waiting']]);
        DB::table('tasks_history')->insert(
            [
                "task_id" =>$taskID,
                "time_setted"=>$stepArr['time_setted'],
                "step_reason"=>$stepArr['step_reason'],
                "step_count"=>$stepArr['step_count']+1,
                "step_description"=>$stepArr['step_description']
            ]);
    }
    public function changeTaskStatus($arr){
        DB::table('tasks_history')->where('task_id',$arr['task_id'])
            ->where('step_count',$arr['step_count'])
            ->update(['time_spended'=>$arr['waiting']]);
        DB::table('tasks_history')->insert(
            [
                "task_id" =>$arr['task_id'],
                "time_setted"=>$arr['time_setted'],
                "step_reason"=>$arr['step_reason'],
                "step_count"=>$arr['step_count']+1,
                "step_description"=>$arr['status']
            ]);

    }
    public function completeTask($arr){
        DB::table('tasks_history')->where('task_id',$arr['task_id'])
            ->where('step_count',$arr['step_count'])
            ->update(['time_spended'=>$arr['waiting']]);
        DB::table('tasks_history')->insert(
            [
                "task_id" =>$arr['task_id'],
                "step_reason"=>$arr['step_reason'],
                "step_count"=>$arr['step_count']+1,
                "step_description"=>$arr['status'],
            ]);
        $this->where('id',$arr['task_id'])
            ->update(array('is_complete' => 'Y' )
            );

    }
}
//super test