<?php
	class Atualizacao {
		private $ultimaVersao = 0.09;
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
			return $dados;
		}

		//////////////////////////////////////
		//Abaixo estão as versões do sistema//
		//////////////////////////////////////

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
			$this->parametros->cadastraParametros("empresa: permite trabalhar com estoque negativo", "false", "Parametro usado para bloquear operações de estoque quando o mesmo estiver zerado ou negativo");
			$this->parametros->cadastraParametros("empresa: libera estoque negativo com senha", "false", "Parametro usado para permitir operações de estoque (negativo ou zerado) com senha");
			$this->parametros->cadastraParametros("empresa: senha para liberacao do estoque", "senha não informada", "Senha usada para liberação do estoque quando negativo ou zerado");
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
						prod_qte_estoque decimal(10,2) NULL,
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

	}


?>
