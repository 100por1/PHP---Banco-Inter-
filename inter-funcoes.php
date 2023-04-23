<?php
function oauthInter($scope='cob.write'){
  /*
  O tempo de vida de um token gerado é de uma hora. Com isso, é possível realizar um número determinado de requisições nas apis, de acordo com o rate limit de cada api, utilizando um único token.
  Rate limit: 5 chamadas por minuto
  */
    require("config-inter-pix.php");

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://cdpj.partners.bancointer.com.br/oauth/v2/token',
        CURLOPT_SSL_VERIFYPEER=>false,
        CURLOPT_SSLKEY => $clientKey,
        CURLOPT_SSLCERT => $clientCert,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'grant_type' => 'client_credentials',
            'scope' => $scope
        ),
    ));
    $resposta = curl_exec($curl);
    $oauth = json_decode($resposta);
    curl_close($curl);
    if(isset($oauth->access_token)){
        return $oauth->access_token;
    }else{
        return false;
    }
}
function registra_webhook($chave_pix,$url_interna,$bearer){
  /*
  Escopo requerido: webhook.write
  Rate limit: 120 chamadas por minuto
  */
    require("config-inter-pix.php");
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://cdpj.partners.bancointer.com.br/pix/v2/webhook/'.$chave_pix,
    CURLOPT_SSL_VERIFYPEER=>false,
    CURLOPT_SSLKEY => $clientKey,
    CURLOPT_SSLCERT => $clientCert,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS =>'{
        "webhookUrl":"'.$url_interna.'"
    }',
    CURLOPT_HTTPHEADER => array(
        'x-conta-corrente: '.$conta_corrente,
        'Content-Type: application/json',
        'Authorization: Bearer '.$bearer
    ),
    ));

    $resposta = curl_exec($curl);
    curl_close($curl);
    return $retorno;
}
function PixInter($c,$valor,$bearer){
  /*
  Escopo requerido: cob.write
  Rate limit: 120 chamadas por minuto
  */
    require("config-inter-pix.php");

    $valor=decimal($valor);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://cdpj.partners.bancointer.com.br/pix/v2/cob/'.md5($c.'123456'),
        CURLOPT_SSL_VERIFYPEER=>false,
        CURLOPT_SSLKEY => $clientKey,
        CURLOPT_SSLCERT => $clientCert,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS =>'{
            "calendario":{
                "expiracao":86400
            },
            "valor":{
                "original":"'.$valor.'"
            },
            "chave":"'.$chave_pix.'"
        }',
        CURLOPT_HTTPHEADER => array(
            'x-conta-corrente: '.$conta_corrente,
            'Authorization: Bearer '.$bearer,
            'Content-Type: application/json'
        ),
    ));

    $resposta = curl_exec($curl);
    $cob = json_decode($resposta);
    curl_close($curl);
    if(isset($cob->pixCopiaECola)){
        return $cob->pixCopiaECola;
    }else{
        return '';
    }
}
function TransferenciaPixInter($valor,$usuario,$pix,$bearer){
    /*
    Escopo requerido: pagamento-pix.write
    Rate limit: 20 chamadas por minuto
    */
    require("config-inter-pix.php");

    $valor=decimal($valor);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://cdpj.partners.bancointer.com.br/banking/v2/pix',
        CURLOPT_SSL_VERIFYPEER=>false,
        CURLOPT_SSLKEY => $clientKey,
        CURLOPT_SSLCERT => $clientCert,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "valor":'.$valor.',
            "descricao":"PIX solicitado de '.$usuario.'",
            "destinatario":{
                "chave":"'.$pix.'",
                "tipo":"CHAVE"
            }
        }',
        CURLOPT_HTTPHEADER => array(
            'x-conta-corrente: '.$conta_corrente,
            'Authorization: Bearer '.$bearer,
            'Content-Type: application/json'
        ),
    ));

    $resposta = curl_exec($curl);
    $inter = json_decode($resposta);
    curl_close($curl);
    $retorno=array();
    if(isset($inter->codigoSolicitacao)){
        $retorno['cs']=$inter->codigoSolicitacao;
    }else{
        if(isset($inter->detail)){
            $retorno['d']=$inter->detail;
        }else $retorno['d']='Erro desconhecido, tente novamente.';
    }
    return $retorno;
}
?>
