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
function PixInter($c,$valor,$bearer){
  /*
  Escopo requerido: cob.write
  Rate limit: 120 chamadas por minuto
  */
    require("config-inter-pix.php");

    $valor=decimal($valor);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://cdpj.partners.bancointer.com.br/pix/v2/cob/'.md5($c.'100p1'),
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
            "descricao":"Saque de '.$usuario.'",
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
        }else $retorno['d']='Verifique seus dados PIX, erro efetuando transferência';
    }
    return $retorno;
}
?>
