<?php            
const API_KEY = "vTvt9l-U5rJE8ptJjCV_Xq1xEmi8ossU";
const ALIBABA_COLLECTION_NAME = "alibaba";
const MONGOLAB_ALIBABA_URL = "https://api.mongolab.com/api/1/databases/kilaiet/collections/".ALIBABA_COLLECTION_NAME;
const ALIBABA_URL_PREFIX = "https://detail.1688.com/offer/";
const QUANTITY_ROW_SELECTOR = 'table.table-sku tr';
const QUANTITY_SELECTOR = 'td.count span';
// Pull in PHP Simple HTML DOM Parser
include("lib/simple_html_dom.php");

function getDom($url){
    $options = [CURLOPT_PROXY => "117.135.250.134:80",
        CURLOPT_HTTPPROXYTUNNEL => 1,
        CURLOPT_REFERER,"http://www.google.com",
        CURLOPT_COOKIESESSION => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1", 
        CURLOPT_CONNECTTIMEOUT => 20,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_HEADER         => true,
        CURLOPT_SSL_VERIFYPEER => false
        ]; 
    try {
        $ch = curl_init( $url ); 
        if (FALSE === $ch){
                throw new Exception('failed to initialize');
        }
        curl_setopt_array( $ch, $options ); 
        $content = curl_exec( $ch ); 
        curl_close($ch);
        if (FALSE === $content){
                throw new Exception(curl_error($ch), curl_errno($ch));
        }

        $dom = new simple_html_dom();
        $dom->load($content,true,false);
        return $dom;
    }catch(Exception $e) {
        trigger_error(sprintf(
            'Curl failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);

    }
}
$response = file_get_html(MONGOLAB_ALIBABA_URL . "?apiKey=" . API_KEY, false, $stream);
$monitorItems = json_decode($response);        
$emailContent = "test";
// For every page to check...
foreach($monitorItems as $monitorItem) {
    $url = ALIBABA_URL_PREFIX.$monitorItem->_id.".html";
    $dom = getDom($url);
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $url); 
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1); 
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
curl_setopt($ch, CURLOPT_PROXY, '101.81.30.26:8118'); 
$data = curl_exec(); 
curl_close($ch); 
    $quantities = $dom->find(QUANTITY_ROW_SELECTOR);
    foreach ($monitorItem->preOrders as $monitoredSizeName => $monitoredQuantity) {                    
        foreach($quantities as $quantity){
            $sizeName = trim($quantity->children[0]->plaintext);
            if ($sizeName == $monitoredSizeName){
                //0-name, 1-price, 2-count, 3-amount
                $row = str_get_html($quantities[0]->outertext);
                $count = $row->find(QUANTITY_SELECTOR)[0]->first_child()->plaintext;
                if ($count > 0){
                    mail("jhtsai@hotmail.com","Sites Have Changed!",$emailContent,"From: kent.tsai@kilaiet.net","\r\n");
                    // Debug
                    echo $emailContent;
                }
            }
        }
    }
}
?>
