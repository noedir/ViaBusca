<?
/*****************************************************
A finalidade desse arquivo é pegar os links dos sites
e acrescentar para indexar outros sites na lista de
busca. Esse recurso é similar ao Google, pois não é
necessário que se cadastre outros site, o sistema já
faz isso automaticamente.
É executado uma vez por semana.

Sistema desenvolvido por Noedir C. Filho
http://www.constantweb.com.br  -  2010
******************************************************/

include("conexao.php");
opendatabase();

$sites = @mysql_query("SELECT * FROM tbl_acre WHERE acr_indexado = 's'");

while($sit = @mysql_fetch_array($sites)){
	
$file = @file_get_contents($sit['acr_url']);

@preg_match_all("/<\s*a\s+[^>]*href\s*=\s*[\"']?([^\"' >]+)[\"' >]/isU", $file, $ancora);

@list($links) = $ancora;

foreach($links as $key => $value){
	
	$ver = @substr_count($value, "http://");
	$ver += @substr_count($value, "https://");
	
	if($ver >= 1){
		
		$href = @str_replace("<a href=\"","",$value);
		$href = @str_replace("\"","",$href);
		
		$head = @get_headers($sit['acr_url']);
		$head = @substr_count($head[0],"200");
		
		if($head >= 1){
		
		$check = @mysql_num_rows(mysql_query("SELECT * FROM tbl_acre WHERE acr_url = '$href'"));
		
		if($check <= 0){
			$insere = @mysql_query("INSERT INTO tbl_acre (acr_url, acr_indexado) VALUES ('$href','n')");
		}else{
			$delete = @mysql_query("DELETE FROM tbl_acre WHERE acr_codigo = $sit[acr_codigo]");
		}
		
		}else{
			$delete = @mysql_query("DELETE FROM tbl_acre WHERE acr_codigo = $sit[acr_codigo]");	
		}
	}
	
}
}
$delete = @mysql_query("DELETE FROM tbl_acre WHERE acr_indexado = 's'");
?>