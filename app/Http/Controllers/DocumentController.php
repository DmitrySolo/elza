<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CSVClient;
use App\Models\CSVPnk;
use App\Models\RetProduct;
use App\Models\Returns;
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

    public function updateTracks(){
        $document=new Document();
        $cdek = new CDEKController();

        $c=0;
        $docs=$document->getWithoutTrack();
        foreach ($docs as $doc) {
            $track = $cdek->getDispatchNumber($doc->number);
            $document->setTrack($doc->number,$track);
            $doc_client = $document->getWithClient($doc->number);
            $mail = $doc_client->address;
            $name = $doc_client->name;
            $site = 'santehsmart.ru';

            if(/*!empty($mail)&&!empty($track)*/false) {
                $from_user = "=?UTF-8?B?" . base64_encode($site) . "?=";
                $subject = "=?UTF-8?B?" . base64_encode('Ваш товар передан в транспортную компанию') . "?=";

                $headers = "From: $from_user <sale@santehsmart.ru>\r\n" .
                    "MIME-Version: 1.0" . "\r\n" .
                    "Content-type: text/html; charset=UTF-8" . "\r\n";

                mail(/*'sergk393@inbox.ru'*/$mail, $subject, "
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"ru\" lang=\"ru\">
<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html;charset=UTF-8\"/>
	<style>
		body
		{
			font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
			font-size: 14px;
			color: #000;
		}
	</style>
</head>
<body>
<table cellpadding=\"0\" cellspacing=\"0\" width=\"850\" style=\"background-color: #d1d1d1; border-radius: 2px; border:1px solid #d1d1d1; margin: 0 auto;\" border=\"1\" bordercolor=\"#d1d1d1\">
	<tr>
		<td height=\"83\" width=\"850\" bgcolor=\"#eaf3f5\" style=\"border: none; padding-top: 23px; padding-right: 17px; padding-bottom: 24px; padding-left: 17px;\">
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">
				<tr>
					<td bgcolor=\"#ffffff\" height=\"75\" style=\"font-weight: bold; text-align: center; font-size: 26px; color: #0b3961;\">Ваш товар передан в транспортную компанию</td>
				</tr>
				<tr>
					<td bgcolor=\"#bad3df\" height=\"11\"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 16px; padding-left: 44px;\">
			<p style=\"margin-top:30px; margin-bottom: 28px; font-weight: bold; font-size: 19px;\">Уважаемый $name,</p>
			<p style=\"margin-top: 0; margin-bottom: 20px; line-height: 20px;\">Ваш товар отправлен в службу транспортной компании СДЭК.<br />
<br />
Ваша электронная почта: $mail<br />
<br />
Вы можете отследить за выполнением доставки своего товара с помощью трек-кода <b>$track</b> на сайте edostavka.ru.
<br />
Для отслеживания нажмите <a href=\"http://www.edostavka.ru/track.html?order_id=$track\" style=\"color:#2e6eb6;\">сюда</a>.
</p>
		</td>
	</tr>
	<tr>
		<td height=\"40px\" width=\"850\" bgcolor=\"#f7f7f7\" valign=\"top\" style=\"border: none; padding-top: 0; padding-right: 44px; padding-bottom: 30px; padding-left: 44px;\">
			<p style=\"border-top: 1px solid #d1d1d1; margin-bottom: 5px; margin-top: 0; padding-top: 20px; line-height:21px;\">С уважением,<br />администрация <a href=\"http://$site\" style=\"color:#2e6eb6;\">Интернет-магазина</a><br />
				E-mail: <a href=\"mailto:sale@santehsmart.ru\" style=\"color:#2e6eb6;\">sale@santehsmart.ru</a>
			</p>
		</td>
	</tr>
</table>
</body>
</html>", $headers);
            }

            $c++;
        }

        echo "Updated $c track(s)<br>";
    }

    public function import(){
        $CSVDocument=new CSVDocument();
        $CSVClient=new CSVClient();
        $CSVPnk=new CSVPnk();
        $client=new Client();
        $document=new Document();
        $docProduct=new DocProduct();
        $returns=new Returns();
        $retProduct=new RetProduct();
        $p_info=new ProductInfoController();
        $ar_products=array();
        $CSVDocument->open();
        //$i=0;

        $doc_id=0;
        $doc_number='';
        while($data=$CSVDocument->getLine()){
            if(empty($data['product_code'])){
                $doc_id=$document->import($data);
                $doc_number=$data['document_number'];
            }elseif($doc_number==$data['document_number']) {
                $docProduct->import($data,$doc_id);
                $ar_products[]=$data['product_code'];
            }else echo "error $doc_number id=$doc_id<br>";
            //if(++$i>10)break;
        }
        $p_info->updateByArray($ar_products);
        $this->updateTracks();
        echo 'ok CSVDocument<br>';

        $CSVClient->open();
        while($data=$CSVClient->getLine()){
            $client->import($data);
        }
        echo 'ok CSVClient<br>';

        $CSVPnk->open();
        while($data=$CSVPnk->getLine()){
            if(empty($data['product_code']))$returns->import($data);
            else {
                $retProduct->import($data);
                //$ar_products[]=$data['product_code'];
            }
            //if(++$i>10)break;
        }
        //$p_info->updateByArray($ar_products);
        echo 'ok CSVPnk<br>';
    }


}
