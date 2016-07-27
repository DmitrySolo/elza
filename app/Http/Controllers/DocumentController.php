<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CSVClient;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Document;
use App\Models\DocProduct;
use App\Models\CSVDocument;

class DocumentController extends Controller
{
    public $perPage=25;
    public function getDoc(Document $document, DocProduct $product,$number){
        $rds=$document->getWithClient($number);
        $products=$product->getAll($number);
        if($rds) {
            $arResult = array(
                'Number' => $rds->number,
                'Date' => $rds->date,
                'ClientName' => $rds->name,
                'ClientAddress' => $rds->address,
                'ClientPhone' => $rds->phone,
                'Products' => []
            );
        }else $arResult = array(
            'Number' => '',
            'Date' => '',
            'ClientName' => '',
            'ClientAddress' => '',
            'ClientPhone' => '',
            'Products' => [[
                'sku' => '',
                'name' => '',
                'quantity' => '',
                'price' => ''
            ]],
            'ERROR_DOC' => "Документ $number не найден"
        );
        if(!empty($products)) {
            foreach ($products as $prod) {
                $arResult['Products'][] = [
                    'sku' => $prod->sku,
                    'name' => $prod->name,
                    'quantity' => $prod->quantity,
                    'price' => $prod->price
                ];
            }
        }
        return $arResult;
    }

    public function import(){
        $CSVDocument=new CSVDocument();
        $CSVClient=new CSVClient();
        $client=new Client();
        $document=new Document();
        $docProduct=new DocProduct();
        $p_info=new ProductInfoController();
        $ar_products=array();
        $CSVDocument->open();
        //$i=0;
        while($data=$CSVDocument->getLine()){
            if(empty($data['product_code']))$document->import($data);
            else {
                $docProduct->import($data);
                $ar_products[]=$data['product_code'];
            }
            //if(++$i>10)break;
        }
        $p_info->updateByArray($ar_products);
        echo 'ok CSVDocument<br>';

        $CSVClient->open();
        while($data=$CSVClient->getLine()){
            $client->import($data);
        }
        echo 'ok CSVClient<br>';
    }


}
