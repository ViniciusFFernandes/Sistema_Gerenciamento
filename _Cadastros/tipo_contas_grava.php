<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'tipo_contas_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT * FROM tipo_contas";
    //
    if ($_POST['pesquisa'] != "") {
      $sql .= " WHERE idtipo_contas LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR tico_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idtipo_contas'] = "width='6%'";
    $dados['tico_nome'] = "";
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
  	$db->setTabela("tipo_contas", "idtipo_contas");

    unset($dados);
    $dados['id']                  = $_POST['id_cadastro'];
  	$dados['tico_nome'] 	        = $util->sgr($_POST['tico_nome']);
  	$dados['tico_tipo_vale'] 	    = $util->sgr($_POST['tico_tipo_vale']);
  	$dados['tico_tipo_extra'] 	  = $util->sgr($_POST['tico_tipo_extra']);
  	$dados['tico_tipo_salario'] 	= $util->sgr($_POST['tico_tipo_salario']);
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
    $db->setTabela("tipo_contas", "idtipo_contas");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir cadastro<br>Operação cancelada!");
        exit;
    }
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
  }

 ?>
