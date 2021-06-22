<?php
  require_once("../_BD/conecta_login.php");
  
  //Gera as mensagens porem nao exibe nenhuma
  unset($_SESSION['mensagem'], $_SESSION['tipoMsg']);
  //
  if(!empty($_REQUEST['id_cadastro'])){
    $sql = "SELECT * 
            FROM grupos_acessos 
            WHERE idgrupos_acessos = {$_REQUEST['id_cadastro']}";
    $reg = $db->retornaUmReg($sql);
  }
  //
  if(!empty($reg['idgrupos_acessos'])){
    //
    $listaProgramas = '<div class="row" style="margin-top: 5px;">
                        <div class="col-md-12 col-sm-12 col-12 pb-3">
                          <div class="input-group">
                            <span class="input-group-addon"><img src="../icones/lupa.png"></span>
                            <input type="text" class="form-control" name="consulta" id="consulta" placeholder="consultar">
                          </div>
                        </div>
                      </div>
                      <div class="row" align="center">
                        <div class="col-md-12 col-sm-12 col-12 pb-3">
                          <button class="btn btn-success" onclick="consultarProgramas()">Consultar</button>
                        </div>
                      </div>
                      <div class="row" align="center">
                        <div class="col-md-12 col-sm-12 col-12" id="listaProgramas">
                          <img src="../icones/carregando.gif" width="25px">Buscando programas... 
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12 col-sm-12 col-12" align=\'center\'>
                          <button type="button" onclick="ativarDesativarProgramas(\'Ativar\')" class="btn btn-success">Ativar Todos</button>
                          <button type="button" onclick="ativarDesativarProgramas(\'Desativar\')" class="btn btn-danger">Desativar Todos</button>
                        </div>
                      </div>';
    //
    if($reg["grac_inativo"] == 1){
      $btnInativar = '<button type="button" onclick="chamaGravar(\'Ativar\')" class="btn btn-primary mb-3">Ativar</button>';
    }else{
      $btnInativar = '<button type="button" onclick="chamaGravar(\'Inativar\')" class="btn btn-warning mb-3">Inativar</button>';
    }
    //
    $btnGerarMenu = '<button type="button" onclick="gerarMenu()" class="btn btn-primary mb-3" id="btnGerarMenu">Gerar Menu</button>';
  }  
  //
  //Abre o arquivo html e Inclui mensagens e trechos php
  $html = $html->buscaHtml("configuracoes");
  $html = str_replace("##Mensagem##", $msg, $html);
  $html = str_replace("##id_cadastro##", $reg['idgrupos_acessos'], $html);
  $html = str_replace("##grac_nome##", $reg['grac_nome'], $html); 
  $html = str_replace("##listaProgramas##", $listaProgramas, $html);
  $html = str_replace("##btnInativar##", $btnInativar, $html);
  $html = str_replace("##btnGerarMenu##", $btnGerarMenu, $html);
  echo $html;
  exit;
?>