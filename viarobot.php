<?php
/*****************************************************
A finalidade desse arquivo � pegar os links dos sites
e acrescentar para indexar outros sites na lista de
busca. Esse recurso � similar ao Google, pois n�o �
necess�rio que se cadastre outros site, o sistema j�
faz isso automaticamente.
� executado uma vez por semana.

Sistema desenvolvido por Noedir C. Filho
http://www.constantweb.com.br  -  2010
******************************************************/

include_once("classes/conecta.class.php");
$via = new mysqlConn();

$sql = "SELECT * FROM tbl_acre WHERE acr_indexado = 's'";
$qr = $via->consulta($sql);

$dom = new DOMDocument();

while($sit = $qr->fetch(PDO::FETCH_ASSOC)){

    @$dom->loadHTML(file_get_contents($sit['acr_url']));
    $tags = $dom->getElementsByTagName("a");

    foreach($tags as $tag){

        $link = $tag->getAttribute('href');
        if($link != ""){
            echo $link."<br>";
        }

        $ver = substr_count($link, "http://");
        $ver += substr_count($link, "https://");

        if($ver >= 1){

            $head = get_headers($link);
            $header = substr_count($head[0],"200");

            if($header >= 1){

                $check = $via->totalRegistros("SELECT * FROM tbl_acre WHERE acr_url = '$href'");

            if($check <= 0 && $link != ""){
                $via->setAcao("insert");
                $via->setTabela("tbl_acre");
                $via->setCampos("acr_url");
                $via->setValores("'$link'");
                $via->executa();
            }else{
                $via->setAcao("delete");
                $via->setTabela("tbl_acre");
                $via->setCdg("acr_codigo = '$sit[acr_codigo]'");
                $via->executa();
            }

            }else{
                $via->setAcao("delete");
                $via->setTabela("tbl_acre");
                $via->setCdg("acr_codigo = '$sit[acr_codigo]'");
                $via->executa();
            }
        }
    }
}
$via->setAcao("delete");
$via->setTabela("tbl_acre");
$via->setCdg("acr_indexado = 's'");
$via->executa();
?>