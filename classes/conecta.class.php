<?php
class mysqlConn{

    // CONSTANTES DE CONEXÃO
    private $user = "root"; // usuário do BD
    private $senha = "xpto11"; // senha do BD
    private $base  = "viabusca"; // BD*/
    private $server = "localhost"; // servidor do BD*/

    /*private $user = "mandalae"; // usuário do BD
    private $senha = "mandala123"; // senha do BD
    private $base  = "mandalae_nova"; // BD*/

    // MONTA SETTERS E GETTERS
    public function __call($metodo, $parametros){
            // Selecionando os 3 primeiros caracteres do método chamado
            $prefixo  = substr($metodo, 0, 3);
            $variavel = substr($metodo, 4);

            // se for set*, "seta" um valor para a propriedade
            if( $prefixo == 'set' ) {
                    $this->$variavel = $parametros[0];
            }
            // se for get*, retorna o valor da propriedade
            elseif( $prefixo  == 'get' ) {
                    return $this->$variavel;
            }
            // Retorna exception dizendo que não existe o método chamada
            else {
                    throw new Exception('O método ' . $metodo . ' não existe!');
            }
    }

    // CONSTRUTOR
    public function __construct(){
        $this->con = new PDO("mysql:host=$this->server;dbname=$this->base", $this->user, $this->senha);

        $mtime = microtime();
        $mtime = explode(" ",$mtime);
        $mtime = $mtime[1] + $mtime[0];
        $this->starttime = $mtime;
    }

    public function tempo_carrega_fim(){
        $mtime = microtime();
        $mtime = explode(" ",$mtime);
        $mtime = $mtime[1] + $mtime[0];
        $endtime = $mtime; // Finaliza a vari�vel de contagem do tempo de gera��o da p�gina.
        $totaltime = ($endtime - $this->starttime); // � feita a contagem do tempo total que a p�gina levou para ser gerada.
        $saida = "<p align=\"center\">Página carregada em: ". round($totaltime,7) ." segundo(s).</p>"; // Mostra o tempo que a p�gina levou para ser gerada.

        return $saida;
    }

    // FAZ A CONSULTA NO BANCO DE DADOS
    public function consulta($sql){
            $saida = $this->con->query($sql);
            return $saida;
    }

    // VERIFICA SE EMAIL JÁ ESTÁ CADASTRADO
    public function duplicado(){
            $sql = "SELECT ".$this->getCampos()." FROM ".$this->getTabela()." WHERE ".$this->getCampos()." = '".$this->getDuplicado()."'";
            $qr  = $this->consulta($sql);
            return $qr->rowCount();
    }

    // CONTA O TOTAL DE REGISTROS
    public function totalRegistros($sql){
            $qr = $this->consulta($sql);
            $num = $qr->rowCount();

            return $num;
    }

    // ULTIMO DADO INSERIDO
    public function ultimo_registro(){
        $sql = "SELECT ".$this->getCampos()." FROM ".$this->getTabela()." WHERE ".$this->getValores()." ORDER BY ".$this->getCdg()." DESC";
        $qr = $this->consulta($sql);
        $row = $qr->fetch(PDO::FETCH_ASSOC);

        $saida = $row[$this->getCdg()];

        return $saida;
    }

    // EXECUTE AÇÃO NO BANCO DE DADOS (UPDATE, INSERT, DELETE);
    public function executa(){
            switch($this->getAcao()){
                    case 'insert':
                            $sql = "INSERT INTO ".$this->getTabela()." (".$this->getCampos().") VALUES (".$this->getValores().")";
                            break;
                    case 'update':
                            $sql = "UPDATE ".$this->getTabela()." SET ".$this->getValores()." WHERE ".$this->getCdg();
                            break;
                    case 'delete':
                            $sql = "DELETE FROM ".$this->getTabela()." WHERE ".$this->getCdg();
                            break;
            }
            $saida = $this->con->exec($sql);
            if(!$saida){
                return $this->con->errorInfo();
            }else{
                return $saida;
            }
    }

