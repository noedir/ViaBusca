<?
include("conexao.php");
opendatabase();

//@set_time_limit(0);

$sites = @mysql_query("SELECT * FROM tbl_site");

while($sit = @mysql_fetch_array($sites)){

$file = @file_get_contents($sit['sit_url']);

$tti = $file;

$file = @ereg_replace('<script.*</script>', '', $file);
$file = @ereg_replace('<object.*</object>', '', $file);
$file = @ereg_replace('<style.*</style>','', $file);
$file = @ereg_replace('<embed.*</embed>', '', $file);
$file = @ereg_replace('<applet.*</applet>', '', $file);
$file = @ereg_replace('<iframe.*</iframe>', '', $file);
$file = @ereg_replace('<noframes.*</noframes>', '', $file);
$file = @ereg_replace('<noscript.*</noscript>', '', $file);
$file = @ereg_replace('<noembed.*</noembed>', '', $file);

$file = @trim(@strip_tags($file));

$file = @html_entity_decode($file);

$tag = @get_meta_tags($sit['sit_url']);

@preg_match_all("/src\=\"([a-zA-Z_\.0-9\/\-\! :\&\@\$]*)\"/i", $tti, $ancora);

@preg_match("/<title>(.*)<\/title>/i", $tti, $titulo);

$titulo = @str_replace("<title>","",$titulo[0]);
$titulo = @str_replace("</title>","",$titulo);

$titulo = @str_replace("<TITLE>","",$titulo);
$titulo = @str_replace("</TITLE>","",$titulo);

$titulo = @html_entity_decode($titulo);

if(mb_detect_encoding($titulo,"UTF-8,ISO-8859-1") != "ISO-8859-1"){
	$titulo = @utf8_decode($titulo);
}

@list($links) = $ancora;

foreach($links as $key => $value){
	
	$value = @str_replace("src=\"","",$value);
	$value = @str_replace("\"","",$value);
	
	$lnkhttp = substr_count($value,"http://");
	$lnkhttp += substr_count($value,"https://");
	
	if($lnkhttp <= 0){
		$etapa = $sit['sit_url']."/".$value;
	}else{
		$etapa = $value;
	}
	
	$etapa = @str_replace("///","/",$etapa);
	
	$filename = $etapa; // Qual será a imagem
	$imagesize = @getimagesize($filename); // Pega os dados
	
	#Define as arrays
	
	$x = $imagesize[0]; // 0 será a largura.
	$y = $imagesize[1]; // 1 será a altura.
	
	//if($x <> "" || $y <> ""){
		
	if($tag['description'] == ""){
		$descricao = $titulo;
	}else{
		$descricao = $tag['description'];
	}

	if($tag['keywords'] == ""){
		$keyw = @trim($file);
		$keyw = @str_replace(" ",",",$file);
	}else{
		$keyw = $tag['keywords'];
	}
	
	$ver = @substr_count($etapa,".js");
	$ver += @substr_count($etapa,".swf");
	$ver += @substr_count($etapa,".php");
	$ver += @substr_count($etapa,".html");
	$ver += @substr_count($etapa,".asp");
	$ver += @substr_count($etapa,".htm");
	$ver += @substr_count($etapa,".css");
	
	$tipoimg = @explode(".",$etapa);
	$tpim = @end($tipoimg);
	
	if($ver <= 0 || $x <> "" || $y <> ""){
		
		$check = @mysql_num_rows(mysql_query("SELECT * FROM tbl_image WHERE img_url = '$etapa'"));
		
		if($check <= 0){
			$cad = @mysql_query("INSERT INTO tbl_image (img_titulo, img_url, img_largura, img_altura, img_tipo, img_metakey, img_metades) VALUES ('$titulo','$etapa','$x','$y','$tpim','$keyw','$descricao')");
		}else{
			$up = @mysql_query("UPDATE tbl_image SET img_titulo = '$titulo', img_largura = '$x', img_altura = '$y', img_tipo = '$tpim', img_metakey = '$keyw', img_metades = '$descricao' WHERE img_url = '$etapa'");
		}	
	}else{
		$apaga = @mysql_query("DELETE FROM tbl_image WHERE img_url = '$etapa'");
	}
	
}
}

$nulo = "";

$apaga = @mysql_query("DELETE FROM tbl_image WHERE img_largura = '$nulo'");
?>