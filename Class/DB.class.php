<?php
	class Db {
		private $host;
		private $porta;
		private $usudb;
		private $nomedb;
		private $senhadb;
		private $conexao;
		private $tabela;
		private $idtabela;
		private $msgErro;
		private $erro;
		private $transactionAtivo;


/* não é seguro fazer desse jeito, sempre mudar  a senha e o usuario nos seus projetos empresariais */

		function __construct($host, $porta, $usudb, $senhadb, $nomedb){
			$this->host = $host;
			$this->porta = $porta;
			$this->nomedb = $nomedb;
			$this->usudb = $usudb;
			$this->senhadb = $senhadb;
		}

		public function conectar(){
			try{	
				if(!isset($this->conexao)){
					$dados = "mysql:host=" . $this->host;
					$dados = $dados . ";port=" . $this->porta;
					$dados = $dados . ";dbname=" . $this->nomedb;
					$this->conexao = new PDO($dados, $this->usudb, $this->senhadb, array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));   // pdo php data objectve, classe generica pra banco de dados
					$this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$this->conexao->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				}
			}catch(PDOException $e){
				if(preg_match('/Unknown database/', $e->getMessage())){
					//Cria o banco e suas tabelas
					$this->criarBase();
				}else{
					echo 'ERROR: ' . $e->getMessage();
				}
			}
		}

		public function beginTransaction(){
			$this->conexao->beginTransaction();
		}

		public function commit(){
			$this->conexao->commit();
		}

		public function rollBack(){
			$this->conexao->rollBack();
		}

		public function erro(){
			return $this->erro;
		}

		public function getErro(){
			return $this->msgErro;
		}

		public function setTabela($tabela, $idtabela){
			$this->tabela = $tabela;
			$this->idtabela = $idtabela;
		}

		//função antiga para fazer login
		public function buscarUsuario($login){
			if (empty($login) && empty($id)) {
				return;
			}
			$sql = "SELECT * FROM " . $this->tabela . " WHERE ";
			//define se vai buscar pelo login, pelo id ou pelos dois
			if (!empty($login)) {
				$sql .= "pess_usuario = '" . $login . "' ";
			}
			if (!empty($login) && !empty($id)) {
				$sql .= " AND ";
			}
			if (!empty($id)) {
				$sql .= "idpessoas = '" . $id . "' ";
			}
			// hibrido da linguagem php com sql
			$query = $this->conexao->query($sql);
			$query->execute();
			$resultado = $query->fetch(PDO::FETCH_ASSOC);  // fetch = recuperação do resultado
			$query->closeCursor();
			return $resultado;
		}

		public function getUltimoID(){
			$nomeID = 'id'. $this->tabela;
			$sql = "SELECT " . $nomeID . " FROM " . $this->tabela . " ORDER BY " . $nomeID . " DESC LIMIT 1";
			$query = $this->conexao->query($sql);
			$query->execute();
			$resultado = $query->fetch(PDO::FETCH_ASSOC);  // fetch = recuperação do resultado
			$query->closeCursor();
			return $resultado[$nomeID];
		}

		public function consultar($sql){
		  	$sql = trim($sql);
		  	$this->erro = '';
		  	$this->msgErro = '';
			try{
				$query = $this->conexao->query($sql);
				$query->execute();
				$dados = $query->fetchAll(PDO::FETCH_ASSOC);
				$query->closeCursor();
				$this->erro = false;
			}catch(PDOException $e) {
				$resultado = NULL;
				$this->erro = true;
				$this->msgErro = $e->getMessage();
				$mensagem  = $e->getMessage();
				file_put_contents("../erro.log", "\n\nData: " . date("d/m/Y H:i") . "\nErro: " . $mensagem, FILE_APPEND);
				
			}
			//
			//print_r($dados);
		  return $dados;
		}

		public function retornaUmReg($sql){
			$this->erro = '';
			$this->msgErro = '';
			$sql = trim($sql);
			try{
				$query = $this->conexao->query($sql);
				$query->execute();
				$resultado = $query->fetch(PDO::FETCH_ASSOC);  // fetch = recuperação do resultado
				$query->closeCursor();
			}
			catch(PDOException $e) {
				$resultado = NULL;
				$this->erro = true;
				$this->msgErro = $e->getMessage();
				$mensagem  = $e->getMessage();
				file_put_contents("../erro.log", "\n\nData: " . date("d/m/Y H:i") . "\nErro: " . $mensagem, FILE_APPEND);
			}
		 	return $resultado;
		}

		public function retornaUmCampoSql($sql, $campo){
			$this->erro = '';
			$this->msgErro = '';
			$sql = trim($sql);
			try{
				// $resultado=$this->conexao->query($sql);
				// $this->erro = false;
				$query = $this->conexao->query($sql);
				$query->execute();
				$resultado = $query->fetch(PDO::FETCH_ASSOC);  // fetch = recuperação do resultado
				$query->closeCursor();
			}
			catch(PDOException $e) {
				$resultado = NULL;
				$this->erro = true;
				$this->msgErro = $e->getMessage();
				$mensagem  = $e->getMessage();
				file_put_contents("../erro.log", "\n\nData: " . date("d/m/Y H:i") . "\nErro: " . $mensagem, FILE_APPEND);
			}

			return $resultado[$campo];
		}

		public function retornaUmCampoID($campo, $tabela, $id){
			$this->erro = '';
			$this->msgErro = '';
			$sql = "SELECT {$campo} AS campo FROM {$tabela} WHERE id{$tabela} = {$id}";
			$sql = trim($sql);
			try{
				// $resultado=$this->conexao->query($sql);
				// $this->erro = false;
				$query = $this->conexao->query($sql);
				$query->execute();
				$resultado = $query->fetch(PDO::FETCH_ASSOC);  // fetch = recuperação do resultado
				$query->closeCursor();
			}
			catch(PDOException $e) {
				$resultado = NULL;
				$this->erro = true;
				$this->msgErro = $e->getMessage();
				$mensagem  = $e->getMessage();
				file_put_contents("../erro.log", "\n\nData: " . date("d/m/Y H:i") . "\nErro: " . $mensagem, FILE_APPEND);
			}

			return $resultado["campo"];
		}

    public function executaSQL($sql, $tipoMsg = ''){
		  $sql = trim($sql);
		  $this->erro = '';
		  $this->msgErro = '';
			try{
				$this->conexao->exec($sql);
				$this->erro = false;
				if(!empty($tipoMsg)) $this->geraMensagem($tipoMsg);
			}catch(PDOException $e) {
				$this->erro = true;
				$this->msgErro = $e->getMessage();
				$mensagem  = $e->getMessage();
				file_put_contents("../erro.log", "\n\nData: " . date("d/m/Y H:i") . "\nErro: " . $mensagem . "\nSQL: " . $sql, FILE_APPEND);
				
			}
	  	}

	  	public function gravarInserir($dados, $geraMensagem = false){
			$tipoMsg = '';
	  		if(!empty($dados['id'])){
				if($geraMensagem) $tipoMsg = "Alterar";
	  			return $this->alterar($dados, $tipoMsg);
	  		}else{
				if($geraMensagem) $tipoMsg = "Inserir";
	  			unset($dados['id']);
	  			return $this->gravar($dados, $tipoMsg);
	  		}
	  	}

		 public function gravar($dados = null, $tipoMsg = false){
			$campos   = implode(",",array_keys($dados));
			$valores  = implode(",",array_values($dados));
			$query = "INSERT INTO " . $this->tabela . " (" .
					  $campos." ) VALUES ( " . $valores . " ) ";
			//echo "$query<br>";
			//exit;
		    //
			return $this->executaSQL($query, $tipoMsg);
		 }

		 public function alterar($dados = null, $tipoMsg = false){
			if(!is_null($dados)){
				$valores = array();
				foreach($dados as $key=>$value){
					if($key != 'id') $valores[] = $key . " = " . $value;
				}
				$valores = implode(',',$valores);
				$query = "UPDATE " . $this->tabela . " SET " . $valores . " WHERE " . $this->idtabela . " = " . $dados['id'];
			    //echo "$query<br>";
			    
			return $this->executaSQL($query, $tipoMsg);
		  }else{
			return false;
			}
		}

		public function excluir($id = null, $tipoMsg = ""){
				if(!is_null($id)){
					$query = "DELETE FROM " . $this->tabela . " WHERE " . $this->idtabela . " = " . $id;
					return $this->executaSQL($query, $tipoMsg);
				}
				else{
					return false;
				}
			}

		public function retornaUmTel($idpessoas){
			$sql = "SELECT * FROM pessoas_numeros WHERE pnum_idpessoas = " . $idpessoas . " LIMIT 1";
			$res = $this->conexao->query($sql);
			$res->execute();
			$reg = $res->fetchAll(PDO::FETCH_ASSOC);
			if ($reg[0]['pnum_DDD'] != "") {
				$telefone = "(" . $reg[0]['pnum_DDD'] . ") " .  $reg[0]['pnum_numero'];
			}else{
				$telefone = $reg[0]['pnum_numero'];
			}

			return $telefone;
		}

		private function geraMensagem($tipoMsg){
			switch($tipoMsg){
				case 'Inserir':
					$_SESSION['mensagem'] = "Cadastro efetuada com sucesso!";
			    	$_SESSION['tipoMsg'] = "info";
					break;
				case 'Alterar':
					$_SESSION['mensagem'] = "Alteração efetuado com sucesso!";
	      			$_SESSION['tipoMsg'] = "info";
					break;
				case 'Excluir':
					$_SESSION['mensagem'] = "Cadastro excluido com sucesso!";
	    			$_SESSION['tipoMsg'] = "danger";
					break;	
				default:
					break;
			}
		}

		private function criaBanco($nomeBD, $host, $user){
			$sql = "CREATE DATABASE IF NOT EXISTS `{$nomeBD}`
			DEFAULT character SET UTF8
			DEFAULT collate utf8_general_ci;
			GRANT ALL ON `{$nomeBD}`.* TO '{$user}'@'{$host}';
    		FLUSH PRIVILEGES;";
    		if(!$this->conexao->exec($sql)){
    			echo 'Falha ao criar banco de dados!';
    		}
		} 

		private function criaTabelas($nomeBD){
			//
			//Crias as tabelas do banco de dados
			$sql = file_get_contents("../_BD/tabelas_sistema.sql");
			$sql = utf8_decode($sql);
			if($this->conexao->exec($sql)){
    			echo 'Falha ao criar as tabelas!';
    			exit;
    		}
		}

		private function criarBase(){
			//Conecta ao servidor e cria a base
			$dados = "mysql:host=" . $this->host;
			$dados = $dados . ";port=" . $this->porta;
			$this->conexao = new PDO($dados, $this->usudb, $this->senhadb);   // pdo php data objectve, classe generica pra banco de dados
			$this->criaBanco($this->nomedb, $this->host, $this->usudb);
			//
			//conecta ao banco e cria as tabelas
			$dados = $dados . ";dbname=" . $this->nomedb;
			$this->conexao = new PDO($dados, $this->usudb, $this->senhadb);   // pdo php data objectve, classe generica pra banco de dados
			$this->criaTabelas($this->nomedb);
			//
			//Retorna mensagem e pede login novamente
			$_SESSION['logado'] = false;
			$_SESSION['mensagem'] = "Executamos uma atualização do sistema <br> Tente novamente";
    		$_SESSION['tipoMsg'] = "info";
			header('Location: ../index.php');
			exit;
		}
	}


?>
