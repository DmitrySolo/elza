<?php

namespace App\Http\Controllers;
use App\Models\CdekCity;
use App\Models\CdekTariff;
use Illuminate\Contracts\Mail\Mailer;
use App\Models\Client;
use App\Models\Task;
use App\Models\goods_problem;
use Illuminate\Http\Request;
//use Illuminate\Mail\Mailer;
//use Illuminate\Contracts\Mail;
use App\Http\Controllers\CDEKController;
use App\Http\Requests;
use App\Models\Document;
use App\Models\DocProduct;
//use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Mail;
class AjaxFormController extends Controller
{

    public function docInfoAjax($number,Document $document, DocProduct $product,DocumentController $CDocument,CDEKController $CDEK,BitrixController $bitrix){
        $data=array_merge($CDocument->getDoc($document,$product,$number),$CDEK->basicCDEK($number));
        //$data['bitrix']=$bitrix->search($data['ClientName'],$data['ClientAddress']);
        return  view('ajax.registerRDSproblem',['data'=>$data]);
    }
    public function addGoodsProblemTask(Request $request,Task $task,goods_problem $gp){
         $arrp=array();
         $arrp['doc']=$request->docnum;
         $arrp['desc']=$request->description;
         $arrp['step']=$request->step;
         $arrp['setted_time']=$request->setTime;
         $problem = $gp->setProblem($arrp);
         $arr=array();
         $arr['type']=3;
         $arr['content']=$problem;
         $taskID=$task->setTask($arr);
         $gp->BeginTaskHistory($arrp,$taskID);

        $from_user = "=?UTF-8?B?".base64_encode('santehsmart.ru')."?=";
        $subject = "=?UTF-8?B?".base64_encode('test')."?=";

        $headers = "From: $from_user <sale@santehsmart.ru>\r\n".
            "MIME-Version: 1.0" . "\r\n" .
            "Content-type: text/html; charset=UTF-8" . "\r\n";

        mail('solo-webworks@mail.ru', $subject, 'teest', $headers);

        /*Mail::send('emails.appeal', array('key' => 'value'), function($message) {
           $message->to('solo-webworks@mail.ru');
        });*/
         echo "<div class=\"alert alert-success\" role=\"alert\">
            <a href=\"#\" class=\"alert-link\">ЗАДАЧА ДОБАВЛЕНА</a>
         </div>";
    }
    public function getFullTaskById(Task $task,Request $request,Document $document,Client $clientM,goods_problem $goodsProblem){
        $phone=0;
        $client=array();
        if($request->type==1)$phone=$task->getByID($request->id,$request->type)->phone;
        elseif($request->type==4){
            $docId=$task->getByID($request->id,$request->type)->document_id;
            $client=$document->getWithClient($docId);
            $phone=$client->phone;
        }
        $allClientDocs=$clientM->getDocsByPhone($phone);
        $arrDocc=array();
        $appealHistoryArr=array();
        foreach ($allClientDocs as $doc) $arrDocc[]=$doc->number;
        if($request->type==1&&!empty($arrDocc))$client=$document->getWithClient($arrDocc[0]);
        $appealHistory=$goodsProblem->getProblemByDocs($arrDocc);
        foreach ($appealHistory as $appeal){
           $appealHistoryArr[$appeal->document_id]=$appeal->description;
        }
        $taskInfoData=[];
        if($request->type==1) {
            $clientInfo=[
                'client'=>$client,
                'allDoc'=>$allClientDocs,
                'appealHis' => $appealHistoryArr
            ];
            $bitrix=new BitrixController();
            $task_data=$task->getByID($request->id, $request->type);
            $statuses=$bitrix->statusList($task_data->site);
            $taskInfoData = ['data' => $task_data, 'statuses' => $statuses,'clientInfo'=>$clientInfo];
            return view('ajax.taskOrder',$taskInfoData);
        }elseif($request->type==3) {
            $taskInfoData = ['data' => $task->getByID($request->id, $request->type), 'client' => $client, 'allDoc' => $allClientDocs, 'appealHis' => $appealHistoryArr];
            return view('ajax.taskInfo',$taskInfoData);
        }
        return view('ajax.taskInfo',$taskInfoData);
    }
    public function getWithClientByID( Document $doc,CDEKController $cdek,Request $request){
        $rds=$doc->getByID($request->doc_id);
        if($rds)$resCdek=$cdek->basicCDEK($rds->number,intval($rds->old));
        //dd($resCdek);
        if(isset($resCdek['ERROR_CDEK'])||(!isset($resCdek))){
            $resCdek=[
                "Number" => $request->rds,
                "DispatchNumber" => "0",
                "Status" => [
                    "Date" => "",
                    "Code" => "0",
                    "Description" => "Статус отсутствует",
                    "CityCode" => "0",
                    "CityName" => ""
                ],
                "Reason" => [],
                "DeliverySum" => "0",
                "AddedService" => [],
                "DeliverySumTotal" => 0,
                "ERROR_CDEK" => isset($resCdek)?$resCdek['ERROR_CDEK']:'Накладная СДЭК не найдена.'
            ];
        }
        $package=array();
        if(isset($resCdek['Package']) && !empty($resCdek['Package'])){
            foreach ($resCdek['Package'] as $itemq ){
                //dd($itemq);
                if(isset($itemq['Item']['@attributes'])) {
                    $key = $itemq['Item']['@attributes']['WareKey'];
                    $value = $itemq['Item']['@attributes']['DelivAmount'];
                    $package[$key] = $value;
                }
            }
        }
        $res=$doc->getWithClientAndGoodsById($request->doc_id);
            $goods=array();
            $client=array();
            $flag=0;
        foreach($res as $resItem){
            $resCdek['track']=$resItem['track'];
            if(!$flag){
                $client['name']=$resItem['name'];
                $client['address']=$resItem['address'];
                $client['city']=$resItem['city'];
                $client['phone']=$resItem['phone'];
                $doc['number']=$resItem['number'];
                $doc['date']=$resItem['date'];
                $goods['total_price']=0;

                $goods['total_base_price'] = 0;
            }
            if($resItem['sku']==106730){
                $goods['delivery_cost']= $resItem['price'];
            }
            else {
                $goods['products'][$flag]['sku'] = $resItem['sku'];
                $goods['products'][$flag]['product_name'] = $resItem['product_name'];
                $goods['products'][$flag]['real_quantity'] = (array_key_exists($resItem['sku'],$package))?$package[$resItem['sku']]:$resItem['quantity'];
                $goods['products'][$flag]['price'] = $resItem['price']*$goods['products'][$flag]['real_quantity'];
                $goods['products'][$flag]['base'] = $resItem['base_price'];
                $goods['products'][$flag]['rds_quantity'] = $resItem['quantity'];
                $goods['total_price'] += $resItem['price']* $goods['products'][$flag]['real_quantity'];
                $goods['total_base_price'] += $resItem['base_price']/** $goods['products'][$flag]['real_quantity']*/;
            }
            $flag++;
        }
        if(!isset($resCdek['ERROR_CDEK'])){
        foreach($resCdek['AddedService'] as $service){
            $i= CDEKController::$masks['dopUslugi'][$service['@attributes']['ServiceCode']];
            $resCdek['services'][$i]=$service['@attributes']['Sum'];
        }
        }else $resCdek['ERROR_CDEK']='TRUE';
        $res=['client'=>$client,'goods'=>$goods,'pp'=>$resCdek,'doc'=>$doc,'pkg'=>$package];
        return view('ajax.getWithClient',['data'=>$res]);
    }
    public function CDEK(CDEKController $cdek, Request $rqst){
        $res=$cdek->basicCDEK($rqst->rds);
        return view('ajax.modalCdek',['data'=>$res]);
    }

