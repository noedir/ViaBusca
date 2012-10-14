<?php
/**
*@param string $param Receive a comand to starts or finalize the timer. This will mensure the load time of each page/query
*@param string $starttime Receive the value of start time if already called before with $param="start"
*/
function timer($param,$starttime)
{
    switch($param)
    {
        case"start":
            $mtime = microtime();
            $mtime = explode(" ",$mtime);
            $mtime = $mtime[1] + $mtime[0];
            $starttime = $mtime;
         $returnable = $starttime;
        break;
        case"finalize":
            $mtime = microtime();
            $mtime = explode(" ",$mtime);
            $mtime = $mtime[1] + $mtime[0];
            $endtime = $mtime;                      // Finaliza a variável de contagem do tempo de geração da página.
            $totaltime = ($endtime - $starttime);
            $returnable = round($totaltime,2);
        break;
    }

    return $returnable;
}
?>