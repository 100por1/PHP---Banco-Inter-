# PHP---Banco-Inter-
Integração da API banco Inter via PHP <br>
Seguindo documentação:
https://developers.bancointer.com.br/reference/token-1

Para criar um Token<br>
$bearer=oauthInter();<br>
Fico gerando os Tokens a cada 5 minutos e deixo salvo evita consultas a todo momento e sempre tem um disponível.<br><br>
<br>

Gerar um QR Code<br>
$pix=PixInter($id,$valor,$bearer);<br><br>

Com o QR Code criado para gerar a imagem do PIX basta por na src da imagem assim:<br>
$src = 'https://chart.apis.google.com/chart?chs=250x250&cht=qr&chl='.$pix;<br><br>

Para fazer uma transferência basta chamar assim:<br>
$cs=TransferenciaPixInter(10,'Joao Silva','local@local');<br>
Qualquer chave pix é aceita, não percisa identificar o tipo, o Inter faz isso automático.<br><br>



