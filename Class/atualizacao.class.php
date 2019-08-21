<?php
	class Atualizacao {
		private $ultimaVersao = 0.04;
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
			$dados['novaVersao'] = $versaoAtual;
			$dados['msg'] = $msg;
			return $dados;
		}

		//////////////////////////////////////
		//Abaixo estão as versões do sistema//
		//////////////////////////////////////

		private function versao_00_04(){
			//
			// 21/08/2019 Vinicius
			//
			$this->parametros->cadastraParametros("sistema: data da ultima execucao de tarefas diarias", date('Y-m-d'), "Parametro usado para o sistema definir se deve ou não executar as tarefas diarias");
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
						subg_descricao TEXT NULL,
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
						grup_descricao TEXT NULL,
						PRIMARY KEY (idgrupos)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
			$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela Grupos";
		}

	}


?>
