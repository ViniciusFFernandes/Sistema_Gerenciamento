<?php
    //
    //Adicona os path padrões do sistema
    $path = PATH_SEPARATOR . '../Class/';
    $path .= PATH_SEPARATOR . 'Class/';
    $path .= PATH_SEPARATOR . '../privado/';
    $path .= PATH_SEPARATOR . 'privado/';
    $path .= PATH_SEPARATOR . '../_BD/';
    $path .= PATH_SEPARATOR . '_BD/';
    $path .= PATH_SEPARATOR . '../PHPMailer/';
    $path .= PATH_SEPARATOR . 'PHPMailer/';
    set_include_path(get_include_path() . $path);
?>