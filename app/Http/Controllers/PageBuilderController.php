<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Document;
use App\Models\DocProduct;

class PageBuilderController extends Controller
{
    public function index(TaskController $taskCnt,Task $task){

        $tasks=$taskCnt->getTasks($task);
        $taskData=array('data'=>$tasks);

        $headerData=array('data'=>'header');
        $leftsidebarData=array('data'=>'leftsidebar');
        $rightsidebarData=array('data'=>'rightsidebar');

        return  view()->make('main')
                ->nest('main','child.taskList',$taskData)
                ->nest('header', 'child.header',$headerData)
                ->nest('footer', 'child.footer')
                ->nest('leftsidebar', 'child.leftsidebar',$leftsidebarData)
                ->nest('rightsidebar', 'child.rightsidebar',$rightsidebarData);
    }

    public function docInfo($number,Document $document, DocProduct $product,DocumentController $CDocument,CDEKController $CDEK,BitrixController $bitrix){
        $data=array_merge($CDocument->getDoc($document,$product,$number),$CDEK->basicCDEK($number));
        $data['bitrix']=$bitrix->search($data['ClientName'],$data['ClientAddress']);

        $headerData=array('data'=>'header');
        $leftsidebarData=array('data'=>'leftsidebar');
        $rightsidebarData=array('data'=>'rightsidebar');

        return  view()->make('main')
            ->nest('main','child.docInfo',['data'=>$data])
            ->nest('header', 'child.header',$headerData)
            ->nest('footer', 'child.footer')
            ->nest('leftsidebar', 'child.leftsidebar',$leftsidebarData)
            ->nest('rightsidebar', 'child.rightsidebar',$rightsidebarData);
    }
    public function getRDSList(Request $request,Document $RDS,CDEKController $CDEKController,$filter=null){
        $page=$request->page;
        if(isset($request->submit)){
            if (!empty($request->number)) $number = 'РДС-' . $request->number; else $number = '';
            if(!empty($request->dispatch)) {
                if(empty($number=$CDEKController->getNumberByReturn($request->dispatch,$request->start,$request->finish)))
                    $number=$CDEKController->getNumberByDispatch($request->dispatch);
            }
            $filter['period'] = array(
                'start' => $request->start,
                'finish' => $request->finish,
            );
            $filter['data'] = array(
                'city' => $request->city,
                'author' => $request->author,
                'number' => $number
            );
            $filter['nameLike'] = $request->name;
            $list = $RDS->getList($page, $filter);
        }else {
            $filter=array();
            $list=$RDS->getList($page,$filter);
        }

        $footerData=array(
            'endTotal'=>0,
            'processTotal'=>0,
            'st4'=>0,
            'st5'=>0,
            'st45'=>0,
        );

        $rdsList=array();
        foreach($list['query'] as $rds){
            $rdsList[]=$rds->number;
        }
        if(count($rdsList)) {
            $lInfo = $CDEKController->getListInfo($rdsList);
            $goods = $RDS->getWithClientAndGoodsByMultiID($rdsList);

            foreach ($goods as $resItem) {
                if(isset($resCdek['Package']) && !empty($resCdek['Package'])){
                }

                if (isset($lInfo[$resItem['number']])) {
                    $package=array();
                    if(isset($lInfo[$resItem['number']]['Package']) && !empty($lInfo[$resItem['number']]['Package'])){
                        foreach ($lInfo[$resItem['number']]['Package'] as $itemq ){
                            //dd($itemq);
                            if(isset($itemq['Item'])) {
                                $key = $itemq['Item']['@attributes']['WareKey'];
                                $value = $itemq['Item']['@attributes']['DelivAmount'];
                                $package[$key] = $value;
                            }
                        }
                    }

                    if (!isset($lInfo[$resItem['number']]['flag'])) {
                        $lInfo[$resItem['number']]['total_price'] = 0;
                        $lInfo[$resItem['number']]['total_base_price'] = 0;
                        $lInfo[$resItem['number']]['delivery_cost'] = 0;
                    }

                    if (!isset($lInfo[$resItem['number']]['DeliverySumTotal'])) $lInfo[$resItem['number']]['DeliverySumTotal'] = 400;
                    $delSum = $lInfo[$resItem['number']]['DeliverySumTotal'];
                    if ($lInfo[$resItem['number']]['Status']['Code'] == 5) {
                        $lInfo[$resItem['number']]['total_price'] = 0;
                        $lInfo[$resItem['number']]['total_base_price'] = 0;
                        $lInfo[$resItem['number']]['delivery_cost'] = 0;
                    } else {
                        if ($resItem['sku'] == 106730) {
                            $lInfo[$resItem['number']]['delivery_cost'] = $resItem['price'];
                        } else {
                            $lInfo[$resItem['number']]['real_quantity'] = (array_key_exists($resItem['sku'],$package))?$package[$resItem['sku']]:$resItem['quantity'];
                            $lInfo[$resItem['number']]['total_price'] = $lInfo[$resItem['number']]['total_price'] += $resItem['price'] * $lInfo[$resItem['number']]['real_quantity'];
                            $lInfo[$resItem['number']]['total_base_price'] = $lInfo[$resItem['number']]['total_base_price'] += $resItem['base_price'] * $lInfo[$resItem['number']]['real_quantity'];
                        }
                    }

                    $lInfo[$resItem['number']]['total'] = $lInfo[$resItem['number']]['total_price']
                        - $lInfo[$resItem['number']]['total_base_price'] - $delSum
                        + $lInfo[$resItem['number']]['delivery_cost'];

                    $lInfo[$resItem['number']]['flag'] = 1;
                }
            }
            foreach ($list['query'] as &$rds) {
                if (isset($lInfo[$rds->number])) {
                    $rds->total = $lInfo[$rds->number]['total'];
                    $rds->status_code = $lInfo[$rds->number]['Status']['Code'];
                    $rds->status_desc=$lInfo[$rds->number]['Status']['Description'];
                    $rds->reason=$lInfo[$rds->number]['Reason']['Description'];

                    if($rds->status_code==4 || $rds->status_code==5){
                        $footerData['endTotal']+=$rds->total;
                        if($rds->status_code==4)$footerData['st4']++;
                        else {$footerData['st5']++;  $lInfo[$rds->number];
                            if(isset($footerData['reason'][$rds->reason]))  $footerData['reason'][$rds->reason]++;
                            else{$footerData['reason'][$rds->reason]=1;}
                          }
                        $footerData['st45']++;
                    }
                    else $footerData['processTotal']+=$rds->total;
                }
            }
        }
        if(isset($footerData['reason']))arsort($footerData['reason']);
        else $footerData['reason']=array();
        if($footerData['st45']) {
            $footerData['st4'] = round($footerData['st4'] / $footerData['st45'] * 100, 2);
            $footerData['st5'] = round($footerData['st5'] / $footerData['st45'] * 100, 2);
        }

        $headerData=array('data'=>'header');
        $leftsidebarData=array('data'=>'leftsidebar');
        $rightsidebarData=array('data'=>'rightsidebar');
        return view()->make('main')
            ->nest('main','child.rdsList',['data'=>$list])
            ->nest('header', 'child.header',$headerData)
            ->nest('footer', 'child.footer',['rds'=>$footerData])
            ->nest('leftsidebar', 'child.leftsidebar',$leftsidebarData)
            ->nest('rightsidebar', 'child.rightsidebar',$rightsidebarData);
    }

