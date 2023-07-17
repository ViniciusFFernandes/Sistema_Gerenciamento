<?php
    require_once("util.class.php");
    require_once("parametros.class.php");
    require_once("util.class.php");
    require_once("html.class.php");
    require_once("cc.class.php");

    class Pedcompras{
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

        public function retornaDadosDesconto($idpedcompras, $valorPedido, $valorDesconto, $porcDesconto){
            if($idpedcompras > 0){
                $totalPedcompras = $this->db->retornaUmCampoID('pcom_total_produtos + pcom_frete', 'pedcompras', $idpedcompras);
            }else{
                $totalPedcompras = $valorPedido;
            }
            //
            $dados = array();
            //
            if($valorDesconto > 0){
                $dados['valor'] = $valorDesconto;
                $dados['porcentagem'] = ($valorDesconto * 100) / $totalPedcompras;
            }elseif($porcDesconto > 0){
                $dados['porcentagem'] = $porcDesconto;
                $dados['valor'] = ($totalPedcompras * $porcDesconto) / 100;
            }else{
                $dados['valor'] = 0;
                $dados['porcentagem'] = 0; 
            }
            //
            return $dados;
        }

        public function retornaItensPedido($idpedcompras){
            //
            $sql = "SELECT * 
                    FROM pedcompras 
                        JOIN pedcompras_itens ON (pcit_idpedcompras = idpedcompras) 
                        LEFT JOIN produtos ON (idprodutos = pcit_idprodutos)
                    WHERE pcit_idpedcompras = " . $idpedcompras;
            $res = $this->db->consultar($sql);
            //
            if(empty($res)) return; //Retorna em branco caso nao tenha itens
            //
            $div = '<div class="row pb-3">';
                $div .= '<div class="col-md-12 col-sm-12 col-12">';
                //
                    $div .= '<div class="row linhaPedidoItens font-weight-bold">';
                        $div .= '<div class="col-md-3 col-sm-3 d-none d-sm-block">';
                            $div .= 'Produto';
                        $div .= '</div>';
                        $div .= '<div class="col-md-2 col-sm-2 d-none d-sm-block text-right">';
                            $div .= 'Qte';
                        $div .= '</div>';
                        $div .= '<div class="col-md-2 col-sm-2 d-none d-sm-block text-right">';
                            $div .= 'Unitário';
                        $div .= '</div>';
                        $div .= '<div class="col-md-2 col-sm-2 d-none d-sm-block text-right">';
                            $div .= 'Desconto';
                        $div .= '</div>';
                        $div .= '<div class="col-md-2 col-sm-2 d-none d-sm-block text-right">';
                            $div .= 'Total';
                        $div .= '</div>';
                        $div .= '<div class="col-md-1 col-sm-1 d-none d-sm-block text-right">';
                        $div .= '</div>';
                    $div .= '</div>';
                    //
                    foreach($res AS $reg){
                        $div .= '<div class="row border-prod">';
                            $div .= '<div class="col-md-3 col-sm-3 col-12 pt-2">';
                                $div .= $reg['idprodutos'] . ' - ' . $reg['prod_nome'];
                                $div .= '<i style="float: right;" class="pointer fas fa-trash-alt pl-1 d-block d-sm-none"  onclick="excluiItem(' . $reg['idpedcompras_itens'] . ')"></i>';
                                $div .= '<i style="float: right;" class="pointer far fa-edit pr-1 d-block d-sm-none" onclick="editarItem(' . $reg['idpedcompras_itens'] . ')"></i>';
                                $div .= '<div class="row">';
                                    $div .= '<div class="col-6 d-block d-sm-none dadosProdPed">';
                                        $div .= 'Qte: ' . $this->util->formataNumero($reg['pcit_qte']);
                                    $div .= '</div>';
                                    $div .= '<div class="col-6 d-block d-sm-none dadosProdPed">';
                                        $div .= 'Unitário: ' . $this->util->formataMoeda($reg['pcit_vlr_unitario']);
                                    $div .= '</div>';
                                    $div .= '<div class="col-6 d-block d-sm-none dadosProdPed">';
                                        $div .= 'Desconto: ' . $this->util->formataMoeda($reg['pcit_valor_desconto']);
                                    $div .= '</div>';
                                    $div .= '<div class="col-6 d-block d-sm-none dadosProdPed">';
                                        $div .= 'Total: ' . $this->util->formataMoeda($reg['pcit_total_item']);
                                    $div .= '</div>';
                                $div .= '</div>';
                            $div .= '</div>';
                            $div .= '<div class="col-md-2 col-sm-2 pt-2 d-none d-sm-block text-right">';
                                $div .= $this->util->formataNumero($reg['pcit_qte']);
                            $div .= '</div>';
                            $div .= '<div class="col-md-2 col-sm-2 pt-2 d-none d-sm-block text-right">';
                                $div .= $this->util->formataMoeda($reg['pcit_vlr_unitario']);
                            $div .= '</div>';
                            $div .= '<div class="col-md-2 col-sm-2 pt-2 d-none d-sm-block text-right">';
                                $div .= $this->util->formataMoeda($reg['pcit_valor_desconto']);
                            $div .= '</div>';
                            $div .= '<div class="col-md-2 col-sm-2 pt-2 d-none d-sm-block text-right">';
                                $div .= $this->util->formataMoeda($reg['pcit_total_item']);
                            $div .= '</div>';
                            $div .= '<div class="col-md-1 col-sm-1 pt-2 d-none d-sm-block text-right">';
                                if($reg["pcom_situacao"] == "Aberto"){
                                    $div .= '<i class="pointer far fa-edit mr-2" onclick="editarItem(' . $reg['idpedcompras_itens'] . ')"></i>';
                                    $div .= '<i class="pointer fas fa-trash-alt ml-2"  onclick="excluiItem(' . $reg['idpedcompras_itens'] . ')"></i>';
                                }
                            $div .= '</div>';
                        $div .= '</div>';
                    }
                //
                $div .= '</div>';
            $div .= '</div>';
            //
            return $div;
        }

        public function retornaContasPedido($idpedcompras){
            //
            $sql = "SELECT * 
                    FROM pedcompras
                        JOIN pedcompras_contas ON (pccon_idpedcompras = idpedcompras)
                        LEFT JOIN meio_pagto ON (idmeio_pagto = pccon_idmeio_pagto)
                        LEFT JOIN cc ON (idcc = pccon_idcc)
                        LEFT JOIN tipo_contas ON (idtipo_contas = pccon_idtipo_contas)
                    WHERE pccon_idpedcompras = " . $idpedcompras;
            $res = $this->db->consultar($sql);
            //
            if(empty($res)) return ""; //Retorna em branco caso nao tenha itens
            //
            $div = '<div class="row pb-3">';
                $div .= '<div class="col-md-12 col-sm-12 col-12">';
                //
                    $div .= '<div class="row linhaPedidoContas font-weight-bold">';
                        $div .= '<div class="col-md-1 col-sm-1 d-none d-sm-block">';
                            $div .= 'Dias';
                        $div .= '</div>';
                        $div .= '<div class="col-md-1 col-sm-1 d-none d-sm-block">';
                            $div .= 'Vencimento';
                        $div .= '</div>';
                        $div .= '<div class="col-md-2 col-sm-2 d-none d-sm-block">';
                            $div .= 'Conta Bancaria';
                        $div .= '</div>';
                        $div .= '<div class="col-md-2 col-sm-2 d-none d-sm-block">';
                            $div .= 'Meio de Pagamento';
                        $div .= '</div>';
                        $div .= '<div class="col-md-1 col-sm-1 d-none d-sm-block">';
                            $div .= 'Conta';
                        $div .= '</div>';
                        $div .= '<div class="col-md-2 col-sm-2 d-none d-sm-block">';
                            $div .= 'Tipo';
                        $div .= '</div>';
                        $div .= '<div class="col-md-2 col-sm-2 d-none d-sm-block text-right">';
                            $div .= 'Valor';
                        $div .= '</div>';
                        $div .= '<div class="col-md-1 col-sm-1 d-none d-sm-block text-right">';
                        $div .= '</div>';
                    $div .= '</div>';
                    //
                    foreach($res AS $reg){
                        $classePointer = "";
                        $functionAbreConta = "";
                        if($reg['pccon_idcontapag'] > 0){
                            $classePointer = 'pointer';
                            $functionAbreConta = 'onclick="abreConta(' . $reg['pccon_idcontapag'] . ')"';
                        }
                        $div .= '<div class="row border-prod">';
                            $div .= '<div class="col-md-1 col-sm-1 pt-2 d-none d-sm-block">';
                                $div .= $reg['pccon_vencimento_dias'];
                            $div .= '</div>';
                            $div .= '<div class="col-md-1 col-sm-1 col-12 pt-2">';
                                $div .=  '<span class="d-sm-none">' . $reg['pccon_vencimento_dias'] . ' - </span>' . $this->util->convertData($reg['pccon_vencimento']);
                                $div .= '<i style="float: right;" class="pointer fas fa-trash-alt pl-1 d-block d-sm-none"  onclick="excluiConta(' . $reg['idpedcompras_contas'] . ')"></i>';
                                $div .= '<i style="float: right;" class="pointer far fa-edit pr-1 d-block d-sm-none" onclick="editarConta(' . $reg['idpedcompras_contas'] . ')"></i>';
                                $div .= '<div class="row">';
                                    $div .= '<div class="col-6 d-block d-sm-none dadosContasPed">';
                                        $div .= 'Conta: ' . $reg['cc_nome'];
                                    $div .= '</div>';
                                    $div .= '<div class="col-6 d-block d-sm-none dadosContasPed">';
                                        $div .= 'Pagamento: ' . $reg['mpag_nome'];
                                    $div .= '</div>';
                                    $div .= '<div class="col-6 d-block d-sm-none dadosContasPed ' . $classePointer . '" ' . $functionAbreConta . '>';
                                        $div .= 'Código: ' . $reg['pccon_idcontapag'];
                                    $div .= '</div>';
                                    $div .= '<div class="col-6 d-block d-sm-none dadosContasPed">';
                                        $div .= 'Valor: ' . $this->util->formataMoeda($reg['pccon_valor']);
                                    $div .= '</div>';
                                $div .= '</div>';
                            $div .= '</div>';
                            $div .= '<div class="col-md-2 col-sm-2 pt-2 d-none d-sm-block">';
                                $div .= $reg['cc_nome'];
                            $div .= '</div>';
                            $div .= '<div class="col-md-2 col-sm-2 pt-2 d-none d-sm-block">';
                                $div .= $reg['mpag_nome'];
                            $div .= '</div>';
                            $div .= '<div class="col-md-1 col-sm-1 pt-2 d-none d-sm-block ' . $classePointer . '" ' . $functionAbreConta . '>';
                                $div .= $reg['pccon_idcontapag'];
                            $div .= '</div>';
                            $div .= '<div class="col-md-2 col-sm-2 pt-2 d-none d-sm-block">';
                                $div .= $reg['tico_nome'];
                            $div .= '</div>';
                            $div .= '<div class="col-md-2 col-sm-2 pt-2 d-none d-sm-block text-right">';
                                $div .= $this->util->formataMoeda($reg['pccon_valor']);
                            $div .= '</div>';
                            $div .= '<div class="col-md-1 col-sm-1 pt-2 d-none d-sm-block text-right">';
                                if($reg["pcom_situacao"] == "Aberto"){
                                    $div .= '<i class="pointer far fa-edit mr-2" onclick="editarConta(' . $reg['idpedcompras_contas'] . ')"></i>';
                                    $div .= '<i class="pointer fas fa-trash-alt ml-2"  onclick="excluiConta(' . $reg['idpedcompras_contas'] . ')"></i>';
                                }
                            $div .= '</div>';
                        $div .= '</div>';
                    }
                //
                $div .= '</div>';
            $div .= '</div>';
            //
            return $div;
        }

    }
?>
