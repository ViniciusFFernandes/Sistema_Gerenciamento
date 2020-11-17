<?php
	require_once("parametros.class.php");
	require_once("html.class.php");
	require_once("util.class.php");

	class Atualizacao {
		private $ultimaVersao = 0.60;
		private $db;
		private $parametros;
		private $util;
		private $html;

		function __construct($db){
			$this->db = $db;
			$this->parametros = new Parametros($db);
			$this->util = new Util();
			$this->html = new html($db);
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
		//Exemplos de uso das funções       //
		//////////////////////////////////////
		//
		//$this->cadastraPrograma("arquivo_do_programa.php", 'Tipo no Grupo Acesso', 'Nome para o Menu',  'tipo: Programa ou Menu', 'Nome_da_imagem_menu.png', 'Qual Menu Aparece', Sepode ou Não Executar por Padrão: 0 ou 1);
		//
		//////////////////////////////////////
		//Abaixo estão as versões do sistema//
		//////////////////////////////////////

		private function versao_00_60(){
			//
			// 04/10/2020 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS salarios_funcionarios(
						idsalarios_funcionarios int(11) NOT NULL AUTO_INCREMENT,
						safu_idsalarios INT NULL,
						safu_idpessoas INT NULL,
						safu_dias INT NULL,
						safu_total DECIMAL(10,2) NULL,
						safu_idcontapag INT NULL,
						PRIMARY KEY (idsalarios_funcionarios)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
					$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela salarios de funcionarios";
		}

		private function versao_00_59(){
			//
			// 04/10/2020 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS salarios(
						idsalarios int(11) NOT NULL AUTO_INCREMENT,
						sala_data DATETIME NOT NULL,
						sala_vlr_total DECIMAL(10,2) NULL,
						sala_situacao VARCHAR(100) NULL,
						PRIMARY KEY (idsalarios)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
					$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela salarios";
		}

		private function versao_00_58(){
			//
			// 07/10/2020 Vinicius
			//
			$this->cadastraPrograma("salarios_edita.php", 'Lancamentos', 'Salários',  'menu', 'salarios.png', 'lancamentos');
			$this->cadastraPrograma("salarios_grava.php", 'Lancamentos');
			//
			//Mensagem para o usuario
			return "Criação do programa para incluir salarios";
		}

		private function versao_00_57(){
			//
			// 24/09/2020 Vinicius
			//
			$sql = "ALTER TABLE contapag ADD ctpg_recalculou VARCHAR(6) NULL";
			$this->db->executaSQL($sql); 
			//
			$sql = "ALTER TABLE contapag ADD ctpg_processou VARCHAR(6) NULL";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação do campo recalculou a conta e processor o vale/extra na tabela contas a pagar";
		}

		private function versao_00_56(){
			//
			// 04/10/2020 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS contapag_hist(
						idcontapag_hist int(11) NOT NULL AUTO_INCREMENT,
						cphi_idcontapag int(11) NOT NULL,
						cphi_operacao VARCHAR(255) NOT NULL,
						cphi_data DATETIME NOT NULL,
						cphi_valor DECIMAL(10,2) NULL,
						cphi_idoperador int(11) NOT NULL,
						cphi_data_pagto DATE NULL,
						cphi_idmeio_pagto INT NULL,
						cphi_idcc INT NULL,
						PRIMARY KEY (idcontapag_hist)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
					$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela contapag_hist";
		}

		private function versao_00_55(){
			//
			// 04/10/2020 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS contapag(
						idcontapag int(11) NOT NULL AUTO_INCREMENT,
						ctpg_idcliente int(11) NOT NULL,
						ctpg_vencimento DATE NOT NULL,
						ctpg_vlr_bruto decimal(10,2) NOT NULL,
						ctpg_vlr_desconto decimal(10,2) DEFAULT 0.00,
						ctpg_vlr_juros decimal(10,2) DEFAULT 0.00,
						ctpg_vlr_liquido DECIMAL(10,2) AS (ctpg_vlr_bruto + ctpg_vlr_juros - ctpg_vlr_desconto) STORED,
						ctpg_vlr_pago decimal(10,2) DEFAULT 0.00,
						ctpg_vlr_devedor DECIMAL(10,2) AS (ctpg_vlr_liquido - ctpg_vlr_pago) STORED,
						ctpg_a_vista VARCHAR(6) NULL,
						ctpg_porc_juros DECIMAL(10,2) DEFAULT 0.00,
						ctpg_porc_desconto DECIMAL(10,2) DEFAULT 0.00,
						ctpg_idmeio_pagto INT NULL,
						ctpg_idcc INT NULL,
						ctpg_idbancos INT NULL,
						ctpg_situacao VARCHAR(255) NULL,
						ctpg_inclusao DATE NULL,
						ctpg_idtipo_contas int(11) NULL,
						PRIMARY KEY (idcontapag)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
					$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela contapag";
		}

		private function versao_00_54(){
			//
			// 24/09/2020 Vinicius
			//
			$sql = "ALTER TABLE contarec ADD ctrc_a_vista VARCHAR(6) NULL";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação do campo à vista na tabela contas a receber";
		}

		private function versao_00_53(){
			//
			// 27/09/2020 Vinicius
			//
			$this->parametros->cadastraParametros("sistema: incluir contas a vista já quitadas", "NAO", "Parametro usado para definir se o sistema deverá incluir contas à vista já baixadas"); 
			//
			//Mensagem para o usuario
			return "Criação do parametro para criar contas à vista já quitadas";
		}

		private function versao_00_52(){
			//
			// 24/09/2020 Vinicius
			//
			$sql = "ALTER TABLE cc_lanctos ADD ccla_obs TEXT NULL";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação do campo observações na tabela lançamentos bancários";
		}

		private function versao_00_51(){
			//
			// 27/09/2020 Vinicius
			//
			//$this->parametros->cadastraParametros("sistema: incluir contas do tipo salario já quitadas", "NAO", "Parametro usado para definir se o sistema deverá incluir contas que sejam do tipo salario já baixadas"); 
			//
			//Mensagem para o usuario
			return "Criação do parametro para criar contas salarios já quitadas (Versão removida)";
		}

		private function versao_00_50(){
			//
			// 24/09/2020 Vinicius
			//
			$sql = "ALTER TABLE tipo_contas ADD tico_tipo_salario VARCHAR(6) NULL";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação do campo tipo salario na tabela tipode de contas";
		}

		private function versao_00_49(){
			//
			// 24/09/2020 Vinicius
			//
			$sql = "ALTER TABLE cc_lanctos ADD ccla_inclusao DATETIME NULL";
			$this->db->executaSQL($sql); 
			//
			$sql = "ALTER TABLE cc_lanctos ADD ccla_idmeio_pagto INT NULL";
			$this->db->executaSQL($sql); 
			//
			$sql = "ALTER TABLE cc_lanctos ADD ccla_idoperador INT NULL";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação dos campos inclusão, meio de pagamento e operador na tabela lançamentos bancários";
		}
		
		private function versao_00_48(){
			//
			// 27/09/2020 Vinicius
			//
			$this->parametros->cadastraParametros("sistema: gerar lancamentos bancarios pelas contas", "NAO", "Parametro usado para definir se o sistema deverá gerar lançamentos bancários ao baixar contas"); 
			//
			//Mensagem para o usuario
			return "Criação do parametro para gerar lançamentos bancarios";
		}

		private function versao_00_47(){
			//
			// 27/09/2020 Vinicius
			//
			$sql = "ALTER TABLE contarec ADD ctrc_idmeio_pagto INT NULL";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação do campo meio de pagamento na tabela contas a receber";
		}

		private function versao_00_46(){
			//
			// 27/09/2020 Vinicius
			//
			$sql = "ALTER TABLE contarec ADD ctrc_porc_desconto DECIMAL(10,2) DEFAULT 0.00";
			$this->db->executaSQL($sql); 
			//
			$sql = "ALTER TABLE contarec ADD ctrc_porc_juros DECIMAL(10,2) DEFAULT 0.00";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação dos campos porcentagem de juros e desconto na tabela contas a receber";
		}

		private function versao_00_45(){
			//
			// 27/09/2020 Vinicius
			//
			$sql = "ALTER TABLE contarec_hist ADD crhi_idcc INT NULL";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação do campo contas bancárias na tabela contas a receber historico";
		}

		private function versao_00_44(){
			//
			// 27/09/2020 Vinicius
			//
			$sql = "ALTER TABLE contarec ADD ctrc_idbancos INT NULL";
			$this->db->executaSQL($sql); 
			//
			$sql = "ALTER TABLE contarec ADD ctrc_idcc INT NULL";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação dos campos banco e conta bancárias na tabela contas a receber";
		}

		private function versao_00_43(){
			//
			// 27/09/2020 Vinicius
			//
			$sql = "ALTER TABLE contarec_hist ADD crhi_idmeio_pagto INT NULL";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação do campo meio de pagamento na tabela contas a receber historico";
		}

		private function versao_00_42(){
			//
			// 27/09/2020 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS cc_lanctos(
						idcc_lanctos int(11) NOT NULL AUTO_INCREMENT,
						ccla_idcc INT NOT NULL,
						ccla_data DATE NULL,
						ccla_tipo VARCHAR(10) NULL,
						ccla_valor DECIMAL(10,2) NULL,
						ccla_idcontapag INT NULL,
						ccla_idcontarec INT NULL,
						PRIMARY KEY (idcc_lanctos)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
					$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela lançamentos bancárias";
		}

		private function versao_00_41(){
			//
			// 27/09/2020 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS cc(
						idcc int(11) NOT NULL AUTO_INCREMENT,
						cc_nome VARCHAR(255) NOT NULL,
						cc_idbancos INT NULL,
						cc_agencia VARCHAR(100) NULL,
						cc_agencia_dg VARCHAR(20) NULL,
						cc_conta VARCHAR(100) NULL,
						cc_conta_dg VARCHAR(20) NULL,
						PRIMARY KEY (idcc)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
					$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela contas bancárias";
		}

		private function versao_00_40(){
			//
			// 27/09/2020 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS bancos(
						idbancos int(11) NOT NULL AUTO_INCREMENT,
						banc_nome VARCHAR(255) NOT NULL,
						PRIMARY KEY (idbancos)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
					$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela bancos";
		}

		private function versao_00_39(){
			//
			// 27/09/2020 Vinicius
			//
			$this->cadastraPrograma("bancos_edita.php", 'Cadastros', 'Bancos',  'menu', 'bancos.png', 'cadastros');
			$this->cadastraPrograma("bancos_grava.php", 'Cadastros');
			//
			$this->cadastraPrograma("cc_edita.php", 'Cadastros', 'Contas Bancárias',  'menu', 'cc.png', 'cadastros');
			$this->cadastraPrograma("cc_grava.php", 'Cadastros');
			//
			//Mensagem para o usuario
			return "Cadastro dos programas de bancos, contas bancárias e suas dependencias";
		}

		private function versao_00_38(){
			//
			// 24/09/2020 Vinicius
			//
			$sql = "ALTER TABLE contarec ADD ctrc_inclusao DATE NULL";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação do campo inclusão na tabela contas a receber";
		}

		private function versao_00_37(){
			//
			// 23/09/2020 Vinicius
			//
			$sql = "ALTER TABLE contarec_hist ADD crhi_data_pagto DATE NULL";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação do campo data de pagamento na tabela contas a receber historico";
		}

		private function versao_00_36(){
			//
			// 23/09/2020 Vinicius
			//
			$sql = "ALTER TABLE contarec ADD ctrc_situacao VARCHAR(255) NULL";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação do campo situacao na tabela contas a receber";
		}

		private function versao_00_35(){
			//
			// 23/09/2020 Vinicius
			//
			$sql = "ALTER TABLE tipo_contas ADD tico_tipo_extra VARCHAR(6) NULL";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação do campo tipo extra na tabela de tipo de contas";
		}

		private function versao_00_34(){
			//
			// 23/09/2020 Vinicius
			//
			$sql = "ALTER TABLE tipo_contas ADD tico_tipo_vale VARCHAR(6) NULL";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação do campos tipo vale na tabela de tipo de contas";
		}

		private function versao_00_33(){
			//
			// 23/09/2020 Vinicius
			//
			$this->cadastraPrograma("tipo_contas_edita.php", 'Cadastros', 'Tipo de Contas',  'menu', 'tipo_contas.png', 'cadastros');
			$this->cadastraPrograma("tipo_contas_grava.php", 'Cadastros');
			//
			//Mensagem para o usuario
			return "Cadastro dos programas tipo de contas e suas dependencias";
		}

		private function versao_00_32(){
			//
			// 20/09/2020 Vinicius
			//
			$this->cadastraPrograma("contarec_edita.php", 'Lançamentos', 'Contas a Receber',  'menu', 'contarec.png', 'lancamentos');
			$this->cadastraPrograma("contarec_grava.php", 'Lançamentos');
			//
			$this->cadastraPrograma("contapag_edita.php", 'Lançamentos', 'Contas a Pagar',  'menu', 'contapag.png', 'lancamentos');
			$this->cadastraPrograma("contapag_grava.php", 'Lançamentos');
			//
			$this->cadastraPrograma("pedidos_edita.php", 'Lançamentos', 'Pedidos',  'menu', 'pedidos.png', 'lancamentos');
			$this->cadastraPrograma("pedidos_grava.php", 'Lançamentos');
			//
			$this->cadastraPrograma("pedcompras_edita.php", 'Lançamentos', 'Pedidos de Compra',  'menu', 'pedcompras.png', 'lancamentos');
			$this->cadastraPrograma("pedcompras_grava.php", 'Lançamentos');
			//
			//Mensagem para o usuario
			return "Cadastro dos programas de lançamentos de contarec, contapag, pedidos e pedcompras e suas dependencias";
		}

		private function versao_00_31(){
			//
			// 15/09/2020 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS tipo_contas(
						idtipo_contas int(11) NOT NULL AUTO_INCREMENT,
						tico_nome VARCHAR(255) NOT NULL,
						PRIMARY KEY (idtipo_contas)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
					$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela tipo_contas";
		}

		private function versao_00_30(){
			//
			// 20/09/2020 Vinicius
			//
			$sql = "ALTER TABLE contarec ADD ctrc_idtipo_contas int(11) NULL";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação do campos tipo da conta na tabela de contas a receber";
		}

		private function versao_00_29(){
			//
			// 15/09/2020 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS contarec_hist(
						idcontarec_hist int(11) NOT NULL AUTO_INCREMENT,
						crhi_idcontarec int(11) NOT NULL,
						crhi_operacao VARCHAR(255) NOT NULL,
						crhi_data DATETIME NOT NULL,
						crhi_valor DECIMAL(10,2) NULL,
						crhi_idoperador int(11) NOT NULL,
						PRIMARY KEY (idcontarec_hist)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
					$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela contarec_hist";
		}

		private function versao_00_28(){
			//
			// 15/09/2020 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS contarec(
						idcontarec int(11) NOT NULL AUTO_INCREMENT,
						ctrc_idcliente int(11) NOT NULL,
						ctrc_vencimento DATE NOT NULL,
						ctrc_vlr_bruto decimal(10,2) NOT NULL,
						ctrc_vlr_desconto decimal(10,2) DEFAULT 0.00,
						ctrc_vlr_juros decimal(10,2) DEFAULT 0.00,
						ctrc_vlr_liquido DECIMAL(10,2) AS (ctrc_vlr_bruto + ctrc_vlr_juros - ctrc_vlr_desconto) STORED,
						ctrc_vlr_pago decimal(10,2) DEFAULT 0.00,
						ctrc_vlr_devedor DECIMAL(10,2) AS (ctrc_vlr_liquido - ctrc_vlr_pago) STORED,
						PRIMARY KEY (idcontarec)
					)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
					$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela contarec";
		}

		private function versao_00_27(){
			//
			// 29/06/2020 Vinicius
			//
			$this->cadastraPrograma("pessoas_ficha_associado.php", 'Impressões');
			//
			//Mensagem para o usuario
			return "Cadastro do programa para imprimir ficha de associado";
		}

		private function versao_00_26(){
			//
			// 29/06/2020 Vinicius
			//
			$this->parametros->cadastraParametros("empresa: nome da empresa", "", "Parametro usado para definir o nome da empresa"); 
			$this->parametros->cadastraParametros("empresa: cnpj da empresa", "", "Parametro usado para definir o cnpj da empresa"); 
			$this->parametros->cadastraParametros("empresa: endereco da empresa", "", "Parametro usado para definir o encereço da empresa"); 
			$this->parametros->cadastraParametros("empresa: cidade da empresa", "", "Parametro usado para definir o cidade da empresa"); 
			$this->parametros->cadastraParametros("empresa: uf da empresa", "", "Parametro usado para definir o uf da empresa"); 
			$this->parametros->cadastraParametros("empresa: CEP da empresa", "", "Parametro usado para definir o CEP da empresa"); 
			$this->parametros->cadastraParametros("empresa: telefone de contato da empresa", "", "Parametro usado para definir o telefone de contato da empresa"); 
			$this->parametros->cadastraParametros("sistema: nome da logo usada para relatorios", "", "Parametro usado para definir a logo que será usada nos relatórios"); 
			//
			//Mensagem para o usuario
			return "Criação de parametro para definir dados da empresa";
		}

		private function versao_00_25(){
			//
			// 27/06/2020 Vinicius
			//
			$sql = "ALTER TABLE pessoas ADD pess_idfuncoes int(11) NULL";
			$this->db->executaSQL($sql); 
			$sql = "ALTER TABLE pessoas ADD pess_idsetores int(11) NULL";
			$this->db->executaSQL($sql); 
			$sql = "ALTER TABLE pessoas ADD pess_associado varchar(5) NULL";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação dos campos para associado, setor e função das pessoas";
		}

		private function versao_00_24(){
			//
			// 27/06/2020 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS setores(
				idsetores int(11) NOT NULL AUTO_INCREMENT,
				set_nome VARCHAR(255) NOT NULL,
				PRIMARY KEY (idsetores)
			)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
			$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela setores";
		}

		private function versao_00_23(){
			//
			// 27/06/2020 Vinicius
			//
			$this->cadastraPrograma("setores_edita.php", 'Cadastros', 'Setores',  'menu', 'setores.png', 'cadastros');
			$this->cadastraPrograma("setores_grava.php", 'Cadastros');
			//
			//Mensagem para o usuario
			return "Cadastro do programa cadastro de setores e suas dependencias";
		}

		private function versao_00_22(){
			//
			// 27/06/2020 Vinicius
			//
			$sql = "CREATE TABLE IF NOT EXISTS funcoes(
				idfuncoes int(11) NOT NULL AUTO_INCREMENT,
				func_nome VARCHAR(255) NOT NULL,
				PRIMARY KEY (idfuncoes)
			)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
			$this->db->executaSQL($sql);
			//
			//Mensagem para o usuario
			return "Criação da tabela funções";
		}

		private function versao_00_21(){
			//
			// 26/06/2020 Vinicius
			//
			$this->cadastraPrograma("funcoes_edita.php", 'Cadastros', 'Funções',  'menu', 'funcoes.png', 'cadastros');
			$this->cadastraPrograma("funcoes_grava.php", 'Cadastros');
			//
			//Mensagem para o usuario
			return "Cadastro do programa cadastro de funcoes e suas dependencias";
		}

		private function versao_00_20(){
			//
			// 17/03/2020 Vinicius
			//
			$this->cadastraPrograma("grupos_acessos.php", 'Sistema', 'Grupos de Acessos',  'menu', '', 'configuracoes');
			$this->cadastraPrograma("grupos_acessos_grava.php", 'Sistema');
			//
			//Mensagem para o usuario
			return "Cadastro do programa grupos de acessos e suas dependencias";
		}
		
		private function versao_00_19(){
			//
			// 16/03/2020 Vinicius
			//
			$this->parametros->cadastraParametros("sistema: nome usado para o sistema", "vWeb", "Parametro usado para definir o nome usado para o sistema", "constante", "NOME_SISTEMA"); 
			//
			//Mensagem para o usuario
			return "Criação de parametro para definir a nome para o sistema";
		}

		private function versao_00_18(){
			//
			// 16/03/2020 Vinicius
			//
			$sql = "ALTER TABLE grupos_acessos ADD grac_inativo int(11) NULL";
			$this->db->executaSQL($sql); 
			//
			//Mensagem para o usuario
			return "Criação de campo para inativar grupo de acesso";
		}
		
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
								if($file->isDir()){
									$caminhoSub = $file->getPathname();
									//
									//abre o diretorio
									$ArquivosSub_Sub = new DirectoryIterator($caminhoSub);
									//
									//troca a barra invertida
									$caminhoSub = str_replace("\\", "/", $caminhoSub);
									foreach($ArquivosSub_Sub as $file_Sub){
										if(!$file_Sub->isDot()){
											if( $file_Sub->isFile()){
												//
												$fileName = $file_Sub->getFilename();
												//
												//Salva o nome para pegar o arquivo
												$arquivoLocal = $caminhoSub . "/" . $fileName;
												//
												//Salva o nome que sera no zip e remove o ../
												$arquivoZip = $caminhoSub . "/" . $fileName;
												$arquivoZip = str_replace("../", "", $arquivoZip);
												//
												//Inclui no zip
												$zip->addFile($arquivoLocal, $arquivoZip);
											}
										}
									}
								}
								if( $file->isFile()){
									//
									$fileName = $file->getFilename();
									//
									//Salva o nome para pegar o arquivo
									$arquivoLocal = $caminho . "/" . $fileName;
									//
									//ignora os constantes
									if(pathinfo($arquivoLocal, PATHINFO_EXTENSION) == 'vf') continue;
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
					//ignora os arquivos do git e o arquivo de log de erros e constantes
					if(strpos($fileName, "git")) continue;
					if(strpos($fileName, "README")) continue;
					if(strpos($fileName, "erro")) continue;
					if(strpos($fileName, "constantes")) continue;
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

		private function cadastraPrograma($file, $tipo_origem = '', $nome = '', $tipo = 'programa', $imagem = '', $tipo_menu = '', $pode_executar = 0){
			//
			if($nome == '') $nome = $file;
			//
			$this->db->setTabela("programas", "idprogramas");
			//
			$dados['prog_nome']        = $this->util->sgr($nome);
			$dados['prog_file']        = $this->util->sgr($file);
			$dados['prog_tipo']        = $this->util->sgr($tipo);
			$dados['prog_imagem']      = $this->util->sgr($imagem);
			$dados['prog_tipo_origem'] = $this->util->sgr($tipo_origem);
			$dados['prog_tipo_menu']   = $this->util->sgr($tipo_menu);
			//
			$this->db->gravarInserir($dados);
			//
			$idprogramas = $this->db->getUltimoID();
			//
			$sql = "INSERT INTO grupos_acessos_programas (gap_idgrupos_acessos, gap_idprogramas, gap_executa)
						SELECT idgrupos_acessos, {$idprogramas}, {$pode_executar} FROM grupos_acessos";
			$this->db->executaSQL($sql);
			//
			if($tipo == 'menu'){
				$this->html->criaMenu('', $tipo_menu);
			}
		}

	}

?>
