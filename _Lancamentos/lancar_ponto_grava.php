<?php
include_once '../_BD/conecta_login.php';
// print_r($_POST);
// exit;

if ($_POST['operacao'] == 'gravar'){
  if ($_POST['idpessoas'] <= 0 ) {
    $util->mostraErro("Funcionaio não selecionado!!!<br>Porfavor, selecione um Funcionaioe tente novamente!");
		exit;
  }
  $db->setTabela("folhaponto");

  $dados['fopo_idpessoas'] 			          = $_POST['idpessoas'];
  $dados['fopo_horario'] 				          = $util->dgr($_POST['fopo_horario']);
  $dados['fopo_entrada_saida'] 				    = $util->sgr($_POST['fopo_entrada_saida']);
  $dados['fopo_idusuario']                = $_SESSION['idusuario'];
  $dados['fopo_data_lancamento'] 		      = "NOW()";

    $db->gravar($dados);
    $ultimoID = $db->getUltimoID();
    $_SESSION['mensagem'] = "Lançamento efetuada com sucesso!";
    $_SESSION['tipoMsg'] = "info";
    header('location:../_Lancamentos/lancar_ponto.php?idcadastro=' . $ultimoID);
    exit;
  }
