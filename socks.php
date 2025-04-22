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


if (isset($_GET['s'])) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://www.gstatic.com/generate_204');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $a = array();
    preg_match('/\d{1,3}([.])\d{1,3}([.])\d{1,3}([.])\d{1,3}((:)|(\s)+)\d{1,8}/', $_GET['s'], $a);
    $proxy = $a[0];
    curl_setopt($ch, CURLOPT_PROXY, trim($proxy));
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    if ($_GET['type'] == 'http') {
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    } else {
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    }
    curl_exec($ch);
    if (curl_error($ch)) {
        echo curl_error($ch);
        echo 'DIE';
    } else {
        echo 'LIVE';
    }
    die();
}

$proxys = array();

$ckfile = getcwd() . "/cookie_" . rand(11111, 99999) . ".txt";
if (file_exists($ckfile))
    unlink($ckfile);
/* deletar cookies */
$lis = scandir(getcwd());
foreach ($lis as $v) {
    if (strpos($v, 'cookie_') !== false) {
        unlink($v);
    }
}

function getStr($string, $start, $end) {
    $str = explode($start, $string);

    $str = explode($end, $str[1]);
    return $str[0];
}

function _curl($url, $post = false, $header = '', $header_out = true, $follow_loc = true, $json = false) {
    global $ckfile;
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
    if ($json) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=UTF-8',
            'Connection: Keep-Alive',
            'Accept: application/json, text/plain, */*',
            $header,));
    } else {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Connection: Keep-Alive',
            $header,));
    }


    curl_setopt($ch, CURLOPT_COOKIESESSION, $ckfile);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
    curl_setopt($ch, CURLOPT_COOKIE, $ckfile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);

    curl_close($ch);

    return $result;
}

function gatherproxy() {
    $resultado = _curl('http://www.gatherproxy.com/subscribe/login');
    $captcha = getStr($resultado, '<span class="blue">', ' = ');
    $captcha = explode(' ', $captcha);

    $captcha[1] = strtolower($captcha[1]);
    if (!is_numeric($captcha[0])) {
        switch (strtolower($captcha[0])) {
            case 'zero':
                $captcha[2] = 0;
                break;
            case 'one':
                $captcha[0] = 1;
                break;
            case 'two':
                $captcha[0] = 2;
                break;
            case 'three':
                $captcha[0] = 3;
                break;
            case 'four':
                $captcha[0] = 4;
                break;
            case 'five':
                $captcha[0] = 5;
                break;
            case 'six':
                $captcha[0] = 6;
                break;
            case 'seven':
                $captcha[0] = 7;
                break;
            case 'eight':
                $captcha[0] = 8;
                break;
            case 'nine':
                $captcha[0] = 9;
                break;
            case 'ten':
                $captcha[0] = 10;
                break;
        }
    }
    if (!is_numeric($captcha[2])) {
        switch (strtolower($captcha[2])) {
            case 'zero':
                $captcha[2] = 0;
                break;
            case 'one':
                $captcha[2] = 1;
                break;
            case 'two':
                $captcha[2] = 2;
                break;
            case 'three':
                $captcha[2] = 3;
                break;
            case 'four':
                $captcha[2] = 4;
                break;
            case 'five':
                $captcha[2] = 5;
                break;
            case 'six':
                $captcha[2] = 6;
                break;
            case 'seven':
                $captcha[2] = 7;
                break;
            case 'eight':
                $captcha[2] = 8;
                break;
            case 'nine':
                $captcha[2] = 9;
                break;
            case 'ten':
                $captcha[2] = 10;
                break;
        }
    }
    if ($captcha[1] == 'minus' or $captcha[1] == '-') {
        $resposta = $captcha[0] - $captcha[2];
    } elseif ($captcha[1] == 'plus' or $captcha[1] == '+') {
        $resposta = $captcha[0] + $captcha[2];
    } elseif ($captcha[1] == 'multiplied' or $captcha[1] == 'x') {
        $resposta = $captcha[0] * $captcha[2];
    }
    return $resposta;
}