    public function contador(){
        if($_SESSION['contador'] != ""){
            $dt = date("Y-m-d");
            $hr = date("H:i:s");
            $ip = $_SERVER['REMOTE_ADDR'];
            $sql = "UPDATE tbl_acesso SET acessos = acessos + 1, data = '".date("Y-m-d")."', hora = '".date("H:i:s")."', ip = '".$_SERVER['REMOTE_ADDR']."'";
            $this->consulta($sql);
            $_SESSION['contador'] = 'sim';
        }
    }

    // Configura DATA para PHP ou MySQL
    public function data_mysql_php($data,$tipo="mysql"){
                if($tipo == "mysql"){
                    $saida =  implode("-",array_reverse(explode("/",$data)));
                }elseif($tipo == "php"){
                    $saida = implode("/",array_reverse(explode("-",$data)));
                }

                return $saida;
    }

    // CRIPTOGRAFA SENHA
    public function cryptsword($user,$senha){
            $saida = sha1(md5($user).":".md5($senha));
            return $saida;
    }

    public function removerAcento($str){
        $from = 'ÀÁÃÂÉÊÍÓÕÔÚÜÇàáãâéêíóõôúüç';
        $to   = 'AAAAEEIOOOUUCaaaaeeiooouuc';
        return strtr($str, $from, $to);
    }


    // Faz a Paginação
    public function monta_paginacao(){
        $this->pag = filter_var($_GET['pag'], FILTER_VALIDATE_INT);

        $this->inicio = 0;
        $this->limite = $this->cfg_numprod;

        if($this->pag != ""){
            $this->inicio = ($this->pag - 1) * $this->limite;
        }else{
            $this->pag = 1;
        }

        $sql_total = "SELECT COUNT(*) AS total FROM ".$this->getTable()." ".$this->getWhere();
        $qr_t = $this->consulta($sql_total);
        $rt = $qr_t->fetch(PDO::FETCH_ASSOC);
        $this->total = $rt['total'];
    }

