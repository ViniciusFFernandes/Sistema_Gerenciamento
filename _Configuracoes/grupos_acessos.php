<?php
  include_once("../_BD/conecta_login.php");
  
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
                        <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 5px;">
                          <div class="input-group">
                            <span class="input-group-addon"><img src="../icones/lupa.png"></span>
                            <input type="text" class="form-control" name="consulta" id="consulta" placeholder="consultar">
                          </div>
                        </div>
                      </div>
                      <div class="row" align="center">
                        <div class="col-md-12 col-sm-12 col-xs-12" style="margin-bottom: 5px;">
                          <button class="btn btn-success" onclick="consultarProgramas()">Consultar</button>
                        </div>
                      </div>
                      <div class="row" align="center">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                          ##tabelaProgramas##
                        </div>
                      </div>';
    //
    $sql = "SELECT * 
            FROM grupos_acessos_programas 
              JOIN programas ON (gap_idprogramas = idprogramas)
            WHERE gap_idgrupos_acessos = " . $reg['idgrupos_acessos'];
    
    if ($_POST['pesquisa'] != "") {
        $sql .= " AND prog_file LIKE " . $util->sgr("%" . $_POST['pesquisa'] ."%");
    }

    $sql .= " ORDER BY prog_tipo, prog_tipo_origem, prog_nome";
    //
    $res = $db->consultar($sql);
    //
    if(empty($res)){
      $tabelaProgramas = "Nenhum registro encontrado!";
    }else{
      //Pega o primeiro tipo
      $tipoOrigem = $res[0]["prog_tipo_origem"];
      $tipoPrograma = $res[0]["prog_tipo"];
      $tabelaProgramas = "<table class='table' style='margin-top: 3px;' >";
      $tabelaProgramas .= "<caption class='captionTable' style='border-top-left-radius: 8px;border-top-right-radius: 8px;'>" . ucfirst($tipoPrograma) . "</caption>";
      $tabelaProgramas .= "<tr class='cabecalhoTable'>";
      $tabelaProgramas .= "<td colspan='2'><b>{$tipoOrigem}</b></td>";
      $tabelaProgramas .= "</tr>";
      foreach($res as $regProg){
        $class = '';
        if ($linhaColorida) {
          $class = "class='info'";
          $linhaColorida = false;
        }else{
          $linhaColorida = true;
        }
        if($tipoPrograma != $regProg["prog_tipo"]){
          $tabelaProgramas .= "</table>";
          $tabelaProgramas .= "<table class='table' style='margin-top: 3px;' cellpadding='3px' cellspacing='0' border='0'>";
          $tabelaProgramas .= "<caption class='captionTable' style='border-top-left-radius: 8px;border-top-right-radius: 8px;'>" . ucfirst($regProg["prog_tipo"]) . "</caption>";
          $tipoPrograma = $regProg["prog_tipo"];
          $tipoOrigem = "####";
        }
        if($tipoOrigem != $regProg["prog_tipo_origem"]){
          $tabelaProgramas .= "<tr class='cabecalhoTable'>";
          $tabelaProgramas .= "<td colspan='2'><b>{$regProg["prog_tipo_origem"]}</b></td>";
          $tabelaProgramas .= "</tr>";
          $tipoOrigem = $regProg["prog_tipo_origem"];
        }
        $tabelaProgramas .= "<tr {$class}>";
        $tabelaProgramas .= "<td>{$regProg['prog_nome']}</td>";
        if($regProg['gap_executa'] == 1){
          $btnAtivaDesativa = '<button type="button" onclick="ativarDesativar(\'Desativar\', ' . $regProg["idgrupos_acessos_programas"] . ')" class="btn btn-danger">Desativar</button>';
        }else{
          $btnAtivaDesativa = '<button type="button" onclick="ativarDesativar(\'Ativar\', ' . $regProg["idgrupos_acessos_programas"] . ')" class="btn btn-success">Ativar</button>';
        }
        $tabelaProgramas .= "<td align='right' id='btn_{$regProg["idgrupos_acessos_programas"]}'>{$btnAtivaDesativa}</td>";
        $tabelaProgramas .= "</tr>";
      }
      $tabelaProgramas .= "</table>";
    }
    //
    if($reg["grac_inativo"] == 1){
      $btnInativar = '<button type="button" onclick="chamaGravar(\'Ativar\')" class="btn btn-primary">Ativar</button>';
    }else{
      $btnInativar = '<button type="button" onclick="chamaGravar(\'Inativar\')" class="btn btn-warning">Inativar</button>';
    }
    
  }  
  //
  //Abre o arquivo html e Inclui mensagens e trechos php
  $html = $util->buscaHtml("", $parametros);
  $html = str_replace("##Mensagem##", $msg, $html);
  $html = str_replace("##id_cadastro##", $reg['idgrupos_acessos'], $html);
  $html = str_replace("##grac_nome##", $reg['grac_nome'], $html); 
  $html = str_replace("##listaProgramas##", $listaProgramas, $html);
  $html = str_replace("##tabelaProgramas##", $tabelaProgramas, $html);
  $html = str_replace("##btnInativar##", $btnInativar, $html);
  echo $html;
  exit;
?>