<?
/*************************************************
Esse arquivo recebe o site que o usuário
digita diretamente no site.

Sistema desenvolvido por Noedir C. Filho
http://www.constantweb.com.br  -  2010
**************************************************/

session_start();

if($_POST['manda'] == "sim"){
include("conexao.php");
opendatabase();

$nbus = @mysql_num_rows(mysql_query("SELECT * FROM tbl_site WHERE sit_url = '$_POST[url]'"));

if($nbus <= 0){

$cad = @mysql_query("INSERT INTO tbl_acre (acr_url, acr_indexado) VALUES ('$_POST[url]','n')");

?>
	<script type="text/javascript">
		alert("Dados cadastrados com sucesso.\r\n\r\nSerão indexados em até 24 horas.");
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
    <td><form id="form" name="form" method="post" action="acrescenta.php">
      <p>Para que seu site seja indexado corretamente (isso ocorrer&aacute; em at&eacute; 24 horas), voc&ecirc; precisa ter definido, obrigatoriamente, 
        duas meta tags no seu site:
</p>
      <ul>
        <li>META KEYWORDS</li>
        <li>META DESCRIPTION</li>
        </ul>
      <p>Ex: Vamos supor que seu site &eacute; sobre&nbsp;programa&ccedil;&atilde;o. Ent&atilde;o, as meta tags poderiam ficar assim:</p>
      <p>&lt;meta NAME=&quot;description&quot; CONTENT=&quot;Somos especialistas em programa&ccedil;&atilde;o para sistemas online. Fazemos v&aacute;rios testes antes de disponibilizar o site online.&quot; /&gt;<br />
        &lt;meta NAME=&quot;keywords&quot; CONTENT=&quot;programa&ccedil;&atilde;o,php,asp,mysql,sql,server,online&quot; /&gt;
      </p>
      <p>Somente com essas  meta tags inseridas corretamente, seu site come&ccedil;ar&aacute; a aparecer&nbsp;nos resultados da busca.</p>
      <p>&nbsp;</p>
      <p>Caso j&aacute; tenha essas  meta tags no seu site, preencha o&nbsp;endere&ccedil;o do seu site abaixo (n&atilde;o esquecer do http://) e clique em continuar.</p>
      <table width="563" border="0" cellspacing="0" cellpadding="4">
        <tr>
          <td width="155"><strong>Digite o URL do seu site:</strong></td>
          <td width="392"><input name="url" type="text" id="url" value="http://" size="50" /></td>
        </tr>
      </table>
      <p>
        <input type="submit" name="button" id="button" value=":: Continuar ::" />
        <input name="manda" type="hidden" id="manda" value="sim" />
      </p>
    </form></td>
  </tr>
</table>
</body>
</html>