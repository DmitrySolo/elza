<?php
namespace App\Http\Controllers;
use App\Models\MCategoriesGroup;
use App\Models\MCity;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\Request;

class SearchStatsController extends Controller {

    static $advert,$search;
    private $cookiePath;

    function __construct(){
        $this->cookiePath=str_replace('elza.','',$_SERVER['HTTP_HOST']);
        $this->cookiePath='/home/'.$this->cookiePath.'/cookie.txt';
    }

    function getResult($text='',$city_name=''){
        $arResult=array();
        $m_group=new MCategoriesGroup();
        $groups=$m_group->getGroups();
        if(!empty($text)){
            $arResult[]=array(
                'QUERY'=>$text,
                'SEARCH'=>$this->getCitiesSearch($text,$city_name)
            );
        }else
        foreach($groups as $group){
            $arResult[]=array(
                'QUERY'=>$group->category_group_name,
                'SEARCH'=>$this->getCitiesSearch($group->category_group_name,$city_name)
            );
        }
        return($arResult);
    }

    function getCitiesSearch($text,$city_name=''){
        $arResult=array();
        $m_city=new MCity();
        $cities=$m_city->getCities();
        foreach($cities as $city){
            if(!empty($city_name)&&$city_name!=$city->region_name) continue;
            $arCity=array();
            $arCity['REGION']=$city->region_name;
            $arCity['RESULT']=$this->getSearch($text,$city->city_ya_id);
            sleep(2);
            $arCity['RESULT_CITY']=$this->getSearch($text.' '.$city->region_name,$city->city_ya_id);
            $arResult[]=$arCity;
        }
        return($arResult);
    }

    function getSearch($text,$ya_city=0){
        self::$advert=array();//реклама
        self::$search=array();//результаты поиска

        //goolemur
        $node_list=$this->_google_cr($text)->filter("cite");
        if($node_list->count())$node_list->each(function (Crawler $v, $i) {
            preg_match("/(.+?)\//", $v->text(),$val);
            $val=str_replace("www.", "", $val[1]);
            //dd($v->parents()->eq(5)->attr('id'));
            $adv = $v->parents()->eq(5)->attr('id')!='search';
            if($adv)SearchStatsController::$advert['google'][]=$val;
            else SearchStatsController::$search['google'][]=$val;
        });
        //yalemur
        $node_list=$this->_ya_cr($text,$ya_city)->filter(".serp-url");
        if($node_list->count())$node_list->each(function (Crawler $v, $i) {
            $v=$v->filter(".serp-url__link")->eq(0);
            $val=$v->text();
            $adv=$v->previousAll();
            if($adv->count()) $adv=$adv->text();
            else $adv=0;
            //dd($adv);
            if($adv)SearchStatsController::$advert['yandex'][]=$val;
            else SearchStatsController::$search['yandex'][]=$val;
        });

        return(['advert'=>SearchStatsController::$advert,'search'=>SearchStatsController::$search]);
    }


    function yaRestore(Request $request){
        if(isset($request->key)){
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
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
        }else echo $this->_ya_cr("сантехника воронеж",213)->html();
    }
    /**
     * @param $text
     * @param $city_id
     * @return Crawler
     */
    private function _ya_cr($text,$city_id=0){
        $params=array('text'=>urlencode($text));
        if($city_id)$params['lr']=$city_id;
        return $this->_curl_cr('yandex.ru/yandsearch',$params);
    }
    /**
     * @param $text
     * @return Crawler
     */
    private function _google_cr($text){
        $params=array('q'=>urlencode($text));
        return $this->_curl_cr('www.google.ru/search',$params);
    }


    /**
     * @param $site
     * @param $arParams
     * @return Crawler
     */
    private function _curl_cr($site,$arParams){
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);

        curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/20050919 Firefox/1.0.7");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiePath);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiePath);
        $out=curl_exec($ch);
        curl_close($ch);
        $data = new Crawler($out);
        return $data;
    }
}