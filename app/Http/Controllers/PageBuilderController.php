<?php

namespace App\Http\Controllers;

use App\Models\ArPage;
use App\Models\ArResult;
use App\Models\ArRule;
use App\Models\ArSite;
use App\Models\ArVendor;
use App\Models\CdekCity;
use App\Models\Client;
use App\Models\ProductInfo;
use App\Models\ProductInfoCategory;
use App\Models\Sr;
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
        $costDel = 0;
        $product = new DocProduct();
        $prefix='РДС-';//По умолчанию только рдс
        if(isset($request->doctype))$prefix='РДИ-';
        if(isset($request->submit)){
            if (empty($request->number)) $number = $prefix;
            else $number = $prefix.$request->number;
            if(!empty($request->dispatch)) {
                if(empty($number=$CDEKController->getNumberByReturn($request->dispatch,$request->start,$request->finish)))
                    $number=$CDEKController->getNumberByDispatch($request->dispatch);
            }//Поиск по номеру СДЕКА
            $filter['period'] = array(
                'start' => $request->start,
                'finish' => $request->finish,
            );
            $filter['data'] = array(
                'city' => $request->city,
                'author' => $request->author,
            );
            $filter['nameLike'] = $request->name;
            $filter['number'] = $number;
            $list = $RDS->getList($page, $filter);
        }else {
            $filter=array();
            $list=$RDS->getList($page,$filter);//получаем рдэссы

        }

        $footerData=array(
            'endTotal'=>0,
            'processTotal'=>0,
            'total_price' =>0,
            'deliveryServicesCostTotal'=>0,
            'deliveryTransportCostTotal'=>0,
            'st4'=>0,
            'st5'=>0,
            'st45'=>0,
        );

        $returns=array();
        $rdsList=array();
        $rdsIDs=array();
        $rdsArrays=array();
        $rdsCount = count($list['query']);
        foreach($list['query'] as $rds){
            $rdsList[]=$rds->number;
            $rdsArrays[$rds->number][($rds->old)?'Old':'New']=['number'=>$rds->number,'doc_id'=>$rds->doc_id,'old'=>$rds->old];
            $rdsIDs[]=$rds->doc_id;
        }
        //dd($list['query']);
        $DocsByCdekIDs = array();
        if(count($rdsList)) {
            $lInfo = $CDEKController->getListInfo($rdsArrays);//отправляем сдэку список из номеров рдс. Теперь по $rdsArrays!!!
            foreach ($lInfo as $key=>$ar_value){
                foreach ($ar_value as $key_value=>$val_value){
                    $DocsByCdekIDs[] = $rdsArrays[$key][$key_value]['doc_id'];
                }
            }
            $ourShipingList = array_diff($rdsIDs,$DocsByCdekIDs);
            $goods = $RDS->getWithGcrByRdsID($rdsIDs);// получаем список товаров
            //dd($goods);
            //dd($lInfo);
            foreach ($goods as $resItem) {
                //if(isset($resCdek['Package']) && !empty($resCdek['Package'])){}
                //dd($resItem);
                $order_subfolder=$resItem['old']?'Old':'New';

                if (isset($lInfo[$resItem['number']][$order_subfolder])) {//Если через сдэк
                    $package=array();
                    if(isset($lInfo[$resItem['number']][$order_subfolder]['Package']) && !empty($lInfo[$resItem['number']][$order_subfolder]['Package'])){
                        foreach ($lInfo[$resItem['number']][$order_subfolder]['Package'] as $itemq ){
                            //dd($itemq);
                            if(isset($itemq['Item']['@attributes'])) {
                                $key = $itemq['Item']['@attributes']['WareKey'];
                                $value = $itemq['Item']['@attributes']['DelivAmount'];
                                $package[$key] = $value;
                            }
                        }
                    }
                    //dd($resItem);
                    if(!empty($resItem['ret_sku'])){//если товар возвратный, то убираем прибыль
                        $returns[$resItem['number']]=true;
                        $resItem['price']-=$resItem['ret_price'];
                        $resItem['quantity']-=$resItem['ret_quantity'];
                        $resItem['base_price']-=$resItem['ret_base_price'];
                    }

                    if (!isset($lInfo[$resItem['number']][$order_subfolder]['flag'])) {
                        $lInfo[$resItem['number']][$order_subfolder]['total_price'] = 0;
                        $lInfo[$resItem['number']][$order_subfolder]['total_base_price'] = 0;
                        $lInfo[$resItem['number']][$order_subfolder]['delivery_cost'] = 0;
                    }

                    if (!isset($lInfo[$resItem['number']][$order_subfolder]['DeliverySumTotal'])) $lInfo[$resItem['number']][$order_subfolder]['DeliverySumTotal'] = 400;
                    $delSum = $lInfo[$resItem['number']][$order_subfolder]['DeliverySumTotal'];
                    if ($lInfo[$resItem['number']][$order_subfolder]['Status']['Code'] == 5) {
                        $vozvrat = 2;
                        $lInfo[$resItem['number']][$order_subfolder]['total_price'] = 0;
                        $lInfo[$resItem['number']][$order_subfolder]['total_base_price'] = 0;
                        $lInfo[$resItem['number']][$order_subfolder]['delivery_cost'] = 0;
                    } else {
                        $vozvrat = 1;
                        if ($resItem['sku'] == 106730) {
                            $lInfo[$resItem['number']][$order_subfolder]['delivery_cost'] = $resItem['price'];
                            $costDel = $resItem['price'];
                        } else {
                            $lInfo[$resItem['number']][$order_subfolder]['delivery_cost']=0;
                            $lInfo[$resItem['number']][$order_subfolder]['real_quantity'] = (array_key_exists($resItem['sku'],$package))?$package[$resItem['sku']]:$resItem['quantity'];
                            $lInfo[$resItem['number']][$order_subfolder]['total_price'] += $resItem['price'] * $lInfo[$resItem['number']][$order_subfolder]['real_quantity'];
                            $lInfo[$resItem['number']][$order_subfolder]['total_base_price'] += $resItem['base_price']/* * $lInfo[$resItem['number']][$order_subfolder]['real_quantity']*/;
                        }
                    }

                    $lInfo[$resItem['number']][$order_subfolder]['total'] = $lInfo[$resItem['number']][$order_subfolder]['total_price']
                        - $lInfo[$resItem['number']][$order_subfolder]['total_base_price'] - $delSum
                        + $lInfo[$resItem['number']][$order_subfolder]['delivery_cost'];
                    $lInfo[$resItem['number']][$order_subfolder]['totalDel'] = $delSum;

                    $lInfo[$resItem['number']][$order_subfolder]['flag'] = 1;
                }

            }
            $nocdekResultArr = array();
            //dd($rdsIDs);
            foreach ($ourShipingList as $key => $doc_id){//////////////////////IF NO CDEK!!!!
                $products = $product->getAllWithRetByID($doc_id);// С ВОЗВРАТАМИ
                //dd([$doc_id,$products]);

                foreach($products as $item =>$val) {
                    if(!empty($val->ret_sku)){//если товар возвратный, то убираем прибыль
                        $returns[$val->doc_number]=true;
                        $val->price-=$val->ret_price;
                        $val->quantity-=$val->ret_quantity;
                        $val->base_price-=$val->ret_base_price;
                    }

                    if ($val['sku'] == 106730) {
                        $costDel = $val['price'];
                    }
                    if(!isset($nocdekResultArr[$val->doc_number])){
                        //dd($val);
                        $nocdekResultArr[$val->doc_number]['total_price']=$val->price*$val->quantity;
                        $nocdekResultArr[$val->doc_number]['total_b_price']=$val->base_price;
                    }
                    else{   $nocdekResultArr[$val->doc_number]['total_price']+=$val->price*$val->quantity;
                        $nocdekResultArr[$val->doc_number]['total_b_price']+=$val->base_price;
                    }
                    //было за циклом
                    $nocdekResultArr[$val->doc_number]['total']=$nocdekResultArr[$val->doc_number]['total_price']-$nocdekResultArr[$val->doc_number]['total_b_price']+$costDel;
                }
            }
            //dd($nocdekResultArr);
            foreach ($list['query'] as &$rds) {
                // dd($list['query']);
                $order_subfolder=$rds->old?'Old':'New';
                if (isset($lInfo[$rds->number][$order_subfolder])) {
                    $rds->total = $lInfo[$rds->number][$order_subfolder]['total'];
                    $rds->total_d = $lInfo[$rds->number][$order_subfolder]['total_price']+$costDel;
                    $rds->totalDelSrvcs = $lInfo[$rds->number][$order_subfolder]['totalDel'];
                    $rds->status_code = (isset($returns[$rds->number]))?81:$lInfo[$rds->number][$order_subfolder]['Status']['Code'];
                    $rds->status_desc=$lInfo[$rds->number][$order_subfolder]['Status']['Description'];
                    $rds->reason=$lInfo[$rds->number][$order_subfolder]['Reason']['Description'];
                    $footerData['total_price']+=$rds->total_d;
                    if($rds->status_code==4 || $rds->status_code==5){
                        $footerData['endTotal']+=$rds->total;
                        $footerData['deliveryServicesCostTotal']+=$rds->totalDelSrvcs;
                        if($rds->status_code==4)$footerData['st4']++;
                        else {$footerData['st5']++;  //$lInfo[$rds->number][$order_subfolder];
                            if(isset($footerData['reason'][$rds->reason]))  $footerData['reason'][$rds->reason]++;
                            else{$footerData['reason'][$rds->reason]=1;}
                        }
                        $footerData['st45']++;
                    }
                    else $footerData['processTotal']+=$rds->total;
                }
                else{//IF NO CDEK
                    $rds->total = $nocdekResultArr[$rds->number]['total'];
                    $rds->totalDelSrvcs = 0;
                    $rds->status_code = (isset($returns[$rds->number]))?81:777;
                    $rds->status_desc='ok';
                    $rds->reason='ok';
                    $footerData['endTotal']+=$rds->total;
                    $footerData['total_price']+=$nocdekResultArr[$rds->number]['total_price']+$costDel;

                    $footerData['deliveryServicesCostTotal']+=$rds->totalDelSrvcs;
                }
            }
        }
        if(isset($footerData['reason']))arsort($footerData['reason']);
        else $footerData['reason']=array();
        if($footerData['st45']) {
            $footerData['st4'] = round($footerData['st4'] / $footerData['st45'] * 100, 2);
            $footerData['st5'] = round($footerData['st5'] / $footerData['st45'] * 100, 2);
        }
        $footerData['count_rds'] = $rdsCount;
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

    public function angryRacoon(Request $request, AngryRacoonController $racoonController){

    }

    public function searchManager(Request $request,SearchStatsController $search){
        $search->manage($request);
        $mainData=$search->getData();
        $headerData=array('data'=>'header');
        $leftsidebarData=array('data'=>'leftsidebar');
        $rightsidebarData=array('data'=>'rightsidebar');
        return view()->make('main')
            ->nest('main','child.searchManager',$mainData)
            ->nest('header', 'child.header',$headerData)
            ->nest('footer', 'child.footer')
            ->nest('leftsidebar', 'child.leftsidebar',$leftsidebarData)
            ->nest('rightsidebar', 'child.rightsidebar',$rightsidebarData);
    }
    public function getSearchResult(Request $request,SearchStatsController $search){
        $data=array('category'=>'','city'=>'');
        if(isset($request->category))$data['category']=$request->category;
        if(isset($request->city))$data['city']=$request->city;

        $result=$search->getResultHistory([$data['category'],$data['city']]);

        $headerData=array('data'=>'header');
        $leftsidebarData=array('data'=>'leftsidebar');
        $rightsidebarData=array('data'=>'rightsidebar');
        return view()->make('main')
            ->nest('main','child.searchList',['data'=>$data,'result'=>$result])
            ->nest('header', 'child.header',$headerData)
            ->nest('footer', 'child.footer')
            ->nest('leftsidebar', 'child.leftsidebar',$leftsidebarData)
            ->nest('rightsidebar', 'child.rightsidebar',$rightsidebarData);
    }

    public function productStats(Request $request,ProductInfoCategory $category,ProductInfo $brand,Client $client){
        $form['dateFirst']=isset($request->dateFirst)?$request->dateFirst:'';
        $form['dateLast']=isset($request->dateLast)?$request->dateLast:'';
        $form['city']=isset($request->city)?$request->city:'!empty!';

        $cl= new Client();
        $cities=$cl->getCities();

        $control=new ProductInfoController();
        $stats=$control->getStats($form['dateFirst'],$form['dateLast'],$form['city'],$category,$brand,$client);

        $headerData=array('data'=>'header');
        $leftsidebarData=array('data'=>'leftsidebar');
        $rightsidebarData=array('data'=>'rightsidebar');
        return view()->make('main')
            ->nest('main','child.productStats',['data'=>$stats,'form'=>$form,'cities'=>$cities])
            ->nest('header', 'child.header',$headerData)
            ->nest('footer', 'child.footer')
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
        $suffix='';
        if(isset($request->newcdek)){
            $response=$CDEK->newDelivery([$request->all()]);
            //dd($response);
            $mainData = array('response' => $response);
            $suffix='response';
        }else {
            $cdekCities=new CdekCity();
            $cities=$cdekCities->getList();

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
            $mainData = array('input' => $input,'cities'=>$cities,'packagenum' => $packagenum);
        }
        $headerData = array('data' => 'header');
        $leftsidebarData = array('data' => 'leftsidebar');
        $rightsidebarData = array('data' => 'rightsidebar');
        return view()->make('main')
            ->nest('main', 'ajax.newCDEK'.$suffix, $mainData)
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

    public function searchRating(Request $request, SrController $SR, Sr $model) {
        $arResult = $SR->getStat($model);
        $mainData = array('data' => $arResult);
        $headerData = array('data' => 'header');
        $leftsidebarData = array('data' => 'leftsidebar');
        $rightsidebarData = array('data' => 'rightsidebar');
        return view()->make('main')
            ->nest('main', 'child.sr', $mainData)
            ->nest('header', 'child.header', $headerData)
            ->nest('footer', 'child.footer')
            ->nest('leftsidebar', 'child.leftsidebar', $leftsidebarData)
            ->nest('rightsidebar', 'child.rightsidebar', $rightsidebarData);
    }
}
