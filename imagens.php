<?
session_start();

if($_GET['bus'] == session_id() and $_GET['qy'] <> ""){
include("conexao.php");
opendatabase();

if($_GET['tipo'] == "toda"){
	$idioma = "";
}else{
	$idioma = $_GET['tipo'];
}

$busqueda = "SELECT * FROM tbl_image WHERE img_url LIKE '%".$_GET['qy']."%' OR img_metakey LIKE '%".$_GET['qy']."%' OR img_metades LIKE '%".$_GET['qy']."%' OR img_titulo LIKE '%".$_GET['qy']."%' ORDER BY img_relevancia DESC LIMIT 30";

$busca = mysql_query($busqueda);

$nbus = mysql_num_rows($busca);
}

$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime; // Dá início a variável de contagem do tempo de geração da página.
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
    <td height="23"><a href="index.php">Home</a>
   &nbsp;&nbsp; | &nbsp;&nbsp;<a href="imagens.php">Imagens</a>   <hr /></td>
  </tr>
  <tr>
    <td align="center"><img src="images/logoimg.jpg" alt="Via Busca - Imagens" width="375" height="153" /></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td align="center"><form id="formbusca" name="formbusca" method="GET" action="imagens.php">
      <table width="100%" border="0" cellspacing="0" cellpadding="4">
        <tr>
          <td align="center"><input name="qy" type="text" value="<?= $_GET['qy']; ?>" id="qy" size="50" />
  &nbsp;
  <input type="submit" value="Buscar" />
  <input name="bus" type="hidden" id="bus" value="<?= session_id(); ?>" /></td>
        </tr>
        </table>
    </form></td>
  </tr>
</table>
<? if($_GET['bus'] == session_id() and $_GET['qy'] <> ""){?>
<? if($nbus >= 1){ ?>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td bgcolor="#DDEBFF" class="mn_resultado">Encontrado <strong><?= $nbus; ?></strong> ocorr&ecirc;ncias para <strong><?= $_GET['qy']; ?></strong></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><? include("resimage.php"); ?></td>
  </tr>
</table>
<?
}else{
?>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td><p>N&atilde;o foi encontrado nenhum resultado para <strong><?= $_GET['qy']; ?></strong>.</p>
    <p>Siga uma das dicas abaixo para encontrar&nbsp;o que voc&ecirc; procura:</p>
    <ul>
      <li> Certifique-se de que todas as palavras estejam escritas corretamente.</li>
      <li>Tente palavras-chave diferentes.</li>
      <li>Tente palavras-chave mais gen&eacute;ricas.</li>
    </ul></td>
  </tr>
</table>
<?
}
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td><hr /></td>
  </tr>
  <tr>
    <td align="center"><a href="acrescenta.php">Acrescente o seu site</a>&nbsp; |&nbsp; <a href="comofunciona.php">Como funciona</a></td>
  </tr>
  <tr>
    <td align="center"><?
$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$endtime = $mtime; // Finaliza a variável de contagem do tempo de geração da página.
$totaltime = ($endtime - $starttime); // É feita a contagem do tempo total que a página levou para ser gerada.
echo "Página carregada em: ". round($totaltime,2) ." segundo(s)."; // Mostra o tempo que a página levou para ser gerada.
?>
</td>
  </tr>
</table>
</body>
</html>