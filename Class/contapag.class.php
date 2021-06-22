<?php
    require_once("util.class.php");
    require_once("parametros.class.php");
    require_once("util.class.php");
    require_once("html.class.php");
    require_once("cc.class.php");

    class Contapag{
        private $db;
		private $util;
		private $parametros;
		private $html;
		private $cc;

		function __construct($db){
			$this->db = $db;
			$this->util = new Util();
			$this->parametros = new Parametros($db);
			$this->html = new html($db);
			$this->cc = new cc($db);
		}

        public function attContaSalario($idcontapag, $idcliente, $idmeiopagto, $idcc, $mes = '', $ano = ''){
            if(empty($mes) || empty($ano)){
                $sql = "SELECT MONTH(ctpg_vencimento) AS mes, 
                            YEAR(ctpg_vencimento) AS ano 
                        FROM contapag
                        WHERE idcontapag = " . $idcontapag;
                $datasConta = $this->db->retornaUmReg($sql);
                //
                $mes = $datasConta['mes'];
                $ano = $datasConta['ano'];
                //
            }
            $multa = 0;
            $desconto = 0;
            $sql = "SELECT * 
                    FROM contapag 
                        JOIN tipo_contas ON (ctpg_idtipo_contas = idtipo_contas)
                    WHERE IFNULL(ctpg_processou, '') <> 'SIM'
                    AND (IFNULL(tico_tipo_extra, '') = 'SIM'
                    OR IFNULL(tico_tipo_vale, '') = 'SIM')
                    AND MONTH(ctpg_vencimento) = {$mes}
                    AND YEAR(ctpg_vencimento) = {$ano}
                    AND ctpg_idcliente = " . $idcliente;
            $res = $this->db->consultar($sql);
            foreach($res as $reg){
                //
                if($reg['ctpg_situacao'] == 'Pendente' || $reg['ctpg_situacao'] == 'QParcial'){
                if($reg['tico_tipo_extra'] == 'SIM'){
                    $multa += $reg['ctpg_vlr_liquido'];
                }
                $desconto += $reg['ctpg_vlr_pago'];
                $ctpg_situacao = 'QSistema';
                $this->gerarHistorio($reg['idcontapag'], "BaixaSistema", $reg['ctpg_vlr_devedor'], $_SESSION['idusuario'], date('d/m/Y'), $idmeiopagto, $idcc);
                }elseif($reg['ctpg_situacao'] == 'Quitada'){
                if($reg['tico_tipo_extra'] == 'SIM'){
                    $multa += $reg['ctpg_vlr_liquido'];
                }
                $desconto += $reg['ctpg_vlr_pago'];
                $ctpg_situacao = $reg['ctpg_situacao'];
                }else{
                continue;
                }
                //
                $this->db->setTabela("contapag", "idcontapag");
                //
                unset($dados);
                $dados['id']                        = $reg['idcontapag'];
                $dados['ctpg_processou']  	        = $this->util->sgr("SIM");
                $dados['ctpg_situacao']  	        = $this->util->sgr($ctpg_situacao);
                $dados['ctpg_vlr_pago']  	        = "ctpg_vlr_pago + " . $reg['ctpg_vlr_devedor'];
                $dados['ctpg_idconta_salario']  	= $idcontapag;
                //
                $this->db->gravarInserir($dados, false);
            }
            //
            $this->db->setTabela("contapag", "idcontapag");
            //
            unset($dados);
            $dados['id']                = $idcontapag;
            $dados['ctpg_recalculou']  	= $this->util->sgr("SIM");
            $dados['ctpg_vlr_juros']  	= "ctpg_vlr_juros + " . $multa;
            $dados['ctpg_vlr_desconto']  	= "ctpg_vlr_desconto + " . $desconto;
            //
            $this->db->gravarInserir($dados, false);
        }

        public function gerarHistorio($idconta, $operacao, $valor, $idoperador, $data = '', $idmeio_pagto = '', $idcc = ''){
            $this->db->setTabela("contapag_hist", "idcontapag_hist");
            //
            unset($dados);
            $dados['id']                    = 0;
            $dados['cphi_idcontapag'] 	    = $this->util->igr($idconta);
            $dados['cphi_operacao']         = $this->util->sgr($operacao);
            $dados['cphi_data_pagto'] 	    = $this->util->dgr($data);
            $dados['cphi_idmeio_pagto'] 	= $this->util->igr($idmeio_pagto);
            $dados['cphi_idcc']             = $this->util->igr($idcc);
            $dados['cphi_valor']            = $this->util->vgr($valor);
            $dados['cphi_idoperador'] 	    = $this->util->igr($idoperador);
            $dados['cphi_data']             = $this->util->dgr(date('d/m/Y H:i'));
            //
            $this->db->gravarInserir($dados);
        }

        public function baixaConta($idconta, $valor, $multa, $desconto, $idcc, $idmeio_pagto, $data, $idoperador){
            //
            $valor = $this->util->vgr($valor);
            //
            $sql = "SELECT * FROM contapag WHERE idcontapag = " . $idconta;
            $reg = $this->db->retornaUmReg($sql);
            //
            if($reg['ctpg_situacao'] != 'Pendente' && $reg['ctpg_situacao'] != 'QParcial'){
                $this->html->mostraErro("Está conta está " . $reg['ctpg_situacao'] . " e não pode ser paga!");
                $this->db->rollBack();
                exit;
            }
            if($reg['ctpg_vlr_devedor'] <= 0){
                $this->html->mostraErro("Está conta já esta totalmente paga!");
                $this->db->rollBack();
                exit;
            }
            //
            $novoValorDevedor = ($reg['ctpg_vlr_devedor'] + $multa - $desconto);
            //
            if($novoValorDevedor < $valor){
                $this->html->mostraErro("Você está tentando pagar mais do que deve!");
                $this->db->rollBack();
                exit;
            }
            //
            if($novoValorDevedor == $valor){
                $novaSitucao = 'Quitada';
            }elseif($novoValorDevedor > $valor){
                $novaSitucao = 'QParcial';
            }
            //
            $this->db->setTabela("contapag", "idcontapag");
            //
            unset($dados);
            $dados['id']                    = $idconta;
            $dados['ctpg_situacao']         = $this->util->sgr($novaSitucao);
            $dados['ctpg_vlr_pago']         = $this->util->vgr($valor);
            if($multa > 0){
                $novoJuros = $reg['ctpg_vlr_juros'] + $multa;
                $novoPorcJuros = ($novoJuros * 100) / $reg['ctpg_vlr_bruto'];
                //
                $dados['ctpg_vlr_juros']    = $this->util->vgr($novoJuros);
                $dados['ctpg_porc_juros']   = $this->util->vgr($novoPorcJuros);
            }
            if($desconto > 0){
                $novoDescontos = $reg['ctpg_vlr_desconto'] + $desconto;
                $novoPorcDescontos = ($novoDescontos * 100) / $reg['ctpg_vlr_bruto'];
                //
                $dados['ctpg_vlr_desconto']    = $this->util->vgr($novoDescontos);
                $dados['ctpg_porc_desconto']   = $this->util->vgr($novoPorcDescontos);
            }
            //
            $this->db->gravarInserir($dados, true, "Pagamento");
            //
            //
            $this->gerarHistorio($idconta, "Baixa", $valor, $idoperador, $data, $idmeio_pagto, $idcc);
            //
            if($this->parametros->buscaValor("sistema: gerar lancamentos bancarios pelas contas") == 'SIM'){
                $this->cc->gerarLancamento($idconta, "D", $valor, $idoperador, $data, $idmeio_pagto, $idcc, "contapag", "Recebimento da conta");
            }
            //
        }

        public function reabrirConta($idconta, $idoperador){
            $sql = "SELECT * FROM contapag WHERE idcontapag = " . $idconta;
            $reg = $this->db->retornaUmReg($sql);
            //
            if($reg['ctpg_situacao'] != 'Quitada' && $reg['ctpg_situacao'] != 'QParcial'){
                $this->html->mostraErro("Está conta está " . $reg['ctpg_situacao'] . " e não pode ser reaberta!");
                $this->db->rollBack();
                exit;
            }
            //
            $this->db->setTabela("contapag", "idcontapag");
            //
            unset($dados);
            $dados['id']                    = $idconta;
            $dados['ctpg_situacao']         = $this->util->sgr("Pendente");
            $dados['ctpg_vlr_pago']         = $this->util->vgr(0);
            //
            $this->db->gravarInserir($dados, true, "Reabertura");
            //
            //
            $this->gerarHistorio($idconta, "Reabertura", $reg['ctpg_vlr_pago'], $idoperador, date('d/m/Y'), $reg['ctpg_idmeio_pagto'], $reg['ctpg_idcc']);
            //
            if($this->parametros->buscaValor("sistema: gerar lancamentos bancarios pelas contas") == 'SIM'){
                $this->cc->gerarLancamento($idconta, "C", $reg['ctpg_vlr_pago'], $idoperador, date("d/m/Y"), $reg['ctpg_idmeio_pagto'], $reg['ctpg_idcc'], "contapag", "Reabertura da conta");
            }
            //
        }

        public function geraHistorico($idconta){
			$sql = "SELECT * FROM contapag_hist WHERE cphi_idcontapag = " . $idconta;
			$res = $this->db->consultar($sql);
			foreach ($res as $reg) {
				$hist .= '<div class="row">';
					$hist .= '<div class="col-sm-12 col-12 pb-3">';
                        $hist .= '<div class="card shadow mb-4 border-left-primary">';
                            $hist .= '<div class="card-header py-3"><b>Operação: </b>' . $reg['cphi_operacao'] . '<span style="float: right;">' . $this->util->convertData($reg['cphi_data']) . '</span></div>';
                            $hist .= '<div class="card-body">';
                                $hist .= '<div class="row">';
                                    $hist .= '<div class="col-sm-2 col-md-2 col-6 pb-3">Valor: ' . $this->util->formataMoeda($reg['cphi_valor']) . '</div>';
                                    $hist .= '<div class="col-sm-3 col-md-3 col-6 pb-3">Operador: ' . $this->db->retornaUmCampoID("pess_nome", "pessoas", $reg['cphi_idoperador']) . '</div>';
                                    $hist .= '<div class="col-sm-3 col-md-3 col-6 pb-3">Conta Bancária: ' . $this->db->retornaUmCampoID("cc_nome", "cc", $reg['cphi_idcc']) . '</div>';
                                    $hist .= '<div class="col-sm-3 col-md-3 col-6 pb-3">Meio de pagamento: ' . $this->db->retornaUmCampoID("mpag_nome", "meio_pagto", $reg['cphi_idmeio_pagto']) . '</div>';
                                    if(strtotime($reg['cphi_data_pagto']) > 0){
                                        $hist .= '<div class="col-sm-4 col-md-4 col-6 pb-3">Pagamento: ' . $this->util->convertData($reg['cphi_data_pagto']) . '</div>';
                                    }
                                $hist .= '</div>';
                            $hist .= '</div>';
                        $hist .= '</div>';
					$hist .= '</div>';
				$hist .= '</div>';
			}
			//
			if(empty($hist)) $hist = "Nenhum histórico encontrado!<br>";
			//
			return $hist;
		}
    }
?>
