<?php
include_once '../_BD/conecta_login.php';
include_once '../Class/Relatorios.class.php';
// print_r($_POST);
// exit;
$rel = new Relatorios();

//if ($_POST['operacao'] == 'Listar'){
  //
  $rel->geraRelatorioSessoes($db, $util);
  //
  $rel->ultilizaAPIRenato("https://connectiontrabalho.000webhostapp.com/retornaSessoes/", '', $util);
  exit;
  //}
