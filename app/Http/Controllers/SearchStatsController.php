<?php
namespace App\Http\Controllers;
use App\Models\MCategoriesGroup;
use App\Models\MCategory;
use App\Models\MBrand;
use App\Models\MKeyword;
use App\Models\MRegion;
use App\Models\MCity;
use App\Models\MSite;
use App\Models\SearchHistory;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\Request;

class SearchStatsController extends Controller {

    static $google,$yandex;
    private $cookiePath;

    function __construct(){
        $this->cookiePath=str_replace('elza.','',$_SERVER['HTTP_HOST']);
        $this->cookiePath='/home/'.$this->cookiePath.'/cookie.txt';
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
        $m_site=new MSite();
        $m_site->clearPoints();
        foreach($result as $region=>$cities):
        foreach($cities as $city=>$queries):
        foreach($queries as $query=>$services):
        foreach($services as $service_name=>$service):
        foreach($service as $service_group=>$sites):
        foreach($sites as $site):
            $m_site->setSite($site,$service_name,$service_group);
        endforeach;
        endforeach;
        endforeach;
        endforeach;
        endforeach;
        endforeach;
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

    function getCitiesSearch($text,$city_name=''){
        $arResult=array();
        $m_city=new MRegion();
        $cities=$m_city->getCities();
        foreach($cities as $city){
            if(!empty($city_name)&&$city_name!=$city->city_name) continue;
            $arResult[$city->region_name][$city->city_name][$text]=$this->getSearch($text,$city->city_ya_id);
            sleep(2);
            $arResult[$city->region_name][$city->city_name][$text.' '.$city->city_name]=$this->getSearch($text.' '.$city->city_name,$city->city_ya_id);
        }
        return($arResult);
    }

    function getSearch($text,$ya_city=0){
        self::$google=array();//реклама
        self::$yandex=array();//результаты поиска

        //goolemur
        $node_list=$this->_google_cr($text)->filter("cite");
        if($node_list->count())$node_list->each(function (Crawler $v, $i) {
            //echo '***'.$v->text().'***|';
            $val=str_replace("https://", "", $v->text());
            preg_match("/(.+?)\//", $val,$matches);
            $val=str_replace("www.", "", isset($matches[1])?$matches[1]:$val);
            //dd($v->parents()->eq(5)->attr('id'));
            $adv = $v->parents()->eq(5)->attr('id')!='search';
            if($adv)self::$google['advert'][]=$val;
            else self::$google['search'][]=$val;
        });
        //yalemur
        $node_list=$this->_ya_cr($text,$ya_city)->filter(".organic__subtitle");
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

        return(['google'=>self::$google,'yandex'=>self::$yandex]);
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
        }else echo $this->_ya_cr("Сантехника Воронеж",193)->html();
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
        $data = ($out==false)?new Crawler():new Crawler($out);
        return $data;
    }
}