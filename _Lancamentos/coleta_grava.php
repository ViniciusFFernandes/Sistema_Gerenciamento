<?php
require_once("../_BD/conecta_login.php");
require_once("tabelas.class.php");
require_once("estoque.class.php");
// print_r($_REQUEST);
// exit;
$paginaRetorno = 'coleta_edita.php';
//
  if ($_POST['operacao'] == "buscaCadastro") {
    $sql = "SELECT *, DATE_FORMAT(cole_data_entrada, '%d/%m/%Y') AS cole_entrada
            FROM coleta 
              LEFT JOIN produtos ON (idprodutos = cole_idprodutos)";

    if ($_POST['pesquisa'] != "") {
        $sql .= " WHERE idcoleta LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR cole_idprodutos LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                  OR prod_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idcoleta'] = "width='6%'";
    $dados['prod_nome'] = "";
    $dados['cole_entrada'] = "width='15%'";
    $dados['cole_situacao'] = "width='10%'";
    //
    $cabecalho['Código'] = '';
    $cabecalho['Produto'] = '';
    $cabecalho['entrada'] = '';
    $cabecalho['Situação'] = '';
    //
    echo $tabelas->geraTabelaBusca($res, $db, $dados, $paginaRetorno, $cabecalho);
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location: ../_Lancamentos/' . $paginaRetorno);
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
  	$db->setTabela("coleta", "idcoleta");
    //
    if(empty($_POST['cole_data_entrada'])){
      $dataEntrada = " NOW() ";
    }else{
      $dataEntrada = $util->sgr($_POST['cole_data_entrada']);
    }
    //
    //
    unset($dados);
    $dados['id']                        = $_POST['id_cadastro'];
  	$dados['cole_data_entrada']         = $dataEntrada;
    $dados['cole_idprodutos']           = $util->igr($_POST['cole_idprodutos']);
    $dados['cole_qte']                  = $util->vgr($_POST['cole_qte']);
    $dados['cole_placa_veiculo']        = $util->sgr($_POST['cole_placa_veiculo']);
    $db->gravarInserir($dados, true);
    //
  	if ($_POST['id_cadastro'] > 0) {
      $id = $_POST['id_cadastro'];
    }else{
      $id = $db->getUltimoID();
    }
    header('location: ../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $id);
    exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("coleta", "idcoleta");
    $db->excluir($_POST['id_cadastro'], "Excluir");
    if($db->erro()){
        $html->mostraErro("Erro ao excluir coleta<br>Operação cancelada!");
        exit;
    }
    header('location:../_Lancamentos/' . $paginaRetorno);
    exit;
  }

if ($_POST['operacao'] == 'fechar'){
  //
    $estoque = new estoque($db, $util, $html);
    //
    if($db->retornaUmCampoID("cole_situacao", "coleta", $_POST['id_cadastro']) == "Fechada"){
            $html->mostraErro("Esta coleta já está fechada!");
            exit;
        }
    //
    $sql = "SELECT * FROM coleta WHERE idcoleta = {$_POST['id_cadastro']}";
    $reg = $db->retornaUmReg($sql);
    //
    $db->beginTransaction();
    //
    //Gera o movimento de estoque
    $estoque->geraMovimento($reg['cole_idprodutos'], "+", $reg['cole_qte'], basename($_SERVER['PHP_SELF']), $reg['idcoleta']);
    //
    //
    $db->setTabela("coleta", "idcoleta");
    //
    unset($dados);
    $dados['id']                        = $reg['idcoleta'];
    $dados['cole_situacao'] 	        = $util->sgr("Fechada");
    //
    $db->gravarInserir($dados, true);
    if($db->erro()){
        $db->rollBack();
        $html->mostraErro("Erro ao fechar coleta!<br>Operação cancelada!");
        exit;
        }
    $db->commit();
    header('location: ../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $reg['idcoleta']);
    exit;
}

if ($_POST['operacao'] == 'reabrir'){
    //
    $estoque = new estoque($db, $util, $html);
    //
    if($db->retornaUmCampoID("cole_situacao", "coleta", $_POST['id_cadastro']) == "Aberta"){
            $html->mostraErro("Esta coleta já está aberta!");
            exit;
        }
    //
    $sql = "SELECT * FROM coleta WHERE idcoleta = {$_POST['id_cadastro']}";
    $reg = $db->retornaUmReg($sql);
    //
    $db->beginTransaction();
    //
    $permiteEstoqueNegativo = $parametros->buscaValor("empresa: permite trabalhar com estoque negativo");
    //
    $qteEstoque = $db->retornaUmCampoID("prod_qte_estoque", "produtos", $reg['cole_idprodutos']);
    $novoEstoque = $qteEstoque - $reg['cole_qte'];
    if($novoEstoque < 0 AND $permiteEstoqueNegativo == "NAO"){
        $db->rollBack();
        $html->mostraErro("Operação atual reduzira seu estoque para um valor negativo!<br>Operação cancelada!");
        exit;
    }
    //
    //Gera o movimento de estoque
    $estoque->geraMovimento($reg['cole_idprodutos'], "-", $reg['cole_qte'], basename($_SERVER['PHP_SELF']), $reg['idcoleta']);
    //
    $db->setTabela("coleta", "idcoleta");
    //
    unset($dados);
    $dados['id']                  = $reg['idcoleta'];
    $dados['cole_situacao'] 	  = $util->sgr("Aberta");
    //
    $db->gravarInserir($dados, true);
    if($db->erro()){
        $db->rollBack();
        $html->mostraErro("Erro ao reabrir coleta!<br>Operação cancelada!");
        exit;
        }
    $db->commit();
    header('location: ../_Lancamentos/' . $paginaRetorno . '?id_cadastro=' . $reg['idcoleta']);
    exit;
}

echo "Operação enviada: " . $_REQUEST['operacao'];
echo "Erro ao executar, operação não encontarda!";
exit;
 ?>
