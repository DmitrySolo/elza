<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CSVDocument extends Model{

    private $path="/mnt/.autofs/ressrv/inout/1CPHPSITE/SITEMANAGER/elza_docs.csv";
    private $file;
    private $head;

    public function open(){
        $this->file=fopen($this->path,'r');
        $this->head=fgetcsv($this->file,384,'^');
    }

    public function getLine(){
        $arr=fgetcsv($this->file,384,'^');
        if($arr==false){
            $this->close();
            return false;
        }
        $arResult=array();
        foreach($this->head as $num=>$key){
            $arResult[$key]=$arr[$num];
        }
        $arResult['document_old']=true;
        if(isset($arResult['document_date'])){
            if(intval(substr($arResult['document_date'],0,4))>=2017)$arResult['document_old']=false;
        }
        return $arResult;
    }

    public function close(){
        fclose($this->file);
    }
}
