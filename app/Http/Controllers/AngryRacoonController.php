<?php

namespace App\Http\Controllers;

use App\Models\ArPage;
use App\Models\ArResult;
use App\Models\ArRule;
use App\Models\ArSite;
use App\Models\ArVendor;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Symfony\Component\DomCrawler\Crawler;

class AngryRacoonController extends Controller
{
    static $get=array();

    public function test(){
        $this->getResultsByVendor(1);
    }

    public function parse($url,$classes){
        $crawler = $this->_get_crawler($url);
        $results = array();
        foreach ($classes as $class){
            if(isset($class['role'])&&isset($class['selector'])){
                AngryRacoonController::$get['result'] = '';
                $node_list = $crawler->filter($class['selector']);
                if ($node_list->count()){
                    AngryRacoonController::$get['class']=$class;
                    $node_list->each(function (Crawler $v, $i) {
                        $class = AngryRacoonController::$get['class'];
                        $result = '';
                        if($class['inner']){
                            if($class['inner']=='text')$result = $v->text();
                            if($class['inner']=='html')$result = $v->html();
                        }elseif($class['attr']){
                            $result = $v->attr($class['attr']);
                        }
                        if(isset($class['intval'])&&$class['intval'])
                            $result = intval(str_replace(' ','',$result));
                        AngryRacoonController::$get['result'] = $result;
                    });
                }
                $results[$class['role']]=AngryRacoonController::$get['result'];
            }
        }
        return $results;
    }

    public function siteAdd($city,$name,$classes){
        $db_ar_sites = new ArSite();
        return $db_ar_sites->add(array(
            'site_city' => $city,
            'site_name' => $name,
            'site_classes' => json_encode($classes)
        ));
    }

    public function vendorAdd($name,$mail,$check=false){
        $db_ar_vendors = new ArVendor();
        return $db_ar_vendors->add(array(
            'vendor_name' => $name,
            'vendor_mail' => $mail,
            'vendor_check' => $check
        ));
    }

    public function ruleAdd($vendor_id,$correct,$sku){
        $db_ar_rules = new ArRule();
        $db_ar_vendors = new ArVendor();

        if($db_ar_vendors->get($vendor_id))
        return $db_ar_rules->add(array(
            'vendor_id' => $vendor_id,
            'rule_correct' => $correct,
            'rule_sku' => $sku
        ));
        else return 0;
    }

    public function pageAdd($site_id,$url,$rule_id){
        $db_ar_pages = new ArPage();
        $db_ar_sites = new ArSite();
        $db_ar_rules = new ArRule();

        if($db_ar_sites->get($site_id)&&$db_ar_rules->get($rule_id))
            return $db_ar_pages->add(array(
                'site_id' => $site_id,
                'page_url' => $url,
                'rule_id' => $rule_id
            ));
        else return 0;
    }

    public function resultAdd($page_id,$code){
        $db_ar_results = new ArResult();
        $db_ar_pages = new ArPage();

        if($db_ar_pages->get($page_id))
            return $db_ar_results->add(array(
                'page_id' => $page_id,
                'result_code' => $code
            ));
        else return 0;
    }

    public function getResultsByVendor($vendor_id){
        $results = array();
        $db_ar_rules = new ArRule();
        $db_ar_pages = new ArPage();
        $db_ar_sites = new ArSite();
        $rules = $db_ar_rules->getByVendor($vendor_id);
        foreach ($rules as $rule) {
            $pages = $db_ar_pages->getByRule($rule->rule_id);
            foreach ($pages as $page) {
                $result_code = 1;
                $site = $db_ar_sites->get($page->site_id);
                $classes = json_decode($site->site_classes,true);

                $parse_data = $this->parse($page->page_url,$classes);
                if($parse_data) {
                    if (isset($parse_data['price'])) {
                        if(is_numeric($parse_data['price'])) {
                            if($parse_data['price'] == $rule->rule_correct) $result_code = 0;
                        }else $result_code = 4;
                    }else $result_code = 4;
                }elseif($parse_data == -1) $result_code = 3;
                else $result_code = 2;

                $results[] = array($page->page_id,$result_code);
            }
        }
        return $results;
    }

    public function scanByVendors(){
        $db_ar_results = new ArResult();
        $db_ar_vendors = new ArVendor();
        $res_ar_vendors = $db_ar_vendors->getAll();
        foreach ($res_ar_vendors as $res_vendor) {
            $results = $this->getResultsByVendor($res_vendor->vendor_id);
            foreach ($results as list($page_id, $result_code)) {
                $db_ar_results->add([
                    "page_id"=>$page_id,
                    "result_code" =>$result_code
                ]);
            }
        }
    }

    public function getDataForUser(){
        $db_ar_sites = new ArSite();
        $db_ar_vendors = new ArVendor();
        $db_ar_pages = new ArPage();
        $db_ar_rules = new ArRule();
        $db_ar_results = new ArResult();

        $arResult = array();

        $arSites = array();
        $res_ar_sites = $db_ar_sites->getAll();
        foreach ($res_ar_sites as $res_site) {
            $arSites[] = array(
                'city' => $res_site->site_city,
                'name' => $res_site->site_name
            );
        }
        $arResult['sites'] = $arSites;

        $arVendors = array();
        $res_ar_vendors = $db_ar_vendors->getAll();
        foreach ($res_ar_vendors as $res_vendor) {
            $arVendors[] = array(
                'name' => $res_vendor->vendor_name,
                'mail' => $res_vendor->vendor_mail,
                'check' => $res_vendor->vendor_check
            );
        }
        $arResult['vendors'] = $arVendors;

        $arPages = array();
        $res_ar_pages = $db_ar_pages->getAll();
        foreach ($res_ar_pages as $res_page) {
            $arPages[] = array(
                'url' => $res_page->page_url
            );
        }
        $arResult['pages'] = $arPages;

        $arRules = array();
        $res_ar_rules = $db_ar_rules->getAll();
        foreach ($res_ar_rules as $res_rule) {
            $arRules[] = array(
                'correct' => $res_rule->rule_name,
                'sku' => $res_rule->rule_mail
            );
        }
        $arResult['rules'] = $arRules;

        $arResults = array();
        $res_ar_results = $db_ar_results->getAllLatest();//todo
    }

    /**
     * @param $url
     * @return Crawler
     */
    private function _get_crawler($url){
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

    /**
     * функция для создания скрина
     * @var $url string - адрес сайта
     * @var $screen string - размер экрана, может принимать только ширину. И может принимать ширину и высоту - 1024x768
     * @var $size integer - ширина масштабированной картинки
     * @var $format string - может принимать два значения (JPEG|PNG), по умолчанию "JPEG"
     */
    private function getScreenShot($url, $screen, $size, $format = "jpeg"){
        $result = "http://mini.s-shot.ru/".$screen."/".$size."/".$format."/?".$url; // делаем запрос к сайту, который делает скрины
        $pic = file_get_contents($result); // получаем данные. Ответ от сайта
        file_put_contents("screen.".$format, $pic); // сохраняем полученную картинку
    }
}
