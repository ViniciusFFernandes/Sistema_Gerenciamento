<?php
   include_once '../_BD/conecta_login.php';
   $pagina = "lancamentos.php";
?>

<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
   	<title>Trabalho de desenvolvimento web</title>

    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/padrao.css" rel="stylesheet">
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/jquery.mask.js"></script>
    <script src="../js/_Lancamentos/folhaPonto.js"></script>
    <script src="../js/_Worker/attAcessos_workers.js"></script>
    <script src="../js/_Chat/chat.js"></script>
    <script src="../js/_Worker/attChat_workers.js"></script>
  </head>
  <body>

    <div>
      <?php include "../menu.php"; ?>
    </div>
<div class="container">
      <div class="row" style="margin: 1px;">
        <div class="col-md-4 col-sm-1"></div>
          <div class="col-md-4 col-sm-10">
            <?php  if (isset($_SESSION['mensagem'])) {
                      $util->mostraMensagem($_SESSION['tipoMsg'], $_SESSION['mensagem'], $_REQUEST['idcadastro']);
      				        unset($_SESSION['mensagem'], $_SESSION['tipoMsg']);
      			}
                   ?>
          <div class="col-md-4 col-sm-1"></div>
        </div>
      </div>

            <div class="panel panel-default" style="border: solid 2px #828282; ">
              <div class="panel-heading" style="border-bottom: solid 1px #828282; " >
                <div class="panel-title">
                  Folha de Ponto
                </div>
              </div>
              <div class="panel-body">
                <form action="lancar_ponto_grava.php" id="lancar_ponto" method="POST" enctype="multipart/form-data">
                  <div class="row">
                      <div class="col-md-4 col-sm-4 col-xs-12">
                        <label for="fopo_idpessoas">Funcionaio</label>
                        <?php
                          $util->comboboxSql("pess_nome", "idpessoas", "pess_funcionario = 'SIM'", $db, "pessoas");
                        ?>
                      </div>
                      <div class="col-md-4 col-sm-4 col-xs-12">
                        <label for="fopo_horario">Horario<span class="Obs_claro">(dd/mm/yyyy hh:mm)</span>  <button type="button" class="btn btn-info" style="padding:0px 2px;" onclick="setaDataHora()">H</button></label>
                        <input type="text" class="form-control" id="fopo_horario" name="fopo_horario">
                      </div>
                      <div class="col-md-4 col-sm-4 col-xs-12">
                        <label for="fopo_entrada_saida">Operação<span class="Obs_claro"></span></label><br>
                        <label class="radio-inline"><input type="radio" name="fopo_entrada_saida" value="entrada" checked>Entrada</label><br>
                        <label class="radio-inline"><input type="radio" name="fopo_entrada_saida" value="saida">Saida</label>
                      </div>
                  </div>
                  <br>
                  <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                      <input type="hidden" class="form-control" id="operacao" name="operacao" value="gravar">
                    <!-- <center><button type="button" class="btn btn-success" onclick="testaDados()">Cadastrar-se</button></center> -->
                    <center><button type="button" onclick="testaDados()" class="btn btn-success">Gravar</button></center>
                    <center><button type="reset" style="margin-top: 5px;" class="btn btn-info">Limpar</button></center>
                  </div>
                </div>
                </form>
              </div>
          </div>
  	</div>
</div>
<?php
    //
    //Configurações e exibições do chat
    $chat->divChat();
?>
  </body>
</html>