     public function paginacao(){
        $prox = $this->pag + 1;
        $ant = $this->pag - 1;
        $ultima_pagina = ceil($this->total / $this->limite);
        $penultima = $ultima_pagina - 1;
        $adjacentes = 2;

        if($this->total > $this->limite){

        $saida = "<div class=\"paginacao\">\n";

        if($this->pag > 1){
            $saida .= "<a href=\"?fab=".$_GET['fab']."&cat=".$_GET['cat']."&catpai=".$_GET['catpai']."&pag=".$ant."\">&laquo;</a>\n";
        }

        if($ultima_pagina <= 5){
            for($i=1;$i< ($ultima_pagina+1);$i++){
                if($i == $this->pag){
                    $saida .= "<a class=\"atual\" name=\"$i\">$i</a>\n";
                }else{
                    $saida .= "<a href=\"?fab=".$_GET['fab']."&cat=".$_GET['cat']."&catpai=".$_GET['catpai']."&pag=$i\">$i</a>\n";
                }
            }
        }

        if($ultima_pagina > 5){
            if($this->pag < 1 + (2 * $adjacentes)){
                for($i=1;$i< 2+(2 * $adjacentes); $i++){
                    if($i == $this->pag){
                        $saida .= "<a class=\"atual\" name=\"$i\">$i</a>\n";
                    }else{
                        $saida .= "<a href=\"?fab=".$_GET['fab']."&cat=".$_GET['cat']."&catpai=".$_GET['catpai']."&pag=$i\">$i</a>\n";
                    }
                }

                $saida .= "...";
                $saida .= "<a href=\"?fab=".$_GET['fab']."&cat=".$_GET['cat']."&catpai=".$_GET['catpai']."&pag=$penultima\">$penultima</a>\n";
                $saida .= "<a href=\"?fab=".$_GET['fab']."&cat=".$_GET['cat']."&catpai=".$_GET['catpai']."&pag=$ultima_pagina\">$ultima_pagina</a>\n";

            }elseif($this->pag > (2 * $adjacentes) && $this->pag < $ultima_pagina - 3){
                $saida .= "<a href=\"?fab=".$_GET['fab']."&cat=".$_GET['cat']."&catpai=".$_GET['catpai']."&pag=1\">1</a>\n";
                $saida .= "<a href=\"?fab=".$_GET['fab']."&cat=".$_GET['cat']."&catpai=".$_GET['catpai']."&pag=2\">2</a> ... \n";

                for($i=($this->pag - $adjacentes);$i <= ($this->pag+$adjacentes);$i++){
                    if($i == $this->pag){
                        $saida .= "<a class=\"atual\" name=\"$i\">$i</a>\n";
                    }else{
                        $saida .= "<a href=\"?fab=".$_GET['fab']."&cat=".$_GET['cat']."&catpai=".$_GET['catpai']."&pag=$i\">$i</a>\n";
                    }
                }

                $saida .= "...";
                $saida .= "<a href=\"?fab=".$_GET['fab']."&cat=".$_GET['cat']."&catpai=".$_GET['catpai']."&pag=$penultima\">$penultima</a>\n";
                $saida .= "<a href=\"?fab=".$_GET['fab']."&cat=".$_GET['cat']."&catpai=".$_GET['catpai']."&pag=$ultima_pagina\">$ultima_pagina</a>\n";
            }else{
                $saida .= "<a href=\"?fab=".$_GET['fab']."&cat=".$_GET['cat']."&catpai=".$_GET['catpai']."&pag=1\">1</a>\n";
                $saida .= "<a href=\"?fab=".$_GET['fab']."&cat=".$_GET['cat']."&catpai=".$_GET['catpai']."&pag=2\">2</a> ... \n";

                for($i=($ultima_pagina - (4+(2*$adjacentes))); $i<= $ultima_pagina; $i++){
                    if($i == $this->pag){
                        $saida .= "<a class=\"atual\" name=\"$i\">$i</a>\n";
                    }else{
                        $saida .= "<a href=\"?fab=".$_GET['fab']."&cat=".$_GET['cat']."&catpai=".$_GET['catpai']."&pag=$i\">$i</a>\n";
                    }
                }
            }
        }

    if($prox <= $ultima_pagina && $ultima_pagina > 1){
        $saida .= "<a href=\"?fab=".$_GET['fab']."&cat=".$_GET['cat']."&catpai=".$_GET['catpai']."&pag=$prox\">&raquo;</a>\n";
    }
    $saida .= "</div>\n";

        }

    return $saida;
    }

    // Pega os dados de Configuração do Site
    public function config(){
        $sql = "SELECT * FROM tbl_config LIMIT 1";
        $qr = $this->consulta($sql);

        foreach ($qr->fetch(PDO::FETCH_ASSOC) as $key => $value){
            $this->$key = $value;
        }

        if($_SERVER['HTTPS'] == "on" || $_SERVER['SERVER_PORT'] == 443){
            $this->url = "https://www.mandalaesoterica.com.br/novo/";
            //$this->url = "https://localhost/MANDALA_ESOTERICA/SITE/";
        }else{
            $this->url = "http://www.mandalaesoterica.com.br/novo/";
            //$this->url = "https://localhost/MANDALA_ESOTERICA/SITE/";
        }
    }

    public function manda_email(){
        /* Para enviar email HTML, você precisa definir o header Content-type. */
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

        /* headers adicionais */
        $headers .= "To: ".$this->getPara()."\r\n";
        $headers .= "From: MegaBabies <no-reply@mandalaesoterica.com.br>\r\n";

        if(mail($this->getPara(),$this->getAssunto(),$this->getMensagem(),$headers)){
            return true;
        }else{
            return false;
        }
    }

