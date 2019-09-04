<?php

class produtos{
	private $db;
	private $util;

	function __construct($db, $util){
		$this->db = $db;
		$this->util = $util;
	}
	public function getItensFormula($idprodutos){
		$itensFormula = '<div class="row" style="margin-bottom: 5px;">
							<div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">
								<b>Formula para Produção</b>
								<span id="btnExibeFormula" onclick="mostraFormula()" style="cursor: pointer"><img src="../icones/visivel.png"></span>
							</div>
						</div>
				          <div class="row" id="formulaCabecalho" style="margin-bottom: 5px;display:none;">
				          <div class="col-xs-12 col-md-7 col-sm-7 col-lg-7">
				            <input type="text" class="form-control" id="produtos" name="produtos" placeholder="Materia Prima">
				            <input type="hidden" id="pfor_idprodutos" name="pfor_idprodutos">
				          </div>
				          <div class="col-xs-4 col-md-2 col-sm-2 col-lg-2">
				            <input type="text" class="form-control" id="pfor_qte" name="pfor_qte" placeholder="Qte">
				            <span id="prod_uni"></span>
				          </div>
				          <div class="col-xs-4 col-md-2 col-sm-2 col-lg-2">
				              <input type="text" class="form-control" id="pfor_porc_perca" name="pfor_porc_perca" placeholder="Perca">
				          </div>
				          <div class="col-xs-4 col-md-1 col-sm-1 col-lg-1">
				           <button class="btn btn-default" type="button"><img src="../icones/adiciona.png"></button>
				          </div>
				        </div>
				        <div class="row" id="formulaItens" style="display:none;">
				          <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">';
		$sqlFormulas = "SELECT * 
            FROM produtos_formulas 
              LEFT JOIN produtos ON (pfor_idprodutos = idprodutos) 
            WHERE pfor_idproduto_final = {$idprodutos}";
	    $resFormulas = $this->db->consultar($sqlFormulas);
	    $itensFormula .= '<table class="table">';
		$itensFormula .= '<tr>';
		$itensFormula .= '<td width="10%">Cod.</td>';
		$itensFormula .= '<td>Nome</td>';
		$itensFormula .= '<td width="15%">Qte</td>';
		$itensFormula .= '<td width="15%">Perca</td>';
		$itensFormula .= '</tr>';  
	    foreach ($resFormulas as $regFormulas) {
	        $itensFormula .= '<tr>';
		    $itensFormula .= '<td>{$regFormulas["idprodutos"]}</td>';
		    $itensFormula .= '<td>{$regFormulas["prod_nome"]}</td>';
		    $itensFormula .= '<td>{$regFormulas["pfor_qte"]}</td>';
		    $itensFormula .= '<td>{$regFormulas["pfor_porc_perca"]}%</td>';
		    $itensFormula .= '</tr>'; 
	    }
	    $itensFormula .= '</table>';
	    $itensFormula .= '</div>';
	    $itensFormula .= '</div>';
	    //
	   	return $itensFormula;
	}
}
?>
