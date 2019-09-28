<?php
include_once("../_BD/conecta_login.php");
include_once("../Class/Tabelas.class.php");
// print_r($_POST);
// exit;
if($_POST['operacao'] == 'gravarItem'){
    $db->setTabela("produtos_formulas", "idprodutos_formulas");
    //
    unset($dados);
    $dados['pfor_idproduto_final']   = $util->igr($_POST['pfor_idproduto_final']);
    $dados['pfor_idprodutos']       = $util->igr($_POST['pfor_idprodutos']);
    $dados['pfor_qte']              = $util->vgr($_POST['pfor_qte']);
    $dados['pfor_porc_perca']       = $util->vgr($_POST['pfor_porc_perca']);
    //
    $ret = $db->gravarInserir($dados, false, true);
    //print_r($ret);
    // exit;
    //
    if($ret['retorno']){
        $sql = "SELECT prod_nome FROM produtos WHERE idprodutos = {$_POST['pfor_idprodutos']}";
        $nomeProd = $db->retornaUmCampoSql($sql, "prod_nome");
        ob_clean();
        $retorno = "<tr>";
        $retorno .= "<td>{$_POST['pfor_idprodutos']}</td>";
        $retorno .= "<td>{$nomeProd}</td>";
        $retorno .= "<td>{$_POST['pfor_qte']}</td>";
        $retorno .= "<td>{$_POST['pfor_porc_perca']}%</td>";
        $retorno .= "<td onclick='excluirItemFormula({$db->getUltimoID()})' style='cursor: pointer;'><img src='../icones/excluir.png'></td>";
        $retorno .= "</tr>";
        echo $retorno;
    }
}

if ($_POST['operacao'] == "buscaProdutos") {
    $sql = "SELECT * 
            FROM produtos 
              LEFT JOIN unidades ON (prod_idunidades = idunidades) 
              LEFT JOIN grupos ON (prod_idgrupos = idgrupos)";
    //
    if ($_POST['pesquisa'] != "") {
      $sql .= " WHERE idprodutos LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                OR prod_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%") . "
                OR grup_nome LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }
    //
    $res = $db->consultar($sql);
    $tabelas = new Tabelas();
    //
    unset($dados);
    $dados['idprodutos'] = "width='6%'";
    $dados['prod_nome'] = "";
    $dados['uni_sigla'] = "width='10%'";
    $dados['grup_nome'] = "width='20%'";
    //
    $tabelas->geraTabelaBusca($res, $db, $dados, "abreProdutos");
    exit;
  }

  if ($_POST['operacao'] == 'novoCadastro'){
    header('location:../_Cadastros/produtos_edita.php');
    exit;
    }

  if ($_POST['operacao'] == 'gravar'){
    $db->setTabela("produtos", "idprodutos");

    unset($dados);
    $dados['id']                     = $_POST['idprodutos'];
    $dados['prod_nome']              = $util->sgr($_POST['prod_nome']);
    $dados['prod_idgrupos']          = $util->igr($_POST['prod_idgrupos']);
    $dados['prod_idsubgrupos']       = $util->igr($_POST['prod_idsubgrupos']);
    $dados['prod_idunidades']        = $util->igr($_POST['prod_idunidades']);
    $dados['prod_tipo_produto']      = $util->sgr($_POST['prod_tipo_produto']);
    $dados['prod_qte_estoque']       = $util->vgr($_POST['prod_qte_estoque']);
    $dados['prod_preco_tabela']      = $util->vgr($_POST['prod_preco_tabela']);

    $db->gravarInserir($dados);


    if ($_POST['idprodutos'] > 0) {
      $id = $_POST['idprodutos'];
    }else{
      $id = $db->getUltimoID();
    }
    header('location:../_Cadastros/produtos_edita.php?idprodutos=' . $id);
    exit;
}

if ($_POST['operacao'] == "excluiCad") {
    $db->setTabela("produtos", "idprodutos");
    $db->excluir($_POST['idprodutos'], "Excluir");
    header('location:../_Cadastros/produtos_edita.php');
    exit;
  }

  if ($_POST['operacao'] == "excluirItemFormula") {
    $db->setTabela("produtos_formulas", "idprodutos_formulas");
    $db->excluir($_POST['idItemFormula']);
    exit;
  }

 ?>
