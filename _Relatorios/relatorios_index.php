<?php
   include_once '../_BD/conecta_login.php';
   $pagina = "relatorios.php";
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
    <script src="../js/html5shiv.min.js"></script>
    <script src="../js/respond.min.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/_Worker/attAcessos_workers.js"></script>
    <script src="../js/_Chat/chat.js"></script>
    <script src="../js/_Worker/attChat_workers.js"></script>
    <script type="text/javascript">
      function direciona(link){
        $(location).attr('href', link);
      }
    </script>
  </head>
  <style>
    #divLink:hover{
      background-color: #e2e2e2;
    }

    #divLink{
      padding: 30px;
      cursor: pointer;
      display: -webkit-flex;
      display: flex;
      -webkit-align-items: center;
      align-items: center;
      -webkit-justify-content: center;
      justify-content: center;
      font-size: 16px;
    }
  </style>
  <body>

    <div>
      <?php include "../menu.php"; ?>
    </div>
<div class="container">
    <div class="row">
        <div class="col-md-3 col-sm-3 col-xs-12" id="divLink" onclick="direciona('rel_folha_ponto.php')">
            <img src="../icones/lista.png"> &nbsp; Folha de Ponto
        </div>
        <div class="col-md-3 col-sm-3 col-xs-12" id="divLink" onclick="direciona('rel_sessoes_ativas.php')">
            <img src="../icones/lista.png"> &nbsp; Sessões Ativas
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
