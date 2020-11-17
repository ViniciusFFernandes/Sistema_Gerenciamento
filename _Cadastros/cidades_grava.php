<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'cidades_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT * 
            FROM cidades 
              LEFT JOIN estados ON (cid_idestados = idestados)";
    
    if ($_POST['pesquisa'] != "") {
        $sql .= " WHERE idcidades LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR cid_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR est_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idcidades'] = "width='6%'";
    $dados['cid_nome'] = "";
    $dados['est_uf'] = "width='10%'";
    //
    $cabecalho["Código"] = "";
    $cabecalho["Nome"] = "";
    $cabecalho["Estado"] = "";
    //
    echo $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno, $cabecalho);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("cidades", "idcidades");
    //
    $dados['id']            = $_POST['id_cadastro'];
  	$dados['cid_nome'] 			= $util->sgr($_POST['cid_nome']);
  	$dados['cid_idestados'] = $util->igr($_POST['cid_idestados']);
    $db->gravarInserir($dados, true);
    //
  	if ($_POST['id_cadastro'] > 0) {
  		$id = $_POST['id_cadastro'];
    }else{
  		$id = $db->getUltimoID();
  }
    header('location: ../_Cadastros/' . $paginaRetorno . '?id_cadastro=' . $id);
    exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("cidades", "idcidades");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir cadastro<br>Operação cancelada!");
        exit;
    }
    header('location:../_Cadastros/' . $paginaRetorno);
    exit;
  }

 ?>
