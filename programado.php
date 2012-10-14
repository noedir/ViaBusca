<?
/*********************************************
Esse arquivo atualiza as informa��es dos sites
que j� est�o cadastrados. Isso � necess�rio
para que os dados n�o fiquem obsoletos.
Essa verifica��o � feita uma vez por m�s.

Sistema desenvolvido por Noedir C. Filho
http://www.constantweb.com.br  -  2010
*********************************************/

include_once("classes/conecta.class.php");
$via = new mysqlConn();

$sql = "SELECT * FROM tbl_site";
$qr = $via->consulta($sql);

while($sit = $qr->fetch(PDO::FETCH_ASSOC)){

	$head = get_headers($sit['sit_url']);

	$head = substr_count($head[0],"200");

	if($head >= 1){

	$pega = file_get_contents($sit['sit_url']);

	$tti = $pega;

	$pega = ereg_replace('<script.*</script>', '', $pega);
	$pega = ereg_replace('<object.*</object>', '', $pega);
	$pega = ereg_replace('<embed.*</embed>', '', $pega);
	$pega = ereg_replace('<applet.*</applet>', '', $pega);
	$pega = ereg_replace('<iframe.*</iframe>', '', $pega);
	$pega = ereg_replace('<noframes.*</noframes>', '', $pega);
	$pega = ereg_replace('<noscript.*</noscript>', '', $pega);
	$pega = ereg_replace('<noembed.*</noembed>', '', $pega);

	$pega = trim(strip_tags($pega));

	$pega = html_entity_decode($pega);

	$tag = get_meta_tags(strtolower(strtolower($sit['sit_url'])));

	preg_match("/<title>(.*)<\/title>/i", $tti, $titulo);

	$titulo = str_replace("<title>","",$titulo[0]);
	$titulo = str_replace("</title>","",$titulo);

	$titulo = str_replace("<TITLE>","",$titulo);
	$titulo = str_replace("</TITLE>","",$titulo);

	$titulo = html_entity_decode($titulo);

        if(mb_detect_encoding($titulo,"UTF-8,ISO-8859-1") != "ISO-8859-1"){
                $titulo = utf8_decode($titulo);
        }

	if($titulo != ""){

            $descricao = ($tag['description'] == "" ? $titulo : $tag['description']);

            $keyw = ($tag['keywords'] == "" ? $pega : $tag['keywords']);

            $via->consulta("UPDATE tbl_site SET sit_titulo = '$titulo', sit_metakey = '$keyw', sit_metades = '$descricao' WHERE sit_codigo = $sit[sit_codigo]");
	}else{

		$via->consulta("DELETE FROM tbl_site WHERE sit_codigo = $sit[sit_codigo]");
	}
	}else{
		$via->consulta("DELETE FROM tbl_site WHERE sit_codigo = $sit[sit_codigo]");
	}

}
?>