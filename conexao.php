<?
define ("MYSQLHOST", "localhost");
define ("MYSQLUSER", "root"); //viabusca_noedir
define ("MYSQLPASS", "xpto11"); //GJxlzstF#eIC
define ("MYSQLDB", "viabusca"); //viabusca_base

function opendatabase() {

	$db = mysql_connect(MYSQLHOST, MYSQLUSER, MYSQLPASS);
		if (!$db){
			$exceptionstring = "Erro conectando na base de dados: <br />";
			$exceptionstring .= mysql_errno() . ": " . mysql_error();
		}else{
			mysql_select_db (MYSQLDB, $db);
		}
		return $db;
}
?>