<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'tipo_movto_estoque_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT * FROM tipo_movto_estoque";
    //
    if ($_POST['pesquisa'] != "") {
      $sql .= " WHERE idtipo_movto_estoque LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR time_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idtipo_movto_estoque'] = "width='6%'";
    $dados['time_nome'] = "";
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
  	$db->setTabela("tipo_movto_estoque", "idtipo_movto_estoque");

    unset($dados);
    $dados['id']                    = $_POST['id_cadastro'];
  	$dados['time_nome'] 	        = $util->sgr($_POST['time_nome']);
  	$dados['time_entrada'] 	        = $util->sgr($_POST['time_entrada']);
  	$dados['time_saida'] 	        = $util->sgr($_POST['time_saida']);
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
    $db->setTabela("tipo_movto_estoque", "idtipo_movto_estoque");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir cadastro<br>Operação cancelada!");
        exit;
    }
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
  }

 ?>