function socks5() {
    echo 'socks5|';
    $return = array();

    /* Socks-proxy.net */
    $resultado = _curl('https://www.socks-proxy.net/');
    $proxys = getStr($resultado, '<tbody>', '</tbody>');
    $proxys = str_replace('</tr>', "</tr>\n", $proxys);
    $e = explode("\n", $proxys);
    for ($i = 0; $i < count($e); $i++) {
        $a = explode('<td>', $e[$i]);
        $return[] = strip_tags($a[1]) . ":" . strip_tags($a[2]);
    }

    /* Gatherproxy */
    $captcha = gatherproxy();
    $resultado = _curl('http://www.gatherproxy.com/subscribe/login', "Username=gamesvaqueiro23@gmail.com&Password=c^!N?YSG&Captcha=$captcha");
    $resultado = _curl('http://www.gatherproxy.com/sockslist/countryplaintext', 'Country=Brazil&Uptime=0', '', false);
    $proxys = explode("\n", $resultado);
    foreach ($proxys as $value) {
        $return[] = $value;
    }
    $resultado = _curl('http://www.gatherproxy.com/sockslist/countryplaintext', 'Country=United States&Uptime=0', '', false);
    $proxys = explode("\n", $resultado);
    foreach ($proxys as $value) {
        $return[] = $value;
    }
    $resultado = _curl('http://www.gatherproxy.com/sockslist/countryplaintext', 'Country=France&Uptime=0', '', false);
    $proxys = explode("\n", $resultado);
    foreach ($proxys as $value) {
        $return[] = $value;
    }
    $resultado = _curl('http://www.gatherproxy.com/sockslist/countryplaintext', 'Country=Russia&Uptime=0', '', false);
    $proxys = explode("\n", $resultado);
    foreach ($proxys as $value) {
        $return[] = $value;
    }
    $resultado = _curl('http://www.gatherproxy.com/sockslist/countryplaintext', 'Country=Sweden&Uptime=0', '', false);
    $proxys = explode("\n", $resultado);
    foreach ($proxys as $value) {
        $return[] = $value;
    }

    $return = array_filter(array_values(array_unique($return)));
    shuffle($return);
    return $return;
}

function http() {
    $return = array();
    echo 'http|';
    /* Gatherproxy */
    $captcha = gatherproxy();
    $resultado = _curl('http://www.gatherproxy.com/subscribe/login', "Username=gamesvaqueiro23@gmail.com&Password=c^!N?YSG&Captcha=$captcha");
    $resultado = _curl('http://www.gatherproxy.com/subscribe/infos');
    $sid = getStr($resultado, '<p><a href="/proxylist/downloadproxylist/?sid=', '"');
    $resultado = _curl("http://www.gatherproxy.com/proxylist/downloadproxylist/", "ID=$sid&C=&P=&T=&U=0", '', false);
    $proxys = explode("\n", $resultado);
    foreach ($proxys as $value) {
        $return[] = $value;
    }
    /* Sslproxies.org */
    $resultado = _curl('https://www.sslproxies.org/');
    $proxys = getStr($resultado, '<tbody>', '</tbody>');
    $proxys = str_replace('</tr>', "</tr>\n", $proxys);
    $e = explode("\n", $proxys);
    for ($i = 0; $i < count($e); $i++) {
        $a = explode('<td>', $e[$i]);
        $return[] = strip_tags($a[1]) . ":" . strip_tags($a[2]);
    }
    /* free-proxy-list.net */
    $resultado = _curl('https://free-proxy-list.net/');
    $proxys = getStr($resultado, '<tbody>', '</tbody>');
    $proxys = str_replace('</tr>', "</tr>\n", $proxys);
    $e = explode("\n", $proxys);
    for ($i = 0; $i < count($e); $i++) {
        $a = explode('<td>', $e[$i]);
        $return[] = strip_tags($a[1]) . ":" . strip_tags($a[2]);
    }
    return $return;
}

$array = array();
$array[] = 'socks5';
$array[] = 'http';
$return = array_unique(array_filter($array[array_rand($array)]()));

foreach ($return as $value) {
    echo $value . "\n";
}
