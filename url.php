<?	
/*************************************************
Esse arquivo pega os sites cadastrados e faz a
varredura para acrescentar na tabela sites.
Isso � feito diariamente �s 3 da manh�.

Sistema desenvolvido por Noedir C. Filho
http://www.constantweb.com.br  -  2010
**************************************************/

include("conexao.php");
opendatabase();

//Pega as informa��es da tabela ACRE
$sites = @mysql_query("SELECT * FROM tbl_acre WHERE acr_indexado = 'n'");

@set_time_limit(0);

while($sit = @mysql_fetch_array($sites)){

$jacds = @mysql_num_rows(mysql_query("SELECT * FROM tbl_site WHERE sit_url = $sit[acr_url]"));

if($jacds <= 0){

//Verifica se possui o http:// ou https://
$conta = @substr_count($sit['acr_url'],"http://");
$conta += @substr_count($sit['acr_url'],"https://");

if($conta >= 1){

//Verifica se o site existe realmente
$head = @get_headers($sit['acr_url']);

$head = @substr_count($head[0],"200");

if($head >= 1){

//Pega o conte�do todo do site.
$pega = @file_get_contents($sit['acr_url']);

// Armazena o conte�do em outra vari�vel
$tti = $pega;

//Retira algumas tags que n�o s�o necess�rios
$pega = @ereg_replace('<script.*</script>', '', $pega);
$pega = @ereg_replace('<object.*</object>', '', $pega);
$pega = @ereg_replace('<style.*</style>','', $pega);
$pega = @ereg_replace('<embed.*</embed>', '', $pega);
$pega = @ereg_replace('<applet.*</applet>', '', $pega);
$pega = @ereg_replace('<iframe.*</iframe>', '', $pega);
$pega = @ereg_replace('<noframes.*</noframes>', '', $pega);
$pega = @ereg_replace('<noscript.*</noscript>', '', $pega);
$pega = @ereg_replace('<noembed.*</noembed>', '', $pega);

//Retira espa�os desnecess�rios
$pega = @trim(@strip_tags($pega));

//Retira a codifica��o HTML
$pega = @html_entity_decode($pega);

//Obt�m as tags DESCRIPTION e KEYWORDS
$tag = @get_meta_tags(strtolower(strtolower($sit['acr_url'])));

//Obt�m o t�tulo da p�gina pelo tag TITLE
@preg_match("/<title>(.*)<\/title>/i", $tti, $titulo);

//Deixa somente o texto do t�tulo
$titulo = @str_replace("<title>","",$titulo[0]);
$titulo = @str_replace("</title>","",$titulo);

$titulo = @str_replace("<TITLE>","",$titulo);
$titulo = @str_replace("</TITLE>","",$titulo);

$titulo = @html_entity_decode($titulo);

if(mb_detect_encoding($titulo,"UTF-8,ISO-8859-1") != "ISO-8859-1"){
	$titulo = @utf8_decode($titulo);
}

//Coloca o conte�do da tag DESCRIPTION em uma vari�vel. Se n�o possuir, atribui o t�tulo
$descricao = ($tag['description'] == "" ? $titulo : $tag['description']);

//Coloca o conte�do da tag KEYWORDS em uma vari�vel. Se n�o possuir, atribui o t�tulo
$keyw = ($tag['keywords'] == "" ? $pega : $tag['keywords']);

//Insere o conte�do do site em banco de dados que ser� usado nas consultas no site
$insere = @mysql_query("INSERT INTO tbl_site (sit_titulo, sit_url, sit_metakey, sit_metades, sit_idioma) VALUES ('$titulo','$sit[acr_url]','$keyw','$descricao','".$_SERVER['HTTP_ACCEPT_LANGUAGE']."')");

//Atualiza a tabela ACRE para mostrar que os sites j� foram indexados
$atualiza = @mysql_query("UPDATE tbl_acre SET acr_indexado = 's' WHERE acr_codigo = $sit[acr_codigo]");

}else{
	
	//Se o site der header diferente de 200, � exclu�do da base de dados
	$apaga = @mysql_query("DELETE FROM tbl_acre WHERE acr_codigo = $sit[acr_codigo]");
}

}else{
	
	//Se o site n�o come�ar com http:// ou https:// � exclu�do da base de dados
	$apaga = @mysql_query("DELETE FROM tbl_acre WHERE acr_codigo = $sit[acr_codigo]");
}
}else{
	
	//Exclui se o site j� estiver cadastrado na base de dados
	$apaga = @mysql_query("DELETE FROM tbl_acre WHERE acr_codigo = $sit[acr_codigo]");
}
}
$opt = mysql_query("OPTIMIZE TABLE tbl_acre, tbl_image, tbl_site");
?>