    public function geraSenha($tamanho = 8, $maiusculas = true, $numeros = true){
        $lmin = 'abcdefghijklmnopqrstuvwxyz';
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '1234567890';
        $retorno = '';
        $caracteres = '';

        $caracteres .= $lmin;
        if ($maiusculas) $caracteres .= $lmai;
        if ($numeros) $caracteres .= $num;

        $len = strlen($caracteres);
        for ($n = 1; $n <= $tamanho; $n++) {
            $rand = mt_rand(1, $len);
            $retorno .= $caracteres[$rand-1];
        }
        return $retorno;
    }

    public function mail_smtp(){
        include_once("smtp.class.php");
        $smtp = new smtp("mail.mandalaesoterica.com.br");
        $smtp->user = "no-reply@mandalaesoterica.com.br";
        $smtp->pass = "mandala123";
        $smtp->auth = true;
        $smtp->from = $this->getDe();
        $smtp->to = $this->getPara();
        $smtp->subject = $this->getAssunto();
        $smtp->body = $this->getMensagem();
        if($smtp->send()){
            return true;
        }else{
            return false;
        }
    }

    function busca_cep($cep){
	$resultado = @file_get_contents('http://republicavirtual.com.br/web_cep.php?cep='.urlencode($cep).'&formato=query_string');
	if(!$resultado){
		$resultado = "&resultado=0&resultado_txt=erro+ao+buscar+cep";
	}
	parse_str($resultado, $retorno);
	return $retorno;
    }

    // Faz o cálculo de frete
    public function calculaFrete($cod_servico, $cep_origem, $cep_destino, $peso, $altura, $largura, $comprimento){
        #OFICINADANET###############################
        # Código dos Serviços dos Correios
        # 41106 PAC sem contrato
        # 40010 SEDEX sem contrato
        # 40045 SEDEX a Cobrar, sem contrato
        # 40215 SEDEX 10, sem contrato
        ############################################

        $correios = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCepOrigem=".$cep_origem."&sCepDestino=".$cep_destino."&nVlPeso=".$peso."&nCdFormato=1&nVlComprimento=".$comprimento."&nVlAltura=".$altura."&nVlLargura=".$largura."&sCdMaoPropria=n&nVlValorDeclarado=0.00&sCdAvisoRecebimento=n&nCdServico=".$cod_servico."&nVlDiametro=0&StrRetorno=xml";
        $xml = simplexml_load_file($correios);
        if($xml->cServico->Erro == '0'){
            $this->valor = $xml->cServico->Valor;
            $this->prazo = $xml->cServico->PrazoEntrega;
        }else{
            $this->res = $xml->cServico->MsgErro;
            $this->valor = "erro";
        }
    }

    public function zero_left($valor){
        $letras = strlen($valor);
        for($i=7;$i>$letras;$i--){
            $zero .= "0";
        }

        $saida = $zero.$valor;

        return $saida;
    }

    public function is_cpf($str) {
	if (!preg_match('|^(\d{3})\.?(\d{3})\.?(\d{3})\-?(\d{2})$|', $str, $matches)){
            return false;
        }

	array_shift($matches);
	$str = implode('', $matches);

	if ($str == '00000000000' || $str == '11111111111' || $str == '22222222222' || $str == '33333333333' || $str == '44444444444' || $str == '55555555555' || $str == '66666666666' || $str == '77777777777' || $str == '88888888888' || $str == '99999999999'){
            return false;
        }

	for ($t=9; $t < 11; $t++) {
		for ($d=0, $c=0; $c < $t; $c++){
                    $d += $str[$c] * ($t + 1 - $c);
                }

		$d = ((10 * $d) % 11) % 10;

		if ($str[$c] != $d){
                    return false;
                }
	}

	return $str;
    }
}
?>
