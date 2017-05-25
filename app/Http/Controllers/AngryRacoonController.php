<?php

namespace App\Http\Controllers;

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
        dd($this->ruleAdd(4,132,1234));
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
        $ar_sites = new ArSite;
        return $ar_sites->add(array(
            'site_city' => $city,
            'site_name' => $name,
            'site_classes' => json_encode($classes)
        ));
    }

    public function vendorAdd($name,$mail,$check=false){
        $ar_vendors = new ArVendor();
        return $ar_vendors->add(array(
            'vendor_name' => $name,
            'vendor_mail' => $mail,
            'vendor_check' => $check
        ));
    }

    public function ruleAdd($vendor_id,$correct,$sku){
        $ar_rules = new ArRule();
        $ar_vendors = new ArVendor();
        if($ar_vendors->get($vendor_id))
        return $ar_rules->add(array(
            'vendor_id' => $vendor_id,
            'rule_correct' => $correct,
            'rule_sku' => $sku
        ));
        else return 'errrrrrorrrr!!!';
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
