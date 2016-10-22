<?php
namespace App\Http\Controllers;
use App\Models\MCategoriesGroup;
use App\Models\MCategory;
use App\Models\MBrand;
use App\Models\MKeyword;
use App\Models\MRegion;
use App\Models\MCity;
use App\Models\MSite;
use App\Models\ProxyList;
use App\Models\SearchHistory;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\Request;
//use App\Additional\AngryCurl;

class SearchStatsController extends Controller {

    static $google,$yandex;
    private $cookiePath;
    private $proxyList,$proxyCount,$proxyPos=0;
    private $agentList,$agentCount,$agentPos=4573;
    //private $AC;

    function __construct(){
        $user=str_replace('elza.','',$_SERVER['HTTP_HOST']);
        if($user=='local')$user='inadmin';

        $this->cookiePath="/home/$user/cookie.txt";
        //$this->AC=new AngryCurl('callback_function');
        $proxy=new ProxyList();
        $this->proxyList=$proxy->getProxyList();
        $this->proxyCount=count($this->proxyList);
        $agent_path="/home/$user/www/elza/app/additional/user_agent.txt";
        if($user=='inadmin')$agent_path="/home/$user/www/elza.in/elza/app/additional/user_agent.txt";
        $agents=file_get_contents($agent_path);
        $this->agentList=explode("\n",$agents);
        $this->agentCount=count($this->agentList);
    }

    function clearCookies(){
        unlink($this->cookiePath);
    }

    function manage(Request $request){
        $m_keyword=new MKeyword();
        $m_group=new MCategoriesGroup();
        $m_category=new MCategory();
        $m_brand=new MBrand();
        $m_region=new MRegion();
        $m_city=new MCity();

        if(!empty($request->new_keyword))$m_keyword->newKeyword($request->new_keyword);
        if(!empty($request->delete_keyword))$m_keyword->deleteKeyword($request->delete_keyword);
        $group_id=$request->category_group_id;
        if(!empty($request->new_category_group))$group_id=$m_group->newGroup($request->new_category_group);
        if($group_id && !empty($request->new_category))$m_category->newCategory($group_id,$request->new_category);
        if(!empty($request->delete_category_group))$m_group->deleteGroup($request->delete_category_group);
        if(!empty($request->delete_category))$m_category->deleteCategory($request->delete_category);
        if(!empty($request->new_brand))$m_brand->newBrand($request->new_brand);
        if(!empty($request->delete_brand))$m_brand->deleteBrand($request->delete_brand);
        $region_id=$request->region_id;
        if(!empty($request->new_region))$region_id=$m_region->newRegion($request->new_region);
        if($region_id && !empty($request->new_city))$m_city->newCity($region_id,$request->new_city);
        if(!empty($request->delete_region)){
            $m_region->deleteRegion($request->delete_region);
            if(!empty($request->delete_region_cities))$m_city->deleteCityByRegion($request->delete_region);
        }
        if(!empty($request->delete_city))$m_city->deleteCity($request->delete_city);
    }

    function getData(){
        $data=array();

        $m_keyword=new MKeyword();
        $m_group=new MCategoriesGroup();
        $m_category=new MCategory();
        $m_brand=new MBrand();
        $m_region=new MRegion();
        $m_city=new MCity();

        $data['keywords']=$m_keyword->getKeywords();
        $data['groups']=$m_group->getGroups();
        $data['categories']=$m_category->getCategories();
        $data['brands']=$m_brand->getBrands();
        $data['regions']=$m_region->getRegions();
        $data['cities']=$m_city->getCitiesWithRegions();

        return $data;
    }

