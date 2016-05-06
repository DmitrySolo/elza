<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskType;

class TaskController extends Controller {
    public function testTable(TaskType $task_type){
        $task_type_list=$task_type->getAll();
        return view('welcome',['task_type'=>$task_type_list]);
    }
    public function Greet (Task $task)
    {
        $tasks = $task->getByManagerName('Vovka');
        return view('welcome', ['tasks' => $tasks]);
    }
    public function setTask(Task $task,$arr){
        $eventArr=[
            "at_" =>date('Y-m-d H:i:s',time()),
            "menedgerName"=>"US",
            "task_type"=>$arr['type'],
            "priority_index"=>"0",
            "waiting"=>"0",
            "task_content"=>$arr['content']
        ];
        $task->setTask($eventArr);
    }

    public function ByBy () {
        return view('by');
    }

    public function reviewTasks(Task $task)
    {
       $reviewResult= $task->reviewTasks();
        return $reviewResult;
    }
    public function gavgav(){
       return "gav-gav";
    }
    public function getTasks(Task $task){
        $resMod=array();
        $result= $task->getTasks();
        foreach ($result as $res){

            $resMod[$res['id']]=$res;
        }
        return $resMod;
    }
    public function delayTask($taskArr, Task $task){
        $task->updateTaskResponsibility($taskArr['task_id'],$taskArr['user_id']);
        $task->setTaskStep($taskArr['step'],$taskArr['task_id']);
    }
    public function changeTaskStatus($arr,Task $task){
        $task->updateTaskResponsibility($arr['task_id'],$arr['user_id']);
        $task->changeTaskStatus($arr);
    }
    public function completeTask($arr,Task $task){
        $task->updateTaskResponsibility($arr['task_id'],$arr['user_id']);
        $task->completeTask($arr);

    }
}