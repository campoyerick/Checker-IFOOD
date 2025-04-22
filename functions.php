<?php

/*
 * Projeto Ifood
 * Copie oque eu faço, Mas não pode copiar oque sei fazer
 * Copyright [#ExplicitChecker] 11/2017 by: Thiagão
 * Discord: Thiaga1#3174
 */

error_reporting(0);
set_time_limit(0);
session_start();

$ckfile = getcwd() . "/cookie_" . rand(11111, 99999) . ".txt";
if (file_exists($ckfile))
    unlink($ckfile);
/* deletar cookies */
$lis = scandir(getcwd());
foreach ($lis as $v) {
    if (strpos($v, 'cookie_') !== false) {
        if (file_exists($v))
            unlink($v);
    }
}

function _curl_proxy($url, $post = false, $header = '', $proxy = false, $header_out = true, $follow_loc = true, $json = false) {
    global $ckfile;
    $randIP = "" . mt_rand(0, 255) . "." . mt_rand(0, 255) . "." . mt_rand(0, 255) . "." . mt_rand(0, 255);
    $ch = curl_init();
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, $header_out);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow_loc);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.81 Safari/537.36");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    if (!is_array($header)) {
        $header = (array) $header;
    }
    $headers = array(
        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
        'Connection: Keep-Alive'
    );
    $headers = array_merge($headers, $header);
    $headers_j = array(
        'Content-Type: application/json; charset=UTF-8',
        'Connection: Keep-Alive'
    );
    $headers_j = array_merge($headers_j, $header);
    if ($json) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_j);
    } else {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    curl_setopt($ch, CURLOPT_COOKIESESSION, $ckfile);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
    curl_setopt($ch, CURLOPT_COOKIE, $ckfile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);


    if ($proxy) {
        $a = array();
        preg_match('/\d{1,3}([.])\d{1,3}([.])\d{1,3}([.])\d{1,3}((:)|(\s)+)\d{1,8}/', $proxy, $a);
        $proxy = $a[0];
        curl_setopt($ch, CURLOPT_PROXY, trim($proxy));
        if ($_GET['type'] == 'http') {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        } else {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        }
    }

    $result = curl_exec($ch);


    if (curl_error($ch)) {
        sock_die("<b style='color:purple'>Sock Die</b>  <b style=\"color:black\">  |  {$proxy} | " . curl_error($ch) . " </b>");
    }
    if (strpos($result, '<iframe') !== false) {
        sock_die("<b style='color:purple'>Sock Die</b>  <b style=\"color:black\">  |  {$proxy} | Captcha </b>");
    }
    $explode = explode("\r\n\r\n", $result);
    if ($header_out) {
        $body = $explode[1];
    } else {
        $body = $explode[0];
    }

    if (!json_decode($body)) {


        sock_die($result);
        sock_die("<b style='color:purple'>Sock Die</b>  <b style=\"color:black\">  |  {$proxy} | Retornou resposta desconhecida </b>");
    }

    curl_close($ch);
    return $result;
}

function getStr($string, $start, $end) {
    $str = explode($start, $string);
    $str = explode($end, $str[1]);
    return $str[0];
}

function deletar_cookie() {
    global $ckfile;
    unlink($ckfile);
}

function aprovadas($str) {
    deletar_cookie();
    $return = array("status" => 0, "str" => "$str<br>");
    echo json_encode($return, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    die();
}

function reprovadas($str) {
    deletar_cookie();
    $return = array("status" => 5, "str" => "$str<br>");
    echo json_encode($return, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    die();
}

function sock_die($str) {
    deletar_cookie();
    $return = array("status" => 56, "str" => "$str<br>");
    echo json_encode($return, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    die();
}

function cx2($str) {
    global $email;
    $fp = fopen('c.php', 'a+');
    if (strpos(file_get_contents('c.php'), $email) !== false) {

    } else {
        fwrite($fp, $str . "<br>\n");
    }
}

function multiexplode($separadores, $string) {
    $a1 = str_replace($separadores, $separadores[0], $string);
    $a2 = explode($separadores[0], $a1);
    return $a2;
}
