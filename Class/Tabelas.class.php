<?php

class Tabelas{

	public function geraTabelaTel($res){
		$linhaColorida = false;
    	$tabela = "<table class='table' width='100%' style='margin-top: 3px;'>";
	    foreach ($res as $reg) {
	     $tabela .= '<tr ';
			 if ($linhaColorida) $tabela .= "class='info'";
			 $tabela .= '>';
	     $tabela .= '<td width="80px">(' . $reg['pnum_DDD'] . ')</td>
	        <td>' . $reg['pnum_numero'] . '</td>
	        <td width="20px;" align="left"><img src="../icones/excluir.png" onclick="excluirTelefone(' . $reg['idpessoas_numeros'] . ')" style="cursor:pointer;"></td>
	      </tr>';
	 if ($linhaColorida) {
		    $linhaColorida = false;
		  }else {
		    $linhaColorida = true;
		  }
	  	}
	  $tabela .= "</table>";
	  //
	  //escreve a tabela
	  echo $tabela;
	}

	public function geraTabelaPes($res, $db){
		 $linhaColorida = false;
	    $tabela = "<div style='max-height: 350px; overflow: auto;'><table class='table' style='margin-top: 3px'>";
	    foreach ($res as $reg) {
	    	//
	    	//Monta o nome da cidade com a sigla do estado
	    	$cidadeEstado = '';
	    	if(!empty($reg['idcidades'])){
	    		$cidadeEstado = $reg['cid_nome'] . " - " . $reg['est_uf'];
	    	}
	    	//
	      $tabela .= '<tr';
				if ($linhaColorida) {$tabela .= " class='info'";}
				$tabela .= ' onclick="abrePessoa(' . $reg['idpessoas'] . ')" style="cursor:pointer" id="linhasBusca">
	        <td width="6%">' . $reg['idpessoas'] . '</td>
	        <td width="25%">' . $reg['pess_nome'] . '</td>
	        <td width="25%">' . $db->retornaUmTel($reg['idpessoas']) . '</td>
	        <td>' . $reg['pess_endereco'] . '</td>
	        <td>' . $cidadeEstado . '</td>
	      </tr>';
			if ($linhaColorida) {
		    $linhaColorida = false;
		  }else {
		    $linhaColorida = true;
		  }
	  }
	  $tabela .= "</table></div>";
	  //
	  //escreve a tabela
	  echo $tabela;
	}
	
	public function geraTabelaBusca($res, $db, $colunas, $functionAbreReg){
		$linhaColorida = false;
	    $tabela = "<div style='max-height: 250px; overflow: auto;'>";
	    $tabela .= "<table class='table' style='margin-top: 3px'>";
		foreach ($res as $reg) {
			//
			//Define se a linha vai ser de outra cor
			$class = '';
			if ($linhaColorida) {
				$class = "class='info'";
				$linhaColorida = false;
			}else{
				$linhaColorida = true;
			}
			//
			$primeiraLinha = true;
			foreach ($colunas as $coluna => $tamanho) {
				if($primeiraLinha){
					$tabela .= "<tr {$class} onclick='{$functionAbreReg}({$reg[$coluna]})' style='cursor:pointer' id='linhasBusca'>";
					$primeiraLinha = false;
				}
				$tabela .= "<td {$tamanho}>{$reg[$coluna]}</td>";
			}
			$tabela .- "</tr>";
		}
		$tabela .= "</table>";
		$tabela .= "</div>";
		//
	  	//escreve a tabela
		echo $tabela;
	}
}
?>
