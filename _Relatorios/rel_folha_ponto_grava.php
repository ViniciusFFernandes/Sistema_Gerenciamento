<?php
include_once '../_BD/conecta_login.php';
include_once '../Class/Relatorios.class.php';
// print_r($_POST);
// exit;
$rel = new Relatorios();

if ($_POST['operacao'] == 'Listar'){
  $db->setTabela("folhaponto");
  //
  $where = " fopo_horario >= " . $util->dgr($_POST['dataInicial'], "00:00:00") . " AND fopo_horario <= " . $util->dgr($_POST['dataFinal'], "23:59:59");
  if ($_POST['idpessoas'] > 0) {
    $where .= " AND fopo_idpessoas = " . $_POST['idpessoas'];
  }
  $where .= " ORDER BY fopo_idpessoas, fopo_horario";  
  $res = $db->consultar($where);
  //
  $rel->geraRelatorioFolhaPonto($res, $db, $util);
  exit;
  }
