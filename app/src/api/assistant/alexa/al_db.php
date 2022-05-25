<?php
// Include config.php file
include_once __DIR__ . '/../../../util/config.php';


// Create a class Users
class Database extends Config
{

	// Fetch all or a single user from database
	public function getChapasUsuario($id_user)
	{

		$sql = 'SELECT chapa.ch_num_serie, chapa.ch_alias, chapa.abrir';
		$sql .= ' FROM chapa, usuario_has_chapa as uhc, usuario ';
		$sql .= ' WHERE chapa.ch_num_serie = uhc.ch_num_serie';
		$sql .= ' AND uhc.us_id_email = usuario.us_id_email';
		$sql .= ' AND usuario.us_id_email = :id_usuario';
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['id_usuario' => $id_user]);
		$rows = $stmt->fetchAll();
		return $rows;
	}

	public function getEstatusByUserChapa($id_chapa, $id_usuario)
	{
		$sql = 'SELECT chapa.abrir';
		$sql .= ' FROM chapa, usuario_has_chapa as uhc, usuario';
		$sql .= ' WHERE chapa.ch_num_serie = uhc.ch_num_serie';
		$sql .= ' AND uhc.us_id_email = usuario.us_id_email';
		$sql .= ' AND usuario.us_id_email = :id_usuario';
		$sql .= ' AND chapa.ch_num_serie = :id_chapa';
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['id_usuario' => $id_usuario, 'id_chapa' => $id_chapa]);
		$rows = $stmt->fetchAll();
		return $rows;
	}

	public function updateAccionChapa($num_serie, $id_usuario, $accion)
	{
		
		if($accion == 1){
			$valDispo = 1;
		}else{
			$valDispo = 0;
		}

		$this->conn->beginTransaction();
		// date_default_timezone_set('America/Mexico_City');
	    $sql = 'INSERT INTO accion_chapa (us_id_email, ch_num_serie, ac_fecha, cac_id) VALUES (:email, :numSerie, :fecha, :accion)';
	    $stmt = $this->conn->prepare($sql);
	    $result = $stmt->execute(['email' => $id_usuario, 'numSerie' => $num_serie, 'fecha' => date("Y-m-d H:i:s"), 'accion' => $accion]);
		
		if($result){
			/*
			$sql = 'UPDATE chapa SET abrir = :accion WHERE ch_num_serie = :numSerie';
	    	$stmt = $this->conn->prepare($sql);
			
			if($stmt->execute(['numSerie' => $num_serie, 'accion' => $valDispo])){
				$this->registroLog($id_usuario, $accion, 'Chapa - '.$num_serie);
				$this->conn->commit();
				return true;

			}else{
				$this->conn->rollBack();
				return false;
			}
			*/

			include_once __DIR__ . '/../../../util/mqtt.php';
			
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
}
