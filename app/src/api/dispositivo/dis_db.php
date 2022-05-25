<?php
	// Include config.php file
	include_once '../config.php';

	// Create a class Users
	class Database extends Config {

	  // Fetch all or a single user from database
	  public function fetch($numSerie = 0) {
	    $sql = 'SELECT abrir FROM chapa WHERE ch_num_serie = :numSerie';
	    $stmt = $this->conn->prepare($sql);
	    $stmt->execute(['numSerie' => $numSerie]);
	    $rows = $stmt->fetchAll();
	    return $rows;
	  }
 
	  // Insert an user in the database
	  public function apertura($email, $numSerie) {

		$this->conn->beginTransaction();
		date_default_timezone_set('America/Mexico_City');
	    $sql = 'INSERT INTO accion_chapa (us_id_email, ch_num_serie, ac_fecha, cac_id) VALUES (:email, :numSerie, :fecha, 1)';
	    $stmt = $this->conn->prepare($sql);
	    $result = $stmt->execute(['email' => $email, 'numSerie' => $numSerie, 'fecha' => date("Y-m-d H:i:s")]);
		
		if($result){
			/*
			$sql = 'UPDATE chapa SET abrir = 1 WHERE ch_num_serie = :numSerie';
	    	$stmt = $this->conn->prepare($sql);
			if($stmt->execute(['numSerie' => $numSerie])){
				$this->registroLog($email, 1, 'Chapa - '.$numSerie);
				$this->conn->commit();
				return true;
			}else{
				$this->conn->rollBack();
				return false;
			}
			*/
			include_once __DIR__ . '/../../util/mqtt.php';
			
			$payload = array(
				'numserie' => $num_serie,
				'accion' => $valDispo
			);

			$mqtt->publish(
				// topic
				'smartlocksystem',
				// payload
				json_encode($payload),
				// qos
				0,
				// retain
				true
			);
			$mqtt->disconnect();
			$this->registroLog($id_usuario, $accion, 'Chapa - '.$num_serie);
			return true;
		}else{
			$this->conn->rollBack();
			return false;
		}

	  }

	  // Insert an user in the database
	  public function cerrar($numSerie) {

		$this->conn->beginTransaction();
		date_default_timezone_set('America/Mexico_City');
	    $sql = 'INSERT INTO accion_chapa (us_id_email, ch_num_serie, ac_fecha, cac_id) VALUES ("system", :numSerie, :fecha, 1)';
	    $stmt = $this->conn->prepare($sql);
	    $result = $stmt->execute(['numSerie' => $numSerie, 'fecha' => date("Y-m-d H:i:s")]);
		
		if($result){
			$sql = 'UPDATE chapa SET abrir = 0 WHERE ch_num_serie = :numSerie';
	    	$stmt = $this->conn->prepare($sql);

			if($stmt->execute(['numSerie' => $numSerie])){
				$this->registroLog("system", 2, 'Chapa - '.$numSerie);
				$this->conn->commit();
				return true;

			}else{
				$this->conn->rollBack();
				return false;

			}
		}else{
			$this->conn->rollBack();
			return false;
			
		}

	  }
      
	}

?>