    function saveResult($params,$result){
        $params=implode('|',$params);
        $res=json_encode($result, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        $search=new SearchHistory();
        $search->set(['search_parameters'=>$params,'search_result'=>$res]);
        $this->saveStats($result);
    }
    function saveStats($result){
        $m_site=new MSite();
        $m_site->clear();
        foreach($result as $region=>$cities):
            foreach($cities as $city=>$queries):
                foreach($queries as $query=>$services):
                    foreach($services as $service_name=>$service):
                        foreach($service as $service_group=>$sites):
                            foreach($sites as $site):
                                $m_site->setSite(trim($site),$service_name,$service_group);
                            endforeach;
                        endforeach;
                    endforeach;
                endforeach;
            endforeach;
        endforeach;
    }
    public function searchStats(Request $request){
        $data=array('category'=>'','city'=>'');
        if(isset($request->category))$data['category']=$request->category;
        if(isset($request->city))$data['city']=$request->city;

        $result=$this->getResultHistory([$data['category'],$data['city']]);
        $this->saveStats($result);

        return 'ok';
    }

    function getResultHistory($params){
        $params=implode('|',$params);
        $search=new SearchHistory();
        return json_decode($search->get($params)->search_result,true);
    }

    function getResult($text='',$city_name=''){
        $arResult=array();
        $m_group=new MCategoriesGroup();
        $groups=$m_group->getGroups();
        $m_keyword=new MKeyword();
        $keywords=$m_keyword->getKeywords();
        $m_brand=new MBrand();
        $brands=$m_brand->getBrands();
        if(!empty($text))$arResult=array_merge_recursive($arResult,$this->getCitiesSearch($text,$city_name));
        else{
            foreach($groups as $group) {
                $arResult = array_merge_recursive($arResult, $this->getCitiesSearch($group->category_group_name, $city_name));
            }
            foreach($brands as $brand) {
                $arResult = array_merge_recursive($arResult, $this->getCitiesSearch($brand->brand_name, $city_name));
            }
            foreach($keywords as $keyword){
                foreach($groups as $group) {
                    $arResult = array_merge_recursive($arResult, $this->getCitiesSearch($group->category_group_name.' '.$keyword->keyword_name, $city_name));
                }
                foreach($brands as $brand) {
                    $arResult = array_merge_recursive($arResult, $this->getCitiesSearch($brand->brand_name.' '.$keyword->keyword_name, $city_name));
                }
            }
        }
        return($arResult);
    }

    function getSearchSteps(Request $request){
        $arResult=array();
        $arResult['date']=date('Y-m-d H:i:s');
        if(isset($request->category)&&isset($request->city)){
            $arResult['params']=['category'=>$request->category,'city'=>$request->city];
            $m_group=new MCategoriesGroup();
            $groups=$m_group->getGroups();
            $m_keyword=new MKeyword();
            $keywords=$m_keyword->getKeywords();
            $m_brand=new MBrand();
            $brands=$m_brand->getBrands();
            if(!empty($request->category))$arResult['steps'][]=['text'=>$request->category,'city_name'=>$request->city];
            else{
                foreach($groups as $group) {
                    $arResult['steps'][]=['text'=>$group->category_group_name,'city_name'=>$request->city];
                }
                foreach($brands as $brand) {
                    $arResult['steps'][]=['text'=>$brand->brand_name,'city_name'=>$request->city];
                }
                foreach($keywords as $keyword){
                    foreach($groups as $group) {
                        $arResult['steps'][]=['text'=>$group->category_group_name.' '.$keyword->keyword_name,'city_name'=>$request->city];
                    }
                    foreach($brands as $brand) {
                        $arResult['steps'][]=['text'=>$brand->brand_name.' '.$keyword->keyword_name,'city_name'=>$request->city];
                    }
                }
            }
        }
        return($arResult);
    }

    function runSearchStep(Request $request){
        if(isset($request->text)&&isset($request->city_name)&&isset($request->date)){
            $this->_init_agent();
            $this->_init_proxy();
            $result=$this->getCitiesSearch($request->text,$request->city_name);
            if(isset($result['error'])) return $result;
            else{
                $sh = new SearchHistory();
                $sh->setToDate($request->date,['search_parameters'=>'|'/*implode('|',['category'=>'','city'=>''])*/,'search_result'=>$result]);
                return ['status'=>'success'];
            }
        }
        return ['error'=>'not found'];
    }

    function getCitiesSearch($text,$city_name=''){
        $arResult=array();
        $m_city=new MRegion();
        $cities=$m_city->getCities();
        foreach($cities as $city){
            if(!empty($city_name)&&$city_name!=$city->city_name) continue;
            $result=$this->getSearch($text,$city->city_ya_id);
            if(isset($result['error']))return $result;
            $arResult[$city->region_name][$city->city_name][$text]=$result;
            sleep(2);
            $result=$this->getSearch($text.' '.$city->city_name,$city->city_ya_id);
            if(isset($result['error']))return $result;
            $arResult[$city->region_name][$city->city_name][$text.' '.$city->city_name]=$result;
        }
        return($arResult);
    }

    function getSearch($text,$ya_city=0){
        self::$google=array();//реклама
        self::$yandex=array();//результаты поиска

        //goolemur
        $err=0;
        $node_list=new Crawler();
        //while ($err<15) {
            $crawler = $this->_google_cr($text);
            if (!$crawler->count()) return ['error' => ['google' => 'empty']];
            $node_list = $crawler->filter("cite");
            if (!$node_list->count()) {
                //$this->_init_agent();
                //$this->_init_proxy();
                //$err++;
                return ['error' => ['google' => 'empty']];
            }
        //}
        //if($err>=15) return ['error' => ['google' => 'empty']];
        $node_list->each(function (Crawler $v, $i) {
            //echo '***'.$v->text().'***|';
            $val=str_replace("https://", "", $v->text());
            preg_match("/(.+?)\//", $val,$matches);
            $val=str_replace("www.", "", isset($matches[1])?$matches[1]:$val);
            preg_match("/(.+?)\ \›/", $val,$matches);
            $val=isset($matches[1])?$matches[1]:$val;
            //dd($v->parents()->eq(5)->attr('id'));
            $adv = $v->parents()->eq(5)->attr('id')!='search';
            if($adv)self::$google['advert'][]=$val;
            else self::$google['search'][]=$val;
        });
        //yalemur
        $crawler=$this->_ya_cr($text,$ya_city);
        if(!$crawler->count())return ['error'=>['yandex'=>'empty']];
        if($crawler->filter(".form_error_no")->count()){
            $arrErr=['error'=>['yandex'=>$crawler->html()]];
            $arrErr['img']=$crawler->filter(".image.form__captcha")->attr('src');
            $arrErr['key']=$crawler->filter(".form__key")->attr('value');
            $arrErr['retpath']=$crawler->filter(".form__retpath")->attr('value');
            return $arrErr;
        }
        $node_list=$crawler->filter(".organic__subtitle");
        if($node_list->count())$node_list->each(function (Crawler $v, $i) {
            $v=$v->filter(".organic__path")->eq(0);
            $adv=$v->previousAll();
            if($adv->count()) $adv=$adv->text();
            else $adv=0;
            $v=$v->filter(".link")->eq(0);
            $val=str_replace("https://", "", $v->text());
            preg_match("/(.+?)\//", $val,$matches);
            $val=str_replace("www.", "", isset($matches[1])?$matches[1]:$val);
            //dd($adv);
            if($adv)self::$yandex['advert'][]=$val;
            else self::$yandex['search'][]=$val;
        });
        //yalemur
        $node_list=$crawler->filter(".serp-url");
        if($node_list->count())$node_list->each(function (Crawler $v, $i) {
            $v=$v->filter(".serp-url__link")->eq(0);
            $adv=$v->previousAll();
            if($adv->count()) $adv=$adv->text();
            else $adv=0;
            $val=str_replace("https://", "", $v->text());
            preg_match("/(.+?)\//", $val,$matches);
            $val=str_replace("www.", "", isset($matches[1])?$matches[1]:$val);
            //dd($adv);
            if($adv)self::$yandex['advert'][]=$val;
            else self::$yandex['search'][]=$val;
        });

        return(['google'=>self::$google,'yandex'=>self::$yandex]);
    }


    function yaRestore(Request $request){
        /*$this->_init_agent();
        echo 'poosss=',$this->agentPos;*/
        //dd($request);
        if(isset($request->key)){
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_BINARYTRANSFER,true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,false);

            curl_setopt($curl, CURLOPT_USERAGENT , "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/20050919 Firefox/1.0.7");
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

            curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookiePath);
            curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookiePath);