    public function getPVZList(Request $request,CDEKController $cdek){
        if(isset($request->cityID)){
            $pvz=$cdek->getPVZ($request->cityID);
            parse_str($request->form,$form);
            $weight=0;
            foreach ($form['PACKAGES'] as $package){
                $weight+=intval($package['weight']);
            }
            foreach ($pvz as $key=>$p){
                if(intval($p['WeightMin'])<$weight && intval($p['WeightMax'])>=$weight) continue;
                else unset($pvz[$key]);
            }
            return view('ajax.listPVZ',['data'=>$pvz]);
        }
        return '';
    }

    public function newCDEK(Request $request,Document $doc){
        $cdekCities=new CdekCity();
        $cities=$cdekCities->getList();
        $cdekTariffs=new CdekTariff();
        $tariffs=$cdekTariffs->getList();

        //$bitrix = new BitrixController();

        $input = [
            'Number' => $request->rds,
            'deliveryCost' => 0,
            'phone' => '',
            'email' => '',
            'name' => '',
            'CityName' => '',
            'flat' => '',
            'house' => '',
            'street' => '',
            'tariff' => '',
            'pvz' => ''
        ];
        $res=$doc->getWithClientAndGoodsByNumber($request->rds);
        foreach($res as $resItem){
            $input['name']=$resItem['name'];
            $input['email']=$resItem['address'];
            $input['CityName']=$resItem['city'];
            $input['phone']=$resItem['phone'];
            if($resItem['sku']!=106730) {
                $input['PACKAGES'][] = [
                    'weight' => '',
                    'size_a' => '',
                    'size_b' => '',
                    'size_c' => '',
                    'Items' => [
                        [
                            'amount' => $resItem['quantity'],
                            'sku' => $resItem['sku'],
                            'name' => $resItem['product_name'],
                            'price' => intval($resItem['price']),
                            'payment' => '',
                            'weight' => ''
                        ]
                    ]
                ];
            }else $input['deliveryCost']=intval($resItem['price']);
        }
        $packagenum=0;
        if(isset($input['PACKAGES']))$packagenum=count($input['PACKAGES']);
            $mainData = array('input' => $input,'cities'=>$cities,'tariffs'=>$tariffs,'packagenum' => $packagenum);
        return view('ajax.newCDEK',$mainData);
    }

