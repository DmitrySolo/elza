<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CSVPnk extends Model{

    private $path="/mnt/.autofs/ressrv/inout/1CPHPSITE/SITEMANAGER/elza_pnk.csv";
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

        preg_match('/РД.+-[0-9]+/',mb_strtoupper($arResult['document_description']),$matches);
        //dd($matches);
        $arResult['docu_number']=isset($matches[0])?$matches[0]:'';
        if(empty($arResult['docu_number'])){
            preg_match('/[0-9][0-9][0-9][0-9]/',mb_strtoupper($arResult['document_description']),$matches);
            $arResult['docu_number']=isset($matches[0])?'РДС-00'.$matches[0]:'';
        }
        if(empty($arResult['docu_number'])){//for future
            preg_match('/[0-9][0-9][0-9][0-9][0-9]/',mb_strtoupper($arResult['document_description']),$matches);
            $arResult['docu_number']=isset($matches[0])?'РДС-0'.$matches[0]:'';
        }
        /*if(empty($arResult['docu_number'])){//additional giving
            if(!empty($arResult['document_client'])&&$arResult['document_client']!=3) {
                $db_docs = new Document();
                $db_res=$db_docs->getByClientId($arResult['document_client']);
                dd($db_res);
            }
        }*/
        //$arResult['docu_number']=preg_replace('/.+РДС/','РДС',mb_strtoupper($arResult['document_description']));
        //$arResult['docu_number']=preg_replace('/\ ОТ.+/','',$arResult['docu_number']);
        //dd($arResult);

        return $arResult;
    }

    public function close(){
        fclose($this->file);
    }
}
