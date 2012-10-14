<?php
session_start();

include_once("classes/conecta.class.php");
$via = new mysqlConn();

if($_GET['bus'] == "sim" and $_GET['qy'] <> ""){

    if($_GET['tipo'] == "toda"){
        $idioma = "";
    }else{
        $idioma = "AND sit_idioma LIKE '%".$_GET['tipo']."%'";
    }

    $sql = "SELECT * FROM tbl_site WHERE sit_url LIKE '%".$_GET['qy']."%' OR sit_metakey LIKE '%".$_GET['qy']."%' OR sit_metades LIKE '%".$_GET['qy']."%' $idioma ORDER BY sit_relevancia DESC LIMIT 30";
    $qr = $via->consulta($sql);

    $nbus = $via->totalRegistros($sql);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include("meta.php"); ?>
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
    <td align="center"><img src="images/logo.jpg" alt="Via Busca" width="375" height="153" /></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td height="72" align="center"><form id="formbusca" name="formbusca" method="get" action="index.php">
      <table width="100%" border="0" cellspacing="0" cellpadding="4">
        <tr>
          <td align="center"><input name="qy" type="text" value="<?php echo  $_GET['qy']; ?>" id="qy" size="50" />
&nbsp;
<input type="submit" name="button" id="button" value="Buscar" />
<input name="bus" type="hidden" id="bus" value="sim" /></td>
          </tr>
        <tr>
          <td align="center"><input name="tipo" type="radio" id="radio" value="toda" <? if($_GET['tipo'] == "toda"){ echo "checked=checked"; }elseif($_GET['tipo'] == ""){ echo "checked=checked"; } ?> />
            Na Web&nbsp;&nbsp;
            <input type="radio" name="tipo" id="radio2" value="pt-br" <? if($_GET['tipo'] == "pt-br"){ echo "checked=checked"; } ?> />
            Em Portugu&ecirc;s</td>
          </tr>
      </table>
    </form></td>
  </tr>
</table>
<? if($_GET['bus'] == "sim" and $_GET['qy'] != ""){?>
<? if($nbus >= 1){ ?>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td bgcolor="#DDEBFF" class="mn_resultado">Encontrado <strong><?php echo  $nbus; ?></strong> ocorr&ecirc;ncias para <strong><?php echo  $_GET['qy']; ?></strong></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <?php while($res = $qr->fetch(PDO::FETCH_ASSOC)){
          $via->setAcao("update");
          $via->setTabela("tbl_site");
          $via->setValores("sit_relevancia = sit_relevancia + 1");
          $via->setCdg("sit_codigo = '".$res['sit_codigo']."'");
          $via->executa();
	  ?>
  <tr>
      <td><a href="<?php echo  $res['sit_url']; ?>" class="tit_link"><?php echo utf8_encode($res['sit_titulo']); ?></a></td>
  </tr>
  <tr>
      <td><?php echo utf8_encode($res['sit_metades']); ?></td>
  </tr>
  <tr>
    <td><strong class="res_link"><?php echo  $res['sit_url']; ?></strong></td>
  </tr>

  <tr>
    <td>&nbsp;</td>
  </tr><?php } ?>
</table>
<?php
}else{
?>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td><p>Não foi encontrado nenhum resultado para <strong><?php echo  $_GET['qy']; ?></strong>.</p>
    <p>Siga uma das dicas abaixo para encontrar&nbsp;o que voc&ecirc; procura:</p>
    <ul>
      <li> Certifique-se de que todas as palavras estejam escritas corretamente.</li>
      <li>Tente palavras-chave diferentes.</li>
      <li>Tente palavras-chave mais gen&eacute;ricas.</li>
    </ul></td>
  </tr>
</table>
<?php
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
      <td align="center"><?php echo $via->tempo_carrega_fim(); ?>
</td>
  </tr>
</table>
</body>
</html>