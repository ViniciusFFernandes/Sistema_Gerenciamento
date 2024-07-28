<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
// print_r($_POST);
// exit;
$paginaRetorno = 'programas.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT * FROM programas WHERE prog_tipo <> 'modelo'";
    //
    if ($_POST['pesquisa'] != "") {
      $sql .= " AND (idprogramas LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR prog_file LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . ")";
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idprogramas'] = "width='6%'";
    $dados['prog_file'] = "";
    //
    $cabecalho['Código'] = '';
    $cabecalho['Nome'] = '';
    //
    echo $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno, $cabecalho);
    exit;
  }


  if ($_POST['operacao'] == 'gravar'){
    //
    if ($_POST['id_cadastro'] <= 0) {
        mostraErro("Você não selecionou nenhum programa<br>Operação cancelada!");
        exit;
    }
    //
  	$db->setTabela("programas", "idprogramas");
    //
    unset($dados);
    $dados['id']                = $_POST['id_cadastro'];
  	$dados['prog_modelo'] 	    = $util->sgr($_POST['prog_modelo']);
    $db->gravarInserir($dados, true);
    //
    header('location:../_Configuracoes/' . $paginaRetorno . '?id_cadastro=' . $_POST['id_cadastro']);
    //
    exit;
}


 ?>
