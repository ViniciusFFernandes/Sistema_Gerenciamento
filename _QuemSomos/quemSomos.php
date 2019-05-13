<?php
  include_once '../_BD/conecta_login.php';
   $pagina = "quemsomos.php"
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="../js/_Worker/attAcessos_workers.js"></script>
    <script src="../js/_Chat/chat.js"></script>
    <script src="../js/_Worker/attChat_workers.js"></script>
  </head>

  <body>
    <div>
      <?php include "../menu.php"; ?>
    </div>

  	<div class="container">

  		<div class="row">
        <div class="col-md-3 col-sm-1"></div>
        <div class="col-md-6 col-sm-10">
          <div class="panel panel-default" style="border: solid 2px #828282; ">
            <div class="panel-heading" style="border-bottom: solid 1px #828282; " >
              <div class="panel-title">
                Desenvolvedores
              </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                      <table class="table" width="50%" align="center">
                        <tr>
                          <th><b>Nome</b></th>
                          <th><b>RA</b></th>
                        </tr>
                        <tr>
                          <td>Vinicius Ferreira Fernandes</td>
                          <td>208762</td>
                        </tr>
                      </table>
                </div>
            </div>
        </div>
      </div>
  	</div>
    <div class="col-md-3 col-sm-1"></div>
  </div>
</div>


     <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
     <script src="../js/bootstrap.min.js"></script>
     <?php
    //
    //Configurações e exibições do chat
    $chat->divChat();
?>
  </body>
</html>
