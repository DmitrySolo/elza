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
        dd($results);
    }

    /**
     * @param $text
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
}
