<?php

class Tabelas{

	public function geraTabelaTel($res){
		$linhaColorida = false;
    	echo "<table class='table' width='100%' style='margin-top: 3px;'>";
	    foreach ($res as $reg) {
	     echo '<tr ';
			 if ($linhaColorida) echo "class='info'";
			 echo '>';
	     echo '<td width="80px">(' . $reg['pnum_DDD'] . ')</td>
	        <td>' . $reg['pnum_numero'] . '</td>
	        <td width="20px;" align="left"><img src="../icones/excluir.png" onclick="excluirTelefone(' . $reg['idpessoas_numeros'] . ')" style="cursor:pointer;"></td>
	      </tr>';
	 if ($linhaColorida) {
		    $linhaColorida = false;
		  }else {
		    $linhaColorida = true;
		  }
	  	}
	  echo "</table>";
	}

	public function geraTabelaPes($res, $db){
		 $linhaColorida = false;
	    echo "<div style='max-height: 350px; overflow: auto;'><table class='table' style='margin-top: 3px'>";
	    //echo $res;
	    foreach ($res as $reg) {
	      echo '<tr';
				if ($linhaColorida) {echo " class='info'";}
				echo ' onclick="abrePessoa(' . $reg['idpessoas'] . ')" style="cursor:pointer" id="linhasPessoas">
	        <td width="6%">' . $reg['idpessoas'] . '</td>
	        <td width="25%">' . $reg['pess_nome'] . '</td>
	        <td width="25%">' . $db->retornaUmTel($reg['idpessoas']) . '</td>
	        <td>' . $reg['pess_endereco'] . '</td>
	        <td>' . $reg['cid_nome'] . " - " . $reg['est_uf'] . '</td>
	      </tr>';
			if ($linhaColorida) {
		    $linhaColorida = false;
		  }else {
		    $linhaColorida = true;
		  }
	  }
	  echo "</table></div>";
	}

	public function geraTabelaCid($res, $db){
		 $linhaColorida = false;
	    echo "<div style='max-height: 250px; overflow: auto;'><table class='table' style='margin-top: 3px'>";
	    //echo $res;
	    foreach ($res as $reg) {
	      echo '<tr';
				if ($linhaColorida) {echo " class='info'";}
				echo ' onclick="abreCidades(' . $reg['idcidades'] . ')" style="cursor:pointer" id="linhasCidades">
	        <td width="6%">' . $reg['idcidades'] . '</td>
	        <td>' . $reg['cid_nome'] . '</td>
	        <td>' . $reg['est_uf'] . '</td>
	      </tr>';
			if ($linhaColorida) {
		    $linhaColorida = false;
		  }else {
		    $linhaColorida = true;
		  }
	  }
	  echo "</table></div>";
	}
}
?>
