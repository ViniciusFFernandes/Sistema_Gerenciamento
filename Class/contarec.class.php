<?php
    require_once("util.class.php");
    require_once("parametros.class.php");
    require_once("util.class.php");
    require_once("html.class.php");
    require_once("cc.class.php");

    class Contarec{
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

        public function gerarHistorio($idconta, $operacao, $valor, $idoperador, $data = '', $idmeio_pagto = '', $idcc = ''){
            $this->db->setTabela("contarec_hist", "idcontarec_hist");
            //
            unset($dados);
            $dados['id']                    = 0;
            $dados['crhi_idcontarec'] 	    = $this->util->igr($idconta);
            $dados['crhi_operacao']         = $this->util->sgr($operacao);
            $dados['crhi_data_pagto'] 	    = $this->util->dgr($data);
            $dados['crhi_idmeio_pagto'] 	= $this->util->igr($idmeio_pagto);
            $dados['crhi_idcc']             = $this->util->igr($idcc);
            $dados['crhi_valor']            = $this->util->sgr($valor);
            $dados['crhi_idoperador'] 	    = $this->util->igr($idoperador);
            $dados['crhi_data']             = $this->util->dgr(date('d/m/Y H:i'));
            //
            $this->db->gravarInserir($dados, true);
        }

        public function baixaConta($idconta, $valor, $multa, $desconto, $idcc, $idmeio_pagto, $data, $idoperador){
            $sql = "SELECT * FROM contarec WHERE idcontarec = " . $idconta;
            $reg = $this->db->retornaUmReg($sql);
            //
            if($reg['ctrc_situacao'] != 'Pendente' && $reg['ctrc_situacao'] != 'QParcial'){
                $this->html->mostraErro("Está conta está " . $reg['ctrc_situacao'] . " e não pode ser paga!");
                $this->db->rollBack();
                exit;
            }
            if($reg['ctrc_vlr_devedor'] <= 0){
                $this->html->mostraErro("Está conta já esta totalmente paga!");
                $this->db->rollBack();
                exit;
            }
            //
            $novoValorDevedor = ($reg['ctrc_vlr_devedor'] + $multa - $desconto);
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
            $this->db->setTabela("contarec", "idcontarec");
            //
            unset($dados);
            $dados['id']                    = $idconta;
            $dados['ctrc_situacao']         = $this->util->sgr($novaSitucao);
            $dados['ctrc_vlr_pago']         = $this->util->vgr($valor);
            if($multa > 0){
                $novoJuros = $reg['ctrc_vlr_juros'] + $multa;
                $novoPorcJuros = ($novoJuros * 100) / $reg['ctrc_vlr_bruto'];
                //
                $dados['ctrc_vlr_juros']    = $this->util->vgr($novoJuros);
                $dados['ctrc_porc_juros']   = $this->util->vgr($novoPorcJuros);
            }
            if($desconto > 0){
                $novoDescontos = $reg['ctrc_vlr_desconto'] + $multa;
                $novoPorcDescontos = ($novoDescontos * 100) / $reg['ctrc_vlr_bruto'];
                //
                $dados['ctrc_vlr_desconto']    = $this->util->vgr($novoDescontos);
                $dados['ctrc_porc_desconto']   = $this->util->vgr($novoPorcDescontos);
            }
            //
            $this->db->gravarInserir($dados, true);
            //
            //
            $this->gerarHistorio($idconta, "Baixa", $valor, $idoperador, $data, $idmeio_pagto, $idcc);
            //
            if($this->parametros->buscaValor("sistema: gerar lancamentos bancarios pelas contas") == 'SIM'){
                $this->cc->gerarLancamento($idconta, "C", $valor, $idoperador, $data, $idmeio_pagto, $idcc, "contarec", "Recebimento da conta");
            }
            //
        }

        public function reabrirConta($idconta, $idoperador){
            $sql = "SELECT * FROM contarec WHERE idcontarec = " . $idconta;
            $reg = $this->db->retornaUmReg($sql);
            //
            if($reg['ctrc_situacao'] != 'Quitada' && $reg['ctrc_situacao'] != 'QParcial'){
                $this->html->mostraErro("Está conta está " . $reg['ctrc_situacao'] . " e não pode ser reaberta!");
                $this->db->rollBack();
                exit;
            }
            //
            $this->db->setTabela("contarec", "idcontarec");
            //
            unset($dados);
            $dados['id']                    = $idconta;
            $dados['ctrc_situacao']         = $this->util->sgr("Pendente");
            $dados['ctrc_vlr_pago']         = $this->util->vgr(0);
            //
            $this->db->gravarInserir($dados, true);
            //
            //
            $this->gerarHistorio($idconta, "Reabertura", $reg['ctrc_vlr_pago'], $idoperador, date('d/m/Y'), $reg['ctrc_idmeio_pagto'], $reg['ctrc_idcc']);
            //
            if($this->parametros->buscaValor("sistema: gerar lancamentos bancarios pelas contas") == 'SIM'){
                $this->cc->gerarLancamento($idconta, "D", $reg['ctrc_vlr_pago'], $idoperador, date("d/m/Y"), $reg['ctrc_idmeio_pagto'], $reg['ctrc_idcc'], "contarec", "Reabertura da conta");
            }
            //
        }
    }
?>