            curl_setopt($curl, CURLOPT_URL, 'http://yandex.ru/checkcaptcha?key='.urlencode($request->key).'&retpath='.urlencode($request->retpath).'&rep='.$request->rep);
            $out=curl_exec($curl);

            echo $out;

            curl_close($curl);
        }else echo $this->_ya_cr("Сантехника Воронеж",193)->html();
        //}else echo $this->_google_cr("Сантехника Воронеж")->html();
    }
    /**
     * @param $text
     * @param $city_id
     * @return Crawler
     */
    private function _ya_cr($text,$city_id=0){
        $arParams=array('text'=>urlencode($text));
        if($city_id)$arParams['lr']=$city_id;

        $site='yandex.ru/yandsearch';
        $url="http://$site";
        $first=true;
        foreach($arParams as $key=>$val){
            if($first){
                $url.='?';
                $first=false;
            }else $url.='&';
            $url.="$key=$val";
        }
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);

        curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/20050919 Firefox/1.0.7");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiePath);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiePath);

        $out=curl_exec($ch);
        //if($out==false)dd(curl_error($ch));
        curl_close($ch);
        $data = ($out==false)?new Crawler():new Crawler($out);
        return $data;
    }
    /**
     * @param $text
     * @return Crawler
     */
    private function _google_cr($text){
        $arParams=array('q'=>urlencode($text));

        $site='www.google.ru/search';
        $url="http://$site";
        $first=true;
        foreach($arParams as $key=>$val){
            if($first){
                $url.='?';
                $first=false;
            }else $url.='&';
            $url.="$key=$val";
        }
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch, CURLOPT_TIMEOUT,15);

        /*curl_setopt($ch, CURLOPT_PROXY, $this->_get_proxy());
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);*/

        //curl_setopt($ch, CURLOPT_USERAGENT , $this->_get_agent());
        curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/20050919 Firefox/1.0.7");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $out=curl_exec($ch);
        //if($out==false)dd(curl_error($ch));
        curl_close($ch);
        $data = ($out==false)?new Crawler():new Crawler($out);
        return $data;
    }

    private function _init_proxy(){
        $this->proxyPos=rand(0,$this->proxyCount-1);
    }

    private function _init_agent(){
        $this->agentPos=rand(0,$this->agentCount-1);
    }

    private function _get_proxy(){
        $proxy=$this->proxyList[$this->proxyPos];
        $ip=$proxy->proxy_ip;
        $port=$proxy->proxy_port;
        return "$ip:$port";
    }

    private function _get_agent(){
        return $this->agentList[$this->agentPos];
    }
}