    public function getBitrixList(Request $request,BitrixController $bitrixController){
        $list=array('ORDERS'=>[],'dateFirst'=>date('d.m.Y',time()-(60*60*24*1)),'dateLast'=>date('d.m.Y'));
        if(isset($request->dateFirst)){
            $list['dateFirst']=$request->dateFirst;
            $list['dateLast']=$request->dateLast;
        }
        $list=$bitrixController->orders($list['dateFirst'],$list['dateLast']);
        $headerData=array('data'=>'header');
        $leftsidebarData=array('data'=>'leftsidebar');
        $rightsidebarData=array('data'=>'rightsidebar');
        return view()->make('main')
            ->nest('main','child.bitrixList',['data'=>$list])
            ->nest('header', 'child.header',$headerData)
            ->nest('footer', 'child.footer')
            ->nest('leftsidebar', 'child.leftsidebar',$leftsidebarData)
            ->nest('rightsidebar', 'child.rightsidebar',$rightsidebarData);
    }

    public function newCDEK(Request $request,CDEKController $CDEK){
        if(isset($request->newcdek)){
            $response=$CDEK->newDelivery([$request->all()]);
            dd($response);
            $mainData = array('response' => $response);
        }else {
            $input = [
                'Number' => 'РДС-',
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
            $packagenum=0;
            if(isset($input['PACKAGES']))$packagenum=count($input['PACKAGES']);
            $mainData = array('input' => $input,'packagenum' => $packagenum);
        }
        $headerData = array('data' => 'header');
        $leftsidebarData = array('data' => 'leftsidebar');
        $rightsidebarData = array('data' => 'rightsidebar');
        return view()->make('main')
            ->nest('main', 'ajax.newCDEK', $mainData)
            ->nest('header', 'child.header', $headerData)
            ->nest('footer', 'child.footer')
            ->nest('leftsidebar', 'child.leftsidebar', $leftsidebarData)
            ->nest('rightsidebar', 'child.rightsidebar', $rightsidebarData);
    }

    public function returnInfo(Request $request,Document $document, DocProduct $product,DocumentController $CDocument,CDEKController $CDEK,BitrixController $bitrix){
        if(!empty($request->input('Number')))return $this->docInfo($CDEK->getNumberByReturn(
            $request->input('Number'),$request->input('DateFirst'),$request->input('DateLast')
        ), $document,$product,$CDocument,$CDEK,$bitrix);
        return redirect('/');
    }

    public function login (){
        return view('login');
    }
}
