<?
session_start();

if($_POST['manda'] == "sim"){
include("conexao.php");
opendatabase();

$nbus = @mysql_num_rows(mysql_query("SELECT * FROM tbl_site WHERE sit_url = '$_POST[url]'"));

if($nbus <= 0){
	
	$head = @get_headers($_POST['url']);
	
	$pega = @file_get_contents($_POST['url']);
	$tag = @get_meta_tags($_POST['url']);
	
	@preg_match("/<title>(.*)<\/title>/i", $pega, $titulo);
	
	$titulo = @str_replace("<title>","",$titulo[0]);
	$titulo = @str_replace("</title>","",$titulo);
	
	if($tag['description'] == ""){
		$tag['description'] = $titulo;
	}
	
	if($tag['keywords'] == ""){
		$tag['keywords'] = @implode(",",$tag['keywords']);
	}
	
	$insere = @mysql_query("INSERT INTO tbl_site (sit_titulo, sit_url, sit_metakey, sit_metades, sit_idioma) VALUES ('$titulo','$_POST[url]','$tag[keywords]','$tag[description]','".$_SERVER['HTTP_ACCEPT_LANGUAGE']."')");
	
	?>
	<script type="text/javascript">
		alert("Dados inseridos com sucesso.");
		location.href="index.php";
	</script>
	<?
	
}else{

?>
<script type="text/javascript">
	alert("Esse site já está cadastrado no sistema.\r\n\r\nExperimente inserir um diretório.");
	location.href="acrescenta.php";
</script>
<?

}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<? include("meta.php"); ?>
<title>Via Busca</title>
<style type="text/css">
<!--
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000;
}
body {
	background-color: #FFF;
}
.tp_logo {
	font-size: 40px;
	color: #090;
}
.tit_link {
	font-size: 14px;
}
.res_link {
	color: #090;
}
.mn_resultado {
	font-size: 14px;
}
-->
</style></head>

<body>
<table width="100%" height="117" border="0" cellpadding="4" cellspacing="0">
  <tr>
    <td height="23"><a href="index.php">Home</a></td>
  </tr>
  <tr>
    <td align="center"><img src="images/logo.jpg" width="375" height="153" /></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td><hr /></td>
  </tr>
  <tr>
    <td><p>Para que seu site seja indexado corretamente (isso ocorrer&aacute; em tempo real), voc&ecirc; precisa ter definido, obrigatoriamente, 
        duas meta tags no seu site:
</p>
      <ul>
        <li>META KEYWORDS</li>
        <li>META DESCRIPTION</li>
      </ul>
      <p>Ex: Vamos supor que seu site &eacute; sobre&nbsp;programa&ccedil;&atilde;o. Ent&atilde;o, as meta tags poderia ficar assim:</p>
      <p>&lt;meta NAME=&quot;description&quot; CONTENT=&quot;Somos especialistas em programa&ccedil;&atilde;o para sistemas online. Fazemos v&aacute;rios testes antes de disponibilizar o site online.&quot; /&gt;<br />
        &lt;meta NAME=&quot;keywords&quot; CONTENT=&quot;programa&ccedil;&atilde;o,php,asp,mysql,sql,server,online&quot; /&gt;
      </p>
      <p>Somente com essas  meta tags inseridas corretamente, seu site come&ccedil;ar&aacute; a aparecer&nbsp;nos resultados de busca.</p>
    <p>De tempos em tempos, o sistema buscar&aacute; por atualiza&ccedil;&otilde;es em seu site e, com isso, manter&aacute; a base de dados sempre atualizada.</p>
    <p>&nbsp;</p>
    <p><a href="acrescenta.php"><strong>Acrescente agora o seu site</strong></a>.</p></td>
  </tr>
</table>
</body>
</html>