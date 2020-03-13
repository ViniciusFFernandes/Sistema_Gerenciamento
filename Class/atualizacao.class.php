<?php
	class Atualizacao {
		private $ultimaVersao = 0.17;
		private $db;
		private $parametros;
		private $util;
		function __construct($db, $parametros, $util){
			$this->db = $db;
			$this->parametros = $parametros;
			$this->util = $util;
		}

		public function getUltimaVersao(){
			return $this->ultimaVersao;
		}

		public function geraHistorico(){
			$sql = "SELECT * FROM versao_hist ORDER BY idversao_hist DESC";
			$res = $this->db->consultar($sql);
			foreach ($res as $reg) {
				$hist .= '<div class="row">';
					$hist .= '<div class="col-sm-12 col-xs-12">';
						$hist .= '<div class="panel-group">';
							$hist .= '<div class="panel panel-default">';
							    $hist .= '<div class="panel-heading">' . $reg['vhist_versao'] . '<span style="float: right;">' . $this->util->convertData($reg['vhist_data']) . '</span></div>';
							    $hist .= '<div class="panel-body">' . $reg['vhist_mensagem'] . '</div>';
							$hist .= '</div>';
						$hist .='</div>';
					$hist .= '</div>';
				$hist .= '</div>';
			}
			//
			if(empty($hist)) $hist = "Nenhum histórico de atualização encontrado!<br>Os históricos são deletados a cada 60 dias.";
			//
			return $hist;
		}

		public function atualizarSistema($versaoAtual){
			$dados['executaNovamente'] = true;
			$versaoAtual += 0.01;
			$versaoFormatada = (str_pad(intval($versaoAtual), 2, "0", STR_PAD_LEFT)) . "." . (str_pad( round(($versaoAtual - intval($versaoAtual)), 2) * 100  , 2, "0", STR_PAD_LEFT));
			//
			$versao = '$this->versao_';
			$versao .= (str_pad(intval($versaoAtual), 2, "0", STR_PAD_LEFT));
			$versao .= "_";
			$versao .= (str_pad( round(($versaoAtual - intval($versaoAtual)), 2) * 100  , 2, "0", STR_PAD_LEFT));
			$versao .= "();";
			//echo $versao;
			$msg = eval("return " . $versao . ";");  // chama a funcao de atualizacao da versao.
			//
			if(empty($msg)){
				$msg = "Dados sobre a atualização não especificados!";
			}
			//
			if($versaoAtual >= $this->ultimaVersao) $dados['executaNovamente'] = false;
			$dados['novaVersao'] = $versaoFormatada;
			$dados['msg'] = $msg;
			$dados['executado'] = true;
			if($this->db->erro()){
				$dados['executaNovamente'] = false;
				$dados['executado'] = false;
				$dados['msg'] = "Erro: " . $this->db->getErro();
			}
			return $dados;
		}

		//////////////////////////////////////
		//Abaixo estão as versões do sistema//
		//////////////////////////////////////
		
		
		private function versao_00_17(){
			//
			// 24/02/2020 Vinicius
			//
			$this->parametros->cadastraParametros("sistema: nome da imagem para logo", "padrao.png", "Parametro usado para definir a logo do sistema", "constante", "LOGO_EMPRESA"); 
			//
			//Mensagem para o usuario
			return "Criação de parametro para definir a logo da empresa";
		}

		private function versao_00_16(){
			//
			// 24/02/2020 Vinicius
			//
			$this->parametros->cadastraParametros("sistema: busca atualizacoes automaticamente", "NAO", "Parametro usado para que o sistema se atualize sozinho", "parametro"); 
			//
			//Mensagem para o usuario
			return "Criação de parametro para atualizar o sistema automaticamente";
		}

		private function versao_00_15(){
			//
			// 20/02/2020 Vinicius
			//
			$this->parametros->cadastraParametros("sistema: data da ultima atualizacao", "", "Parametro usado para baixar novas versões do sistema", "parametro");
			$this->parametros->cadastraParametros("sistema: endereco do servidor ftp", "files.000webhost.com", "Parametro usado para se conectar no servidor ftp", "parametro"); 
			$this->parametros->cadastraParametros("sistema: usuario do servidor ftp", "sistematccbackup", "Parametro usado para se conectar no servidor ftp", "parametro");
			$this->parametros->cadastraParametros("sistema: senha do servidor ftp", "viniciusff1", "Parametro usado para se conectar no servidor ftp", "parametro"); 
			//
			//Mensagem para o usuario
			return "Criação de parametro de data da ultima atualização";
		}

		private function versao_00_14(){
			//
			// 02/02/2020 Vinicius
			//
			global $SERVIDOR;
			global $PORTA;
			global $USUARIO;
			global $SENHA;
			global $DB_NAME;

			$this->parametros->cadastraParametros("constante: endereco do banco de dados", $SERVIDOR, "Parametro usado para gerar o constante com os dados do banco de dados", "variavel", "SERVIDOR");
			$this->parametros->cadastraParametros("constante: porta do banco de dados", $PORTA, "Parametro usado para gerar o constante com os dados do banco de dados", "variavel", "PORTA");
			$this->parametros->cadastraParametros("constante: usuario do banco de dados", $USUARIO, "Parametro usado para gerar o constante com os dados do banco de dados", "variavel", "USUARIO");
			$this->parametros->cadastraParametros("constante: senha do banco de dados", $SENHA, "Parametro usado para gerar o constante com os dados do banco de dados", "variavel", "SENHA");
			$this->parametros->cadastraParametros("constante: nome da base do banco de dados", $DB_NAME, "Parametro usado para gerar o constante com os dados do banco de dados", "variavel", "DB_NAME");
			//
			//Mensagem para o usuario
			return "Criação de parametros com valores para conexão do banco";
		}
		
		private function versao_00_13(){
			//
			// 01/10/2019 Vinicius
			//
			$sql = "CREATE TRIGGER produtos_movto_insert 
						AFTER INSERT 
						ON produtos_movto
					   FOR EACH ROW
							BEGIN
								IF NEW.prmv_maismenos = '+' THEN
									 UPDATE produtos SET prod_qte_estoque = (prod_qte_estoque + NEW.prmv_qte) WHERE idprodutos = NEW.prmv_idprodutos; 
								END IF;
								
								IF NEW.prmv_maismenos = '-' THEN
									 UPDATE produtos SET prod_qte_estoque = (prod_qte_estoque - NEW.prmv_qte) WHERE idprodutos = NEW.prmv_idprodutos;
								END IF;	 												
					END";
			$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da TRIGGER para movimento de produtos";
		}

		private function versao_00_12(){
			//
			// 01/10/2019 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS produtos_movto(
						idprodutos_movto int(11) NOT NULL AUTO_INCREMENT,
						prmv_idprodutos int(11) NOT NULL,
						prmv_data datetime NOT NULL,
						prmv_idoperador int(11) NOT NULL,
						prmv_idorigem int(11) NOT NULL,
						prmv_origem varchar(255) NOT NULL,
						prmv_qte decimal(10,2) NULL,
						prmv_maismenos varchar(2) NULL,
						PRIMARY KEY (idprodutos_movto)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
			$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela produtos_movto";
		}

		private function versao_00_11(){
			//
			// 20/09/2019 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS producao_itens(
						idprodutcao_itens int(11) NOT NULL AUTO_INCREMENT,
						pdci_idproducao int(11) NOT NULL,
						pdci_idprodutos int(11) NOT NULL,
						pdci_qte decimal(10,2) NULL,
						pdci_perca decimal(10,2) NULL,
						pdci_qte_perca decimal(10,2) NULL,
						PRIMARY KEY (idprodutcao_itens)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
			$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela producao_itens";
		}

		private function versao_00_10(){
			//
			// 20/09/2019 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS producao(
						idproducao int(11) NOT NULL AUTO_INCREMENT,
						pdc_data_abertura datetime NULL,
						pdc_data_fechamento datetime NULL,
						pdc_situacao varchar(255) NOT NULL DEFAULT 'Aberta',
						pdc_idprodutos int NOT NULL,
						pdc_qte_produzida decimal(10,2) NULL,
						pdc_calcula_automatico varchar(5) NOT NULL DEFAULT 'SIM',
						PRIMARY KEY (idproducao)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
			$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela producao";
		}

		private function versao_00_09(){
			//
			// 18/09/2019 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS meio_pagto(
						idmeio_pagto int(11) NOT NULL AUTO_INCREMENT,
						mpag_nome varchar(255) NOT NULL,
						PRIMARY KEY (idmeio_pagto)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
			$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela meio_pagto";
		}

		private function versao_00_08(){
			//
			// 14/09/2019 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS forma_pagto(
						idforma_pagto int(11) NOT NULL AUTO_INCREMENT,
						forp_nome varchar(255) NOT NULL,
						forp_tipo varchar(255) NULL,
						forp_dias varchar(255) NULL,
						PRIMARY KEY (idforma_pagto)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
			$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela forma_pagto";
		}

		private function versao_00_07(){
			//
			// 02/09/2019 Vinicius
			//
			$this->parametros->cadastraParametros("empresa: permite trabalhar com estoque negativo", "NAO", "Parametro usado para bloquear operações de estoque quando o mesmo estiver zerado ou negativo", "parametro");
			$this->parametros->cadastraParametros("empresa: libera estoque negativo com senha", "NAO", "Parametro usado para permitir operações de estoque (negativo ou zerado) com senha", "parametro");
			$this->parametros->cadastraParametros("empresa: senha para liberacao do estoque", "senha não informada", "Senha usada para liberação do estoque quando negativo ou zerado", "parametro");
			//
			//Mensagem para o usuario
			return "Criação de parametros para manipulações de estoque";
		}

		private function versao_00_06(){
			//
			// 02/09/2019 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS produtos_formulas(
						idprodutos_formulas int(11) NOT NULL AUTO_INCREMENT,
						pfor_idproduto_final int(11) NOT NULL,
						pfor_idprodutos int(11) NOT NULL,
						pfor_qte decimal(10,2) NOT NULL,
						pfor_porc_perca decimal(10,2) NULL,
						PRIMARY KEY (idprodutos_formulas)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
			$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela produtos_formulas";
		}

		private function versao_00_05(){
			//
			// 29/08/2019 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS produtos(
						idprodutos int(11) NOT NULL AUTO_INCREMENT,
						prod_nome VARCHAR(255) NOT NULL,
						prod_idgrupos int(11) NULL,
						prod_idsubgrupos int(11) NULL,
						prod_idunidades int(11) NULL,
						prod_tipo_produto VARCHAR(255) NULL,
						prod_qte_estoque decimal(10,2) NOT NULL DEFAULT '0.00',
						prod_preco_tabela decimal(10,2) NULL,
						PRIMARY KEY (idprodutos)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
			$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela Produtos";
		}

		private function versao_00_04(){
			//
			// 21/08/2019 Vinicius
			//
			$this->parametros->cadastraParametros("sistema: data da ultima execucao de tarefas diarias", date('Y-m-d'), "Parametro usado para o sistema definir se deve ou não executar as tarefas diarias", "parametro");
			//
			//Mensagem para o usuario
			return "Cadastro do parametro para controle das tarefas diarias";
		}

		private function versao_00_03(){
			//
			// 21/08/2019 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS subgrupos(
						idsubgrupos int(11) NOT NULL AUTO_INCREMENT,
						subg_nome VARCHAR(255) NOT NULL,
						PRIMARY KEY (idsubgrupos)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
			$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela SubGrupos";
		}

		private function versao_00_02(){
			//
			// 21/08/2019 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS grupos(
						idgrupos int(11) NOT NULL AUTO_INCREMENT,
						grup_nome VARCHAR(255) NOT NULL,
						PRIMARY KEY (idgrupos)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
			$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela Grupos";
		}

		public function baixaAtualizacao($manual = false){
			$nomeArquivoAtz = 'sistema_atz.zip';
			$nomeArquivoAtz_local = '../sistema_atz.zip';
			$nomeArquivoAtzInfo = 'atz_info.txt';
			$nomeArquivoAtzInfo_local = '../atz_info.txt';
			$ftp_server = $this->parametros->buscaValor("sistema: endereco do servidor ftp"); 
			$ftp_user_name = $this->parametros->buscaValor("sistema: usuario do servidor ftp");
			$ftp_user_pass = $this->parametros->buscaValor("sistema: senha do servidor ftp"); 
			//
			//
			$conexaoFTP = ftp_connect($ftp_server);
			if($conexaoFTP === false && $manual){
				echo "Erro ao se conectar com o servidor!";
				return;
			}
			//
			// login
			$resultLogin = ftp_login($conexaoFTP, $ftp_user_name, $ftp_user_pass);
			if($resultLogin === false && $manual){
				echo "Erro ao efetuar login no servidor!";
				return;
			}
			//
			//			   
			$modoPassivo = ftp_pasv($conexaoFTP, true);
			if($modoPassivo === false && $manual){
				echo "Erro ao ativar modo passivo no servidor!";
				return;
			}
			//
			//
			if (ftp_get($conexaoFTP, $nomeArquivoAtzInfo_local, $nomeArquivoAtzInfo, FTP_ASCII)) {
				$infoAtz = file_get_contents($nomeArquivoAtzInfo_local);
				$dataUltimaAttSite = substr($infoAtz, 20, 20);
				$dataUltimaAttSistema = $this->parametros->buscaValor("sistema: data da ultima atualizacao");
				if (strtotime($dataUltimaAttSistema) >= strtotime($dataUltimaAttSite)) {
					//
					//Deleta os arquivos da atualização
					unlink($nomeArquivoAtzInfo_local);
					if($manual) echo "O seu sistema já está na versão mais recente!";
					return;
				}
			}else{
				if($manual) echo "Erro ao baixar arquivo de informações da atualização!";
				return;
			}
			//
			//Testou a data e a do sistema esta menor, então tenta atualiza
			if (ftp_get($conexaoFTP, $nomeArquivoAtz_local, $nomeArquivoAtz, FTP_BINARY)) {
				//
				//Descompacta o arquivo e atualiza o sistema
				$zip = new ZipArchive();
				if ($zip->open($nomeArquivoAtz_local) === true) {
						$zip->extractTo('../');
						$zip->close();
						//
						//Deleta os arquivos da atualização
						unlink($nomeArquivoAtzInfo_local);
						unlink($nomeArquivoAtz_local);
				}else{
					if($manual) echo "Erro ao abrir arquivo de atualização!";
					return;
				}
				//
				//Atualiza a data de atualização.
				unset($dados);
				$dados['id'] 			= $this->util->sgr('sistema: data da ultima atualizacao');
				$dados['para_valor'] 	= $this->util->sgr(date('Y-m-d H:i:s'));
				$this->parametros->gravaValor($dados);
				if($manual) echo "Atualização finalizada!.<br>Obrigado!";
				return;
			}else {
				if($manual) echo "Erro ao baixar arquivos de atualização do sistema!";
				return;
			}
		}

		public function gerarAtualizacao(){
			$nomeArquivoAtz = 'sistema_atz.zip';
			$nomeArquivoAtz_local = '../sistema_atz.zip';
			$nomeArquivoAtzInfo = 'atz_info.txt';
			$nomeArquivoAtzInfo_local = '../atz_info.txt';
			$ftp_server = $this->parametros->buscaValor("sistema: endereco do servidor ftp"); 
			$ftp_user_name = $this->parametros->buscaValor("sistema: usuario do servidor ftp");
			$ftp_user_pass = $this->parametros->buscaValor("sistema: senha do servidor ftp"); 
			//
			// Cria uma pasta compactada
			$zip = new ZipArchive;
			$zip->open($nomeArquivoAtz_local, ZipArchive::CREATE);
			//
			$dir = new DirectoryIterator('../');
			foreach($dir as $file){
				if(!$file->isDot()){
					if($file->isDir()){
						$caminho = $file->getPathname();
						//
						//abre o diretorio
						$ArquivosSub = new DirectoryIterator($caminho);
						//
						//troca a barra invertida
						$caminho = str_replace("\\", "/", $caminho);
						//
						//ignora as pastas do git
						if(strpos($caminho, "git")) continue;
						foreach($ArquivosSub as $file){
							if(!$file->isDot()){
								if( $file->isFile()){
									//
									$fileName = $file->getFilename();
									//
									//Salva o nome para pegar o arquivo
									$arquivoLocal = $caminho . "/" . $fileName;
									//
									//Salva o nome que sera no zip e remove o ../
									$arquivoZip = $caminho . "/" . $fileName;
									$arquivoZip = str_replace("../", "", $arquivoZip);
									//
									//Inclui no zip
									$zip->addFile($arquivoLocal, $arquivoZip);
								}
							}
						}
					}
				}
				//
				// listando somente os arquivos do diretório
				if($file->isFile()){
					// atribui o nome do arquivo a variável
					$fileName = $file->getFilename();
					//
					//ignora os arquivos do git e o arquivo de log de erros
					if(strpos($fileName, "git")) continue;
					if(strpos($fileName, "README")) continue;
					if(strpos($fileName, "erro")) continue;
					//
					//Salva o nome para pegar o arquivo
					$arquivoLocal = "../" . $fileName;
					//
					//Salva o nome que sera no zip e remove o ../
					$arquivoZip = $fileName;
					//
					//Inclui no zip
					$zip->addFile($arquivoLocal, $arquivoZip);

				}
			}
			//
			// Fecha o objeto. Necessário para gerar o arquivo zip final.
			$zip->close();
			//
			//Gera o arquivo de informações
			$arquivoAtzInfo = fopen($nomeArquivoAtzInfo_local, "a");
			//
			$textoArquivoAtzInfo = "Ultima Atualizacao: " . date("Y-m-d H:i:s") . "\n";
			$textoArquivoAtzInfo .= "Ultima versao: " . $this->getUltimaVersao() . "\n";
			$textoArquivoAtzInfo .= "-----------Dados do responsavel pela geracao desta versao-----------\n";
			$textoArquivoAtzInfo .= "Usuario: " . $this->db->retornaUmCampoID("pess_nome", "pessoas", $_SESSION['idusuario']) . "\n";
			$textoArquivoAtzInfo .= "Id do Usuario: " .$_SESSION['idusuario'] . "\n";
			$textoArquivoAtzInfo .= "Ip da maquina: " . $_SERVER["REMOTE_ADDR"] . "\n";
			//
			fwrite($arquivoAtzInfo, $textoArquivoAtzInfo);
			fclose($arquivoAtzInfo);
			//
			$conexaoFTP = ftp_connect($ftp_server);
			if($conexaoFTP === false && $manual){
				echo "Erro ao se conectar com o servidor!";
				return;
			}
			//
			// login
			$resultLogin = ftp_login($conexaoFTP, $ftp_user_name, $ftp_user_pass);
			if($resultLogin === false && $manual){
				echo "Erro ao efetuar login no servidor!";
				return;
			}
			//
			//Envia o arquivo de atualização
			$enviou = ftp_put($conexaoFTP, $nomeArquivoAtz, $nomeArquivoAtz_local, FTP_BINARY);
			if(!$enviou){
				//
				//Deleta os arquivos da atualização
				unlink($nomeArquivoAtzInfo_local);
				unlink($nomeArquivoAtz_local);
				//
				echo "Erro ao enviar arquivo de atualização do sistema!";
				return;
			}
			//
			//Envia o arquivo de informações	
			$enviou = ftp_put($conexaoFTP, $nomeArquivoAtzInfo, $nomeArquivoAtzInfo_local, FTP_ASCII);	
			if($enviou){
				//
				//Deleta os arquivos da atualização
				unlink($nomeArquivoAtzInfo_local);
				unlink($nomeArquivoAtz_local);
				//
				echo "Nova versão gerada e enviada ao servido com sucesso!<br>Obrigado!";	
				return;
			}else{
				//
				//Deleta os arquivos da atualização
				unlink($nomeArquivoAtzInfo_local);
				unlink($nomeArquivoAtz_local);
				//
				echo "Erro ao enviar arquivo com informações sobre a atualização!";	
				return;
			}   
		}

		private function cadastraPrograma($file, $tipo_origem = '', $nome = '', $tipo = 'programa', $imagem = '', $tipo_menu = ''){
			//
			if($nome == '') $nome = $file;
			//
			$this->db->setTabela("programas", "idprogramas");
			$dados['prog_nome']        = $this->util->sgr($nome);
			$dados['prog_file']        = $this->util->sgr($file);
			$dados['prog_tipo']        = $this->util->sgr($tipo);
			$dados['prog_imagem']      = $this->util->sgr($imagem);
			$dados['prog_tipo_origem'] = $this->util->sgr($tipo_origem);
			$dados['prog_tipo_menu']   = $this->util->sgr($tipo_menu);
			$this->db->gravarInserir($dados);
		}
	}

?>