    public function newCDEKelem(Request $request){
        //dd($request);
        if(isset($request->package)&&!isset($request->item)){
            return view('ajax.newCDEKpackage',['package'=>intval($request->package)]);
        }elseif(isset($request->package)&&isset($request->item)){
            return view('ajax.newCDEKproduct',['package'=>intval($request->package),'item'=>intval($request->item)]);
        }
        return '';
    }

    public function newCDEKCalculate(Request $request,CDEKController $controller){
        //dd($request->all());

        $controller->calculate($request->all());
    }

    public function newCDEKBitrixInfo(Request $request,BitrixController $bitrix){
        $data=array();
        if(isset($request->rds)){
            $data=$bitrix->searchByRDS($request->rds);
        }
        return view('ajax.newCDEKbitrix',['data'=>$data]);
    }

    public function getBitrixList(Request $request,BitrixController $bitrixController){
        //dd($request);
        $list = array('ORDERS' => []);
        if (isset($request->dateFirst)) {
            $list['dateFirst']=$request->dateFirst;
            $list['dateLast']=$request->dateLast;
            $list = $bitrixController->orders($request->dateFirst, $request->dateLast);
        }
        return view('child.bitrixList',['data'=>$list]);
    }
    public function getBitrix(Request $request,BitrixController $bitrixController,Client $client,Document $docu,CDEKController $cdek){
        $bitrix=$bitrixController->get($request->number,$request->site);
        $docs=$client->getDocsByPhone($bitrix["PHONE"]);
        return view('ajax.modalBitrix',['bitrix'=>$bitrix,'docs'=>$docs]);
    }
    public function delayTask(Request $request, TaskController $controller, Task $task){
        $arrTask['task_id']=$request->id;
        $arrTask['user_id']=Auth::id();
        $arrTask['step']['waiting']=$request->waiting;
        $arrTask['step']['step_count']=$request->step_count;
        $arrTask['step']['step_reason']=$request->DelayReasonDesc;
        $arrTask['step']['step_description']='<b>[Отложено]</b>'.$request->status;
        $arrTask['step']['time_setted']=$request->setTime;
        $controller->delayTask($arrTask, $task);
        echo "hee-hee";
    }
    public function changeTaskStatus(Request $request, TaskController $controller,Task $task){
        $status=$request->status;
        if($request->task_type==1){
            $bitrix=new BitrixController();
            $bitrix->statusSet($request->order_id,$status,$request->site);
            $st_arr=$bitrix->statusList($request->site);
            foreach($st_arr['OPEN'] as $st){
                if($st['ID']==$request->status){
                    $status=$st['NAME'];
                    break;
                }
            }
        }
        $arr=array('task_id'=>$request->id,
                    'step_reason'=>$request->step_reason,
                    'status'=>$status,
                    'setTime'=>$request->setTime,
                    'step_count'=>$request->step_count,
                    'waiting'=>$request->waiting,
                    'time_setted'=>$request->setTime,
                    'user_id'=>Auth::id()
            );
        $controller->changeTaskStatus($arr,$task);
    }
    public function completeTask(Request $request, TaskController $controller,Task $task){
        $reason=$request->DoneTask;
        if($request->task_type==1){
            $bitrix=new BitrixController();
            $bitrix->statusSet($request->order_id,$request->DoneTask,$request->site);
            $st_arr=$bitrix->statusList($request->site);
            foreach($st_arr['CLOSE'] as $st){
                if($st['ID']==$request->DoneTask){
                    $reason=$st['NAME'];
                    break;
                }
            }
        }
        $arr=array(
            'task_id'=>$request->id,
            'step_reason'=>$reason,
            'status'=>'Завершена',
            'step_count'=>$request->step_count,
            'waiting'=>$request->waiting,
            'user_id'=>Auth::id()
        );
        $controller->completeTask($arr,$task);
    }
}

