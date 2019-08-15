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
    <script src="../js/jquery.mask.js"></script>
    <script src="../js/_Relatorios/sessoesativas.js"></script> 
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
			<div class="col-md-12 col-sm-12 col-xs-12 table-responsive" id="relatorio">
				<center><img src="../imagens/carregando.gif"><br>
				<span style="font-size: 18px; font-family: arial;">Carregando...</span></center>
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
