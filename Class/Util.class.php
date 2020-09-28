<?php

class Util{

	public function sgr($string, $retornaNull = false){
		if ($string != "") {
			$string = str_replace("'", "''", $string);
          	return "'" . $string . "'";
        }else{
			if($retornaNull){
				return "NULL";
			}
          	return "''";
        }
	}
	
	public function pgr($string){
		if ($string != "") {
          	return "(" . $string . ")";
        }else{
          	return "NULL";
        }
	}

	public function igr($num){
		if ($num != "") {
         	return intval($num);
        }else{
          	return "0";
        }
	}

	public function vgr($num){
		if ($num != "") {
         	return floatval($num);
        }else{
          	return "0";
        }
	}

	public function dgr($string, $incluiHora = ""){
		if ($string != "") {
					$string = explode(" ", $string);
					$data = explode("/", $string[0]);
					$dataFormatada = $data[2] . "-" . $data[1] . "-" . $data[0];
					if (!empty($incluiHora)) {
							$dataFormatada .= " " . $incluiHora;
					}elseif (!empty($string[1])) {
							$dataFormatada .= " " . $string[1];				
					}else{
						$dataFormatada .= " 00:00:00";		
					}

				return "'" . $dataFormatada . "'";
		}else{
			return "0";
		}
	}

	public function convertData($string){
		if ($string != "") {
					$string = explode(" ", $string);
					$data = explode("-", $string[0]);
					$dataFormatada = $data[2] . "/" . $data[1] . "/" . $data[0];
					if (!empty($string[1])) {
						$dataFormatada .= " " . $string[1];
					}
				}else {
					return "";
				}
				return $dataFormatada;
	}

	public function formataMoeda($valor, $casas = 2) {
		return number_format($valor, $casas, ',', '.');
	}
	
	public function formataNumero($valor, $casas = 2) {
		return (number_format($valor, $casas, ',', ''));
	}
}
?>
