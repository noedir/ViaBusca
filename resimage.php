<?
//*********************************************************************
// CONFIGURAÇÃO DE BANCO DE DADOS
//*********************************************************************
//$con = mysql_connect($hostname_conn,$username_conn,$password_conn);
//$bd  = mysql_select_db($database_conn);
//*********************************************************************
// GERA A INSTRUÇÃO SQL E CHAMA A FUNÇÃO PARA GERAR AS COLUNAS
//*********************************************************************

$sql = $busqueda;

GeraColunas("6", $sql)
 ?>
<?
//*********************************************************************
// FUNÇÃO: GERACOLUNAS
// Parametros:
//  $pNumColunas (int)   > Quant. de colunas para distribuição
//  $pQuery    (string) > Query de registros
//*********************************************************************
function GeraColunas($pNumColunas, $pQuery) {
$resultado = mysql_query($pQuery);
echo ("<table width='100%' align='center' border='0'>\n");
 for($i = 0; $i <= mysql_num_rows($resultado); ++$i) {
 
 for ($intCont = 0; $intCont < $pNumColunas; $intCont++) {
  $linha = mysql_fetch_array($resultado);
  if ($i > $linha) {
   if ( $intCont < $pNumColunas-1) echo "</tr>\n";
   break;
  }

  $codi = $linha[0];
  $titu = $linha[1];
  $iurl = $linha[2];
  $larg = $linha[3];
  $altu = $linha[4];
  $tpim = $linha[5];
  $mkey = $linha[6];
  $mdes = $linha[7];
  
  $turl = substr($iurl,0,30)."...";
  $titu2 = substr($titu,0,20);
  
  if ( $intCont == 0 ) echo "<tr>\n";
  ?>
<style type="text/css">
<!--
.urllink {
	color: #090;
}
-->
</style>

  <td valign="top">
  <table width="100" border="0" align="center" cellpadding="5" cellspacing="0">
    <tr valign="bottom">
      <td height="100" align="left" valign="bottom"><a href="<?= $iurl; ?>"><img src="<?= $iurl; ?>" alt="<?= $titu; ?>" width="60%" border="0" /></a><br><?= $titu2; ?>
      <br>
      <?= $larg; ?> X <?= $altu; ?> - <?= $tpim; ?><br>
      <span class="urllink"><?= $turl; ?></span></td>
    </tr>
  </table>
<?
  
   // Aqui é o final do conteudo
  echo "</td>";

  if ( $intCont == $pNumColunas-1 ) {
   echo "</tr>\n";
  } else { $i++; }
 }
 
 }
echo ('</table>');
}
?>