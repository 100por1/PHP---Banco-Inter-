<?php
$inter = json_decode(file_get_contents('php://input'));
if(strtoupper($_SERVER['REQUEST_METHOD'])=='POST' AND isset($inter->pix)){
    require("config-inter-pix.php");
    if($inter->pix[0]->chave==$chave_pix){
      $txid = $inter->pix[0]->txid; #Esse vai ser o criado no inicio com: md5($c.'123456'), usa para validar a operação.
      $valor_pago=$inter->pix[0]->valor;
      $endToEndId=$inter->pix[0]->endToEndId;
      
      Seu codigo aqui...
    }
}
?>
