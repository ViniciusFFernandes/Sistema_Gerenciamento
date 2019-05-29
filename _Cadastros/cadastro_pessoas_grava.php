<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/Tabelas.class.php");
// print_r($_POST);
// exit;
  if ($_POST['operacao'] == "buscaPessoas") {
    $sql = "SELECT * 
            FROM pessoas 
              LEFT JOIN cidades ON (pess_idcidades = idcidades) 
              LEFT JOIN estados ON (cid_idestados = idestados)";
    //
    if ($_POST['pesquisa'] != "") {
      $sql .= " WHERE idpessoas LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                OR pess_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                OR pess_cidade LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    $tabelas->geraTabelaPes($res, $db);
    exit;
  }

  if ($_POST['operacao'] == "excluiTelefones") {
    $db->setTabela("pessoas_numeros");
    $where = "idpessoas_numeros = " . $_POST['idtelefone'];
    $db->excluir($where);
    exit;
  }

  if ($_POST['operacao'] == "gravaTelefones") {
    $db->setTabela("pessoas_numeros");
    $dados['pnum_idpessoas']        = $util->sgr($_POST['idpessoas']);
    $dados['pnum_DDD']              = $util->sgr($_POST['pnum_DDD']);
    $dados['pnum_numero']           = $util->sgr($_POST['pnum_numero']);
    $db->gravar($dados);
    exit;
  }

  if ($_POST['operacao'] == "buscaTelefones") {
    $db->setTabela("pessoas_numeros");
    $where = " pnum_idpessoas = " . $_POST['idpessoas'];
    $res = $db->consultar($where);
    //
    $tabelas = new Tabelas();
    $tabelas->geraTabelaTel($res);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/cadastro_pessoas.php');
    exit;
    }

  if ($_POST['operacao'] == 'validaUsuario'){
    $user = new Usuario($_SESSION['user'], $_SESSION['senha'], $db);
    $user->testaLogin($db, $util, $_POST['pess_usuario'], $_POST['idpessoas']);

    }

  if ($_POST['operacao'] == 'gravaLogin'){
    $db->setTabela("pessoas");

    $dados['pess_usuario']       = $util->sgr($_POST['pess_usuario']);
    $dados['pess_senha']         = $util->sgr($_POST['pess_senha']);

    $where = " idpessoas = " . $_POST['idpessoas'];
    $db->alterar($where, $dados);
    }

  if ($_POST['operacao'] == 'pess_gravar'){
  	$db->setTabela("pessoas");

  	$dados['pess_nome'] 			= $util->sgr($_POST['pess_nome']);
  	$dados['pess_rg'] 				= $util->sgr($_POST['pess_rg']);
  	$dados['pess_cpf'] 				= $util->sgr($_POST['pess_cpf']);
  	$dados['pess_cnpj'] 			= $util->sgr($_POST['pess_cnpj']);
  	$dados['pess_cep'] 				= $util->sgr($_POST['pess_cep']);
  	$dados['pess_endereco'] 		= $util->sgr($_POST['pess_endereco']);
  	$dados['pess_endereco_numero'] 	= $util->sgr($_POST['pess_endereco_numero']);
    $dados['pess_idcidades']  = $util->igr($_POST['pess_idcidades']);
  	$dados['pess_bairro'] 			= $util->sgr($_POST['pess_bairro']);
  	$dados['pess_cliente'] 			= $util->sgr($_POST['pess_cliente']);
  	$dados['pess_fornecedor'] 		= $util->sgr($_POST['pess_fornecedor']);
  	$dados['pess_funcionario'] 		= $util->sgr($_POST['pess_funcionario']);

  	if ($_POST['idpessoas'] > 0) {
  		$where = " idpessoas = " . $_POST['idpessoas'];
  		$db->alterar($where, $dados);
  		$_SESSION['mensagem'] = "Alteração efetuado com sucesso!";
      $_SESSION['tipoMsg'] = "info";
      header('location: ../_Cadastros/cadastro_pessoas.php?idpessoas=' . $_POST['idpessoas']);
  		exit;
    }else{
  		$db->gravar($dados);
  		$ultimoID = $db->getUltimoID();
  		$_SESSION['mensagem'] = "Cadastro efetuada com sucesso!";
      $_SESSION['tipoMsg'] = "info";
      header('location:../_Cadastros/cadastro_pessoas.php?idpessoas=' . $ultimoID);
  		exit;
  }
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("pessoas");
    $where = "idpessoas = " . $_POST['idpessoas'];
    $db->excluir($where);
    $_SESSION['mensagem'] = "Cadastro excluido com sucesso!";
    $_SESSION['tipoMsg'] = "danger";
    header('location:../_Cadastros/cadastro_pessoas.php');
    exit;
  }

 ?>
