<?php

class produtos{
	private $db;
	private $util;

	function __construct($db, $util){
		$this->db = $db;
		$this->util = $util;
	}
	public function getItensFormulaEdita($idprodutos){
		$itensFormula = '<div id="formula" class="tab-pane fade" style="padding-top: 5px;">
				          <div class="row" id="formulaCabecalho">
				          <div class="col-xs-12 col-md-7 col-sm-7 col-lg-7" style="margin-bottom: 5px;" align="right">
				            <input type="text" class="form-control" id="produtos" name="produtos" placeholder="Materia Prima">
				            <input type="hidden" id="pfor_idprodutos" name="pfor_idprodutos">
				          </div>
				          <div class="col-xs-4 col-md-2 col-sm-2 col-lg-2" style="margin-bottom: 5px;" align="right;">
				            <input type="text" class="form-control" id="pfor_qte" name="pfor_qte" placeholder="Qte">
				            <span id="prod_uni"></span>
				          </div>
				          <div class="col-xs-4 col-md-2 col-sm-2 col-lg-2" style="margin-bottom: 5px;" align="right;">
				              <input type="text" class="form-control" id="pfor_porc_perca" name="pfor_porc_perca" placeholder="Perca">
				          </div>
				          <div class="col-xs-4 col-md-1 col-sm-1 col-lg-1" style="margin-bottom: 5px;" align="right">
				           <button class="btn btn-default" type="button" onclick="gravaItensFormula()"><img src="../icones/adiciona.png"></button>
				          </div>
				        </div>
				        <div class="row" id="formulaItens">
				          <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">';
		$sqlFormulas = "SELECT * 
            FROM produtos_formulas 
              LEFT JOIN produtos ON (pfor_idprodutos = idprodutos) 
            WHERE pfor_idproduto_final = {$idprodutos}";
	    $resFormulas = $this->db->consultar($sqlFormulas);
	    $itensFormula .= '<table class="table" id="tableItensFormula">';
		$itensFormula .= '<tr>';
		$itensFormula .= '<td width="10%">Cod.</td>';
		$itensFormula .= '<td>Nome</td>';
		$itensFormula .= '<td width="15%">Qte</td>';
		$itensFormula .= '<td width="15%">Perca</td>';
		$itensFormula .= '<td width="3%">&nbsp;</td>';
		$itensFormula .= '</tr>';  
	    foreach ($resFormulas as $regFormulas) {
	        $itensFormula .= "<tr id='itemFormula_" . $regFormulas["idprodutos_formulas"] . "'>";
		    $itensFormula .= "<td>{$regFormulas["idprodutos"]}</td>";
		    $itensFormula .= "<td>{$regFormulas["prod_nome"]}</td>";
		    $itensFormula .= "<td>{$regFormulas["pfor_qte"]}</td>";
		    $itensFormula .= "<td>{$regFormulas["pfor_porc_perca"]}%</td>";
		    $itensFormula .= "<td onclick='excluirItemFormula({$regFormulas["idprodutos_formulas"]})' style='cursor: pointer;'><img src='../icones/excluir.png'></td>";
		    $itensFormula .= "</tr>"; 
	    }
	    $itensFormula .= '</table>';
	    $itensFormula .= '</div>';
	    $itensFormula .= '</div>';
	    $itensFormula .= '</div>';
	    //
	   	return $itensFormula;
	}

	public function getItensFormula($idprodutos){
		$itensFormula = '<div class="row" id="formulaItens">
				          <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12">';
		$sqlFormulas = "SELECT * 
            FROM produtos_formulas 
              LEFT JOIN produtos ON (pfor_idprodutos = idprodutos) 
            WHERE pfor_idproduto_final = {$idprodutos}";
	    $resFormulas = $this->db->consultar($sqlFormulas);
	    $itensFormula .= '<table class="table" id="tableItensFormula">';
		$itensFormula .= '<tr>';
		$itensFormula .= '<td width="10%">Cod.</td>';
		$itensFormula .= '<td>Nome</td>';
		$itensFormula .= '<td width="15%">Qte</td>';
		$itensFormula .= '<td width="15%">Perca</td>';
		$itensFormula .= '<td width="3%">&nbsp;</td>';
		$itensFormula .= '</tr>';  
	    foreach ($resFormulas as $regFormulas) {
	        $itensFormula .= "<tr id='itemFormula_" . $regFormulas["idprodutos_formulas"] . "'>";
		    $itensFormula .= "<td>{$regFormulas["idprodutos"]}</td>";
		    $itensFormula .= "<td>{$regFormulas["prod_nome"]}</td>";
		    $itensFormula .= "<td>{$regFormulas["pfor_qte"]}</td>";
		    $itensFormula .= "<td>{$regFormulas["pfor_porc_perca"]}%</td>";
		    $itensFormula .= "<td onclick='excluirItemFormula({$regFormulas["idprodutos_formulas"]})' style='cursor: pointer;'><img src='../icones/excluir.png'></td>";
		    $itensFormula .= "</tr>"; 
	    }
	    $itensFormula .= '</table>';
	    $itensFormula .= '</div>';
	    $itensFormula .= '</div>';
	    $itensFormula .= '</div>';
	    //
	   	return $itensFormula;
	}
}
?>
