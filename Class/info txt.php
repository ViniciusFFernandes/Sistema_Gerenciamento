<?
 require_once("conecta.php");
 
 ob_end_clean();	
 set_time_limit(4500);
 //
 require_once("./m_funcoes/funcoes.php");
 
?>
   <script language="javascript">

		
		var controlaFim = 0;
		function posFinal() {   		
			  if (controlaFim == 0) {
				  window.scrollTo(0,document.body.scrollHeight);
				  //
				  setTimeout(function(){ posFinal(); }, 2000);
				  console.log('indo para o fim');
			  }
		}
		
		setTimeout(function(){ posFinal(); }, 300);


   </script>
   
   

<? 


function callback($buffer) {
  // replace all the apples with oranges
  ob_flush();
  //flush();
  
  $buffer = (str_replace(chr(8), '', $buffer));
  $buffer = (str_replace(chr(10), '<br>', $buffer));
  $buffer = (str_replace(chr(13), '', $buffer));
  
  return utf8_encode($buffer);
}


	if ($_REQUEST["operacao"] == 'moreira') {
		//
		echo "Baixando de moreirasistemas.net.br <br>";
		echo "<br>";
		 
		 //echo $nomeArquivo . '<BR>';
		 //print_r($_POST);
		 //exit(0);
		 
		 //
		 //  Testes com Ftp
		 //
		 $nomeArquivo = 'infoweb_atz.zip';
		 $ftp_server = "moreirasistemas.net.br";
		 $ftp_user_name = "ftp_infoweb@moreirasistemas.net.br";
		 $ftp_user_pass = "Infoweb2013";
		 $conn_id = ftp_connect($ftp_server) or die("Erro ao conectar ao servidor");
		 echo "<br>Conectou";
		 flush();
		
			// login with username and password
			$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass) or die("Erro ao fazer login no servidor");
			echo "<br>Fez Login";
			flush();
		
			
			ftp_pasv($conn_id, true);
			echo "<br> Esta em modo ftp passivo";
			flush();
			
			if (ftp_get($conn_id, $nomeArquivo, $nomeArquivo, FTP_BINARY)) {
					echo "<br>Baixou atualizacao.";
					flush();
					$zip = new ZipArchive;
					if ($zip->open("./" . $nomeArquivo) === TRUE) {
							echo "<br>Abriu arquivo .zip - ok";
							$zip->extractTo('./');
							$zip->close();
							echo "<br><br>Atualizacao feita com sucesso";
					} else {
						//echo "<br>Erro ao abrir arquivo ZIP";
						//apaga_fontes();
						//
						//  Tenta baixar o rar.exe para descompactar via SO
						//
						if (!file_exists("rar.exe")) {
							ftp_get($conn_id, "rar.exe", "rar.exe", FTP_BINARY);
						}
						//
						// Desconpacta o arquivo.
						//
						ob_start("callback");
						
						$comando = "rar x -o+ " . $nomeArquivo . " " . $ATZ_SISDIR;
						system($comando, $retorno);
						echo "<Hr> Descompactou via SO <br><pre>" . nl2br($retorno) . "</pre>";
						
						ob_end_flush();
						//
						exit;
					}
			}
			else {
			  echo "Erro ao baixar arquivo de atualizacao.";
			  //apaga_fontes();
			  exit;
			}
	}
	
	
	

	if ($_REQUEST["operacao"] == 'intis') {
		//
		echo "Baixando de intisbkp.com.br <br>";
		echo "<br>";
		 
		 //echo $nomeArquivo . '<BR>';
		 //print_r($_POST);
		 //exit(0);
		 
		 $nomeArquivo = 'infoweb_atz.zip';
		 //
		//  Hostgator ou GoDaddy
		//
		$ftp_server="intisbkp.com.br"; 
		$ftp_user_name = 'infoweb_atz@intisbkp.com.br';
		$ftp_user_pass = 'Duckman21'; 
		//
		//
			//
			//  intisbkp  na Locaweb
			//
		 //
		 //  Testes com Ftp
		 //
		 
		 //$ftp_server = "ftp.intisbkp.com.br";
		 //$ftp_user_name = "intisbkp";
		 //$ftp_user_pass = "Duckman23!";
		 $conn_id = ftp_connect($ftp_server) or die("Erro ao conectar ao servidor");
		 echo "<br>Conectou";
		 flush();
		
			// login with username and password
			$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass) or die("Erro ao fazer login no servidor");
			echo "<br>Fez Login";
			flush();
		
			
			ftp_pasv($conn_id, true);
			//echo "<br> Esta em modo ftp passivo";
			flush();
			
			if (ftp_get($conn_id, $nomeArquivo, $nomeArquivo, FTP_BINARY)) {
					echo "<br>Baixou atualizacao.";
					flush();
					$zip = new ZipArchive;
					if ($zip->open("./" . $nomeArquivo) === TRUE) {
							echo "<br>Abriu arquivo .zip - ok";
							$zip->extractTo('./');
							$zip->close();
							echo "<br><br>Atualizacao feita com sucesso";
					} else {
						//echo "<br>Erro ao abrir arquivo ZIP";
						//apaga_fontes();
						//
						//  Tenta baixar o rar.exe para descompactar via SO
						//
						if (!file_exists("rar.exe")) {
							ftp_get($conn_id, "rar.exe", "rar.exe", FTP_BINARY);
						}
						//
						// Desconpacta o arquivo.
						//
						//ob_start("callback");
						
						$comando = "rar x -o+ " . $nomeArquivo . " " . $ATZ_SISDIR;
						system($comando, $retorno);
						//echo "<Hr> Descompactando via SO <hr><pre>" . nl2br($retorno) . "</pre>";
						//
						//ob_end_flush();
						//
						if ($retorno == '0') {
							echo '<a href="versao_atualizacao.php">
								<div style="padding:30px; border:solid 1px #033a1f; border-radius:8px; background-color:#08671f; color:#FFF; font-weight:bold" align="center" >';
							echo "Atualizar Sistema";
							echo "</div></a> <br><br><br><hr>";
						}
						//
						echo '   <script language="javascript"> 
						                  window.scrollTo(0,document.body.scrollHeight);
										  controlaFim = 1; </script> ';
						exit;
					}
			}
			else {
			  echo "Erro ao baixar arquivo de atualizacao.";
			  //apaga_fontes();
			  exit;
			}
	}


	
	
?>
