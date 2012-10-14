<?php
/*************************************************
Esse arquivo pega os sites cadastrados e faz a
varredura para acrescentar na tabela sites.
Isso � feito diariamente �s 3 da manh�.

Sistema desenvolvido por Noedir C. Filho
http://www.constantweb.com.br  -  2010
**************************************************/

include_once("classes/conecta.class.php");
$via = new mysqlConn();

//Pega as informa��es da tabela ACRE
$sql = "SELECT * FROM tbl_acre WHERE acr_indexado = 'n'";
$qr = $via->consulta($sql);

set_time_limit(0);

while($sit = $qr->fetch(PDO::FETCH_ASSOC)){

$jacds = $via->totalRegistros("SELECT * FROM tbl_site WHERE sit_url = '".$sit['acr_url']."'");

if($jacds < 1){

//Verifica se possui o http:// ou https://
$conta = substr_count($sit['acr_url'],"http://");
$conta += substr_count($sit['acr_url'],"https://");

if($conta >= 1){

//Verifica se o site existe realmente
$head = get_headers($sit['acr_url']);

$header = substr_count($head[0],"200");

if($header >= 1){

//Pega o conte�do todo do site.
$pega = file_get_contents($sit['acr_url']);

// Armazena o conte�do em outra vari�vel
$tti = $pega;

//Retira algumas tags que n�o s�o necess�rios
$pega = ereg_replace('<script.*</script>', '', $pega);
$pega = ereg_replace('<object.*</object>', '', $pega);
$pega = ereg_replace('<style.*</style>','', $pega);
$pega = ereg_replace('<embed.*</embed>', '', $pega);
$pega = ereg_replace('<applet.*</applet>', '', $pega);
$pega = ereg_replace('<iframe.*</iframe>', '', $pega);
$pega = ereg_replace('<noframes.*</noframes>', '', $pega);
$pega = ereg_replace('<noscript.*</noscript>', '', $pega);
$pega = ereg_replace('<noembed.*</noembed>', '', $pega);

//Retira espa�os desnecess�rios
$pega = trim(strip_tags($pega));

//Retira a codifica��o HTML
$pega = html_entity_decode($pega);

//Obt�m as tags DESCRIPTION e KEYWORDS
$tag = get_meta_tags(strtolower(strtolower($sit['acr_url'])));

//Obt�m o t�tulo da p�gina pelo tag TITLE
preg_match("/<title>(.*)<\/title>/i", $tti, $titulo);

//Deixa somente o texto do t�tulo
$titulo = str_replace("<title>","",$titulo[0]);
$titulo = str_replace("</title>","",$titulo);

$titulo = str_replace("<TITLE>","",$titulo);
$titulo = str_replace("</TITLE>","",$titulo);

$titulo = html_entity_decode($titulo);

if(mb_detect_encoding($titulo,"UTF-8,ISO-8859-1") != "ISO-8859-1"){
	$titulo = utf8_decode($titulo);
}

//Coloca o conte�do da tag DESCRIPTION em uma vari�vel. Se n�o possuir, atribui o t�tulo
$descricao = ($tag['description'] == "" ? $titulo : $tag['description']);

//Coloca o conte�do da tag KEYWORDS em uma vari�vel. Se n�o possuir, atribui o t�tulo
$keyw = ($tag['keywords'] == "" ? $pega : $tag['keywords']);

//Insere o conte�do do site em banco de dados que ser� usado nas consultas no site
$via->setAcao("insert");
$via->setTabela("tbl_site");
$via->setCampos("sit_titulo, sit_url, sit_metakey, sit_metades, sit_idioma");
$via->setValores("'$titulo','".$sit['acr_url']."','$keyw','$descricao','".$_SERVER['HTTP_ACCEPT_LANGUAGE']."'");
$via->executa();

//Atualiza a tabela ACRE para mostrar que os sites j� foram indexados
$via->setAcao("update");
$via->setTabela("tbl_acre");
$via->setValores("acr_indexado = 's'");
$via->setCdg("acr_codigo = ".$sit['acr_codigo']."");
$via->executa();

}else{

	//Se o site der header diferente de 200, � exclu�do da base de dados
        $via->setAcao("delete");
        $via->setTabela("tbl_acre");
        $via->setCdg("acr_codigo = ".$sit['acr_codigo']."");
	$via->executa();
}

}else{

	//Se o site n�o come�ar com http:// ou https:// � exclu�do da base de dados
	$via->setAcao("delete");
        $via->setTabela("tbl_acre");
        $via->setCdg("acr_codigo = ".$sit['acr_codigo']."");
	$via->executa();
}
}else{

	//Exclui se o site j� estiver cadastrado na base de dados
	$via->setAcao("delete");
        $via->setTabela("tbl_acre");
        $via->setCdg("acr_codigo = ".$sit['acr_codigo']."");
	$via->executa();
}
}
$via->consulta("OPTIMIZE TABLE tbl_acre, tbl_image, tbl_site");
?>