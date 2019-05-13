<?php
	class Relatorios{
		public function geraRelatorioFolhaPonto($res, $db, $util){
			$idpessoa = 99999999;
			$primeiro = true;
			echo "<table width='100%' class='table'>";
			foreach ($res as $reg) {
				if ($reg['fopo_idpessoas'] != $idpessoa) {
					$idpessoa = $reg['fopo_idpessoas'];
					$db->setTabela("pessoas");
					$regNome = $db->buscarUsuario('', $reg['fopo_idpessoas']);
					if (!empty($regNome)) {
						if (!$primeiro) {
							echo "<tr colspan='3'><td>&nbsp;</td></tr>";
						}
						echo "<tr class='info'>
								<td colspan='3'> " . $regNome['pess_nome'] . " </td>
							  </tr>";
					}
				}
				if (!empty($regNome)) {
					echo "<tr class='active'>
							<td>&nbsp;</td>
							<td width='40%'>" . $util->convertData($reg['fopo_horario']) . "</td>
							<td width='40%'>" . $reg['fopo_entrada_saida'] . "</td>
						 </tr>	";
					$primeiro = false;
				}
			}
			echo "</table>";
		}

		public function geraRelatorioSessoes($db, $util){
			//$primeiro = true;
			echo "<table width='100%' class='table'>";
			    echo "<caption>Sessões Ativas<caption>";
    			echo "<tr>";
        			echo "<td colspan='2' width='50%'>Nome</td>";
        			echo "<td width='30%'>Ultima Atividade</td>";
        			echo "<td>&nbsp;</td>";
    			echo "</tr>";
    			//
    			//Pessoas ativas
    			$db->setTabela("logs_acessos JOIN pessoas ON (idpessoas = log_idusuario)");
    			//
    			$where = " log_ultimo_acesso >= " . (time() - 30);
    			$where .= " ORDER BY idpessoas";
    			$res = $db->consultar($where);
    			//
    			$this->geraRelatorioSessoesAtivas($res, $db, $util);
			echo "</table>";
			//
			echo "<table width='100%' class='table'>";
			    echo "<caption>Sessões Inativas<caption>";
    			echo "<tr>";
        			echo "<td colspan='2' width='50%'>Nome</td>";
        			echo "<td width='30%'>Ultima Atividade</td>";
        			echo "<td>&nbsp;</td>";
    			echo "</tr>";
			    //
			    //Pessoas não ativas
			    $db->setTabela("logs_acessos JOIN pessoas ON (idpessoas = log_idusuario)");
			    //
			    $where = " log_ultimo_acesso < " . (time() - 30);
    			$where .= " ORDER BY idpessoas";
    			$res = $db->consultar($where);
    			//
    			$this->geraRelatorioSessoesDesativadas($res, $db, $util);
    		echo "</table>";
		}

		public function geraRelatorioSessoesAtivas($res, $db, $util){
		        $idpessoas = "###";
		        $primeiro = true;
				foreach ($res as $reg) {
				    if($reg['idpessoas'] != $idpessoas && !$primeiro){	        
						    echo "</table>";
						echo "</td>"; 
					echo "</tr>";
					}
				    if($reg['idpessoas'] != $idpessoas){
				       $idpessoas = $reg['idpessoas'];
				       echo "<tr>";
						echo "<td colspan='2'>" . $reg['pess_nome'] . "<img src='../icones/mais.png' width='20px' height='20px'onclick=\"mostraSessoes('" . $reg['idpessoas'] . "', 'A')\" style='cursor: pointer;' id='mostra_" . $reg['idpessoas'] . "_A'><img src='../icones/menos.png' width='20px' height='20px'onclick=\"escondeSessoes('" . $reg['idpessoas'] . "', 'A')\" style='cursor: pointer;display:none;' id='esconde_" . $reg['idpessoas'] . "_A'></td>";
						echo "<td>" . date("d/m/Y H:i", $reg['log_ultimo_acesso']) . "</td>";
						echo "<td><img src='../icones/bola_verde.png' width='20px' height='20px'></td>";
					echo "</tr>";
					//
					//cria a tabela para detalhes
					echo "<tr id='detalhes_" . $reg['idpessoas'] . "_A' style='display: none;'>";
					    echo "<td width='10%'> </td>";
						echo "<td colspan='3'>";
					        echo "<table width='100%' class='table'>";
				    }
					            echo "<tr>";
						            echo "<td>" . $reg['log_ultimo_ip'] . "</td>";
						            echo "<td colspan='2'>" . $reg['log_idsessao'] . "</td>";
						        echo "</tr>";  
				$primeiro = false;	
				}
				//
				//Fecha as tags do ultimo registro
				        echo "</table>";
					echo "</td>"; 
				echo "</tr>";
		}

		public function geraRelatorioSessoesDesativadas($res, $db, $util){
		  $idpessoas = "###";
		  $primeiro = true;
				foreach ($res as $reg) {
				    if($reg['idpessoas'] != $idpessoas && !$primeiro){	        
						    echo "</table>";
						echo "</td>"; 
					echo "</tr>";
					}
				    if($reg['idpessoas'] != $idpessoas){
				       $idpessoas = $reg['idpessoas'];
				       echo "<tr>";
						echo "<td colspan='2'>" . $reg['pess_nome'] . "<img src='../icones/mais.png' width='20px' height='20px'onclick=\"mostraSessoes('" . $reg['idpessoas'] . "', 'I')\" style='cursor: pointer;' id='mostra_" . $reg['idpessoas'] . "_I'><img src='../icones/menos.png' width='20px' height='20px'onclick=\"escondeSessoes('" . $reg['idpessoas'] . "', 'I')\" style='cursor: pointer;display:none;' id='esconde_" . $reg['idpessoas'] . "_I'></td>";
						echo "<td>" . date("d/m/Y H:i", $reg['log_ultimo_acesso']) . "</td>";
						echo "<td><img src='../icones/bola_vermelha.png' width='20px' height='20px'></td>";
					echo "</tr>";
				    echo "<tr id='detalhes_" . $reg['idpessoas'] . "_I' style='display: none;'>";
					    echo "<td width='10%'> </td>";
					    echo "<td colspan='3'>";
					        echo "<table width='100%' class='table'>";
				    }
					            echo "<tr>";
						            echo "<td>" . $reg['log_ultimo_ip'] . "</td>";
						            echo "<td colspan='2'>" . $reg['log_idsessao'] . "</td>";
						        echo "</tr>";  
				$primeiro = false;		        
				}
				//
				//Fecha as tags do ultimo registro
				        echo "</table>";
					echo "</td>"; 
				echo "</tr>";
		}
		
		public function ultilizaAPIRenato($linkAPI, $parametros = '', $util){
			$dados = $this->retornaDadoAPI($linkAPI, $parametros);
			//echo $dados;
			//exit;
			$dados = json_decode($dados);
			//print_r($dados);
			//exit;
			if(empty($dados)){
				echo '<div class="row">';
					echo '<div class="col-md-12 col-sm-12 col-xs-12 table-responsive" id="relatorio">';
						echo '<span style="font-size: 18px; font-family: arial;">Nenhum usuario online no site do Renato</span></center>';
					echo '</div>';
				echo '</div>';
			}
			echo "<table width='100%' class='table'>";
				echo "<caption>Sessões Ativas (API Renato)<caption>";
				echo "<tr>";
					echo "<td width='40%'>Nome</td>";
					echo "<td width='30%'>Id da Sessão</td>";
					echo "<td width='30%'>Ultima Atividade</td>";
				echo "</tr>";
				foreach($dados as $reg){
				    $reg = json_decode(json_encode($reg), True);
					echo "<tr>";
						echo "<td>" . $reg['nome'] . "</td>";
						echo "<td>" . $reg['idsession'] . "</td>";
						echo "<td>" . $util->convertData($reg['data']) . "</td>";
					echo "</tr>";
				}
			echo "</table>";
		}
		
		private function retornaDadoAPI($linkAPI, $parametros = ''){
			$cURL = curl_init($linkAPI);
			// Define a opção que diz que você quer receber o resultado encontrado
			curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
			// Executa a consulta, conectando-se ao site e salvando o resultado na variável $resultado
			$resultado = curl_exec($cURL);
			// Encerra a conexão com o site
			curl_close($cURL);
			// Retorna os dados
			return $resultado;
		}
	}
 ?>
