<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'bancos_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT * FROM bancos";
    //
    if ($_POST['pesquisa'] != "") {
      $sql .= " WHERE idbancos LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR banc_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idbancos'] = "width='6%'";
    $dados['banc_nome'] = "";
    //
    $cabecalho['Código'] = '';
    $cabecalho['Nome'] = '';
    //
    echo $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno, $cabecalho);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("bancos", "idbancos");

    unset($dados);
    $dados['id']              = $_POST['id_cadastro'];
  	$dados['banc_nome'] 	    = $util->sgr($_POST['banc_nome']);
    $db->gravarInserir($dados, true);

  	if ($_POST['id_cadastro'] > 0) {
  		$id = $_POST['id_cadastro'];
    }else{
  		$id = $db->getUltimoID();
  }
  header('location:../_Cadastros/' . $paginaRetorno . '?id_cadastro=' . $id);
  exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("bancos", "idbancos");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir cadastro<br>Operação cancelada!");
        exit;
    }
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
  }

 ?>
