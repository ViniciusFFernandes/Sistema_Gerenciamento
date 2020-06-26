<?php
require_once '../_BD/conecta_login.php';
require_once '../Class/Relatorios.class.php';
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
