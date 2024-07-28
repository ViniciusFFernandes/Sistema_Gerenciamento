<?php
    $prog_file = basename($_SERVER['PHP_SELF']);

    $sql = "SELECT * 
            FROM programas 
            WHERE prog_file = " . $util->sgr($prog_file);
    $reg = $db->retornaUmReg($sql);
    //
    // echo $reg['prog_modelo'];
    // exit;
    if($reg['prog_modelo'] != ""){
        include_once($reg['prog_modelo']);
        exit;
    }
?>