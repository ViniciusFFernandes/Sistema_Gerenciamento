<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
require_once("salarios.class.php");
// print_r($_REQUEST);
// exit;
$paginaRetorno = 'salarios_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT *,  format(sala_vlr_total,2,'de_DE') sala_vlr_total, DATE_FORMAT(STR_TO_DATE(sala_data, '%Y-%m-%d'), '%d/%m/%Y') as sala_data
            FROM salarios";
    //
    if ($_POST['pesquisa'] != "") {
        $sql .= " WHERE idsalarios LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR sala_data LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idsalarios'] = "width='6%'";
    $dados['sala_data'] = "";
    $dados['sala_vlr_total'] = "align='right'";
    //
    $cabecalho['Código'] = '';
    $cabecalho['Data'] = '';
    $cabecalho['Total Folha'] = '';
    //
    echo $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno, $cabecalho);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location: ../_Lancamentos/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("salarios", "idsalarios");
    //
    if(empty($_POST['sala_data'])){
      $dataAbertura = " NOW() ";
    }else{
      $dataAbertura = $util->dgr($_POST['sala_data']);
    }
    //
    unset($dados);
    $dados['id']                    = $_POST['id_cadastro'];
  	$dados['sala_data']             = $dataAbertura;
    $dados['sala_vlr_total']        = $util->vgr($_POST['sala_vlr_total']);
    if($_POST['id_cadastro'] <= 0){
      $dados['sala_situacao']        = $util->sgr("Pendente");
    }
    //
    $db->gravarInserir($dados, true);
    //
  	if ($_POST['id_cadastro'] > 0) {
      $id = $_POST['id_cadastro'];
    }else{
      $id = $db->getUltimoID();
      //
      $salarios = new salarios($db);
      $salarios->insereFuncionarios($id);
    }
    header('location: ../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $id);
    exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("salarios_funcionarios", "safu_idsalarios");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir salarios de funcionarios<br>Operação cancelada!");
        exit;
    }
    //
    $db->setTabela("salarios", "idsalarios");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir salarios<br>Operação cancelada!");
        exit;
    }
    header('location:../_Lancamentos/' . $paginaRetorno);
    exit;
  }

if ($_POST['operacao'] == 'fechar'){
  header('location: ../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $reg['idsalarios']);
  exit;
}

if ($_POST['operacao'] == 'reabrir'){
  header('location: ../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $reg['idsalarios']);
  exit;
}

echo "Operação enviada: " . $_REQUEST['operacao'];
echo "Erro ao executar, operação não encontarda!";
exit;
 ?>
