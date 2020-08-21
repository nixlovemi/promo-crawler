<?php
# error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ERROR | E_PARSE);

function get_web_page( $url ) {
    $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

    $options = array(

        CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
        CURLOPT_POST           =>false,        //set to GET
        CURLOPT_USERAGENT      => $user_agent, //set user agent
        # CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
        # CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        CURLOPT_HTTPHEADER     => array(
            ":authority" => "www.americanas.com.br",
            ":method" => "GET",
            // ":path" => "/produto/1521479897",
            ":scheme" => "https",
        )
    );

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;
    return $header;
}
function acerta_moeda($str) {
    $str = trim($str);

    if (strlen($str) <= 0) {
        return null;
    }

    $str = str_replace(".", "", $str);
    $str = str_replace(",", ".", $str);
    $str = str_replace("R$", "", $str);
    $str = str_replace("US$", "", $str);
    $str = str_replace("U$", "", $str);
    $str = str_replace("$", "", $str);
    $str = str_replace(" ", "", $str);
    return $str;
}

# $url = 'https://www.americanas.com.br/produto/1521479897/fritadeira-sem-oleo-mondial-af-34-3-2l-1270w-preto?voltagem=220V';
# $url = 'https://www.americanas.com.br/produto/134409872/fritadeira-eletrica-sem-oleo-air-fryer-mondial-af-29-family-iii-3-5l-preta-com-timer?api=b2wads&chave=b2wads_5ef284de236b98000f5dda7b_8829865000339_134409872_7f8d9758-d39e-4b07-b9fb-814d9a8da181&pos=6&sellerId=8829865000339&sellerName=Loja%20Pro4ce%20Sp&voltagem=110V';
$url = 'https://www.americanas.com.br/produto/132341894/';

$ret = get_web_page($url);
if($ret['http_code'] == 200) {
    $html = $ret['content'] ?? '';

    # header("Content-Type: text/plain");
    # echo $html; die;

    $doc  = new DOMDocument();
    $doc->loadHTML($html);

    $nomeProd  = $doc->getElementById('product-name-default')->textContent ?? NULL;
    $finder    = new DomXPath($doc);
    $classname = "price__SalesPrice";
    $nodes     = $finder->query("//*[contains(@class, '$classname')]");
    $valorProd = $nodes[0]->textContent ?? NULL;
    $valorProd = ($valorProd !== NULL) ? acerta_moeda($valorProd): $valorProd;

    var_dump($nomeProd, $valorProd);

    // procura outros produtos na mesma página
    # $classname2 = "product-card__WrapperInfo";
    # $nodes2     = $finder->query("//*[contains(@class, '$classname2')]");
    # foreach($nodes2 as $nodesChild) {
        # var_dump($nodesChild->childNodes[0]); die;
        # $classname3 = 'product-card__ProductName';
        # $nodeNome   = $finder->query("//*[contains(@class, '$classname3')]", $nodesChild)[0];
        # var_dump($nodeNome);
    # }
    // =======================================
}
