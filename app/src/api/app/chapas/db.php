<?php
	// Include config.php file
	include_once __DIR__ . '/../../../util/config.php';

	// Create a class Users
	class Database extends Config {

	  // Fetch all or a single user from database
	  public function fetch($username, $numSerie = null) {

		$parametros = ['username' => $username];

	    $sql = 'SELECT ch.ch_num_serie as num_serie, ch.ch_alias as alias, ch.abrir as accion';
		$sql .= ' FROM chapa ch, usuario_has_chapa uhc, usuario u';
		$sql .= ' WHERE ch.ch_num_serie = uhc.ch_num_serie';
		$sql .= ' AND uhc.us_id_email = u.us_id_email';
		$sql .= ' AND u.us_id_email = :username';
		
		if (isset($numSerie)) {
	      $sql .= ' AND ch.ch_num_serie = :num_serie';
		  array_push($parametros,['num_serie'=>$numSerie]);
	    }

		$stmt = $this->conn->prepare($sql);
		$stmt->execute($parametros);
	    return $stmt->fetchAll();
	  }

	  // Insert an user in the database
	  public function insert($num_serie, $alias, $username) {

		try{
			$this->conn->beginTransaction();

			$sql = 'INSERT INTO chapa (ch_num_serie, ch_alias, abrir) VALUES (:num_serie, :alias, 0)';
	    	$stmt = $this->conn->prepare($sql);
	    	$stmt->execute(['num_serie' => $num_serie, 'alias' => $alias]);

			$sql = 'INSERT INTO usuario_has_chapa (ch_num_serie, us_id_email) VALUES (:num_serie, :user)';
	    	$stmt = $this->conn->prepare($sql);
	    	$stmt->execute(['num_serie' => $num_serie, 'user' => $username]);

			$this->conn->commit();
			return true;

		} catch (PDOException $e) {
            $this->conn->rollBack();
            die($e->getMessage());
        }
	    
	  }

	  // Update an user in the database
	  public function update($name, $email, $phone, $id) {
	    $sql = 'UPDATE users SET name = :name, email = :email, phone = :phone WHERE id = :id';
	    $stmt = $this->conn->prepare($sql);
	    $stmt->execute(['name' => $name, 'email' => $email, 'phone' => $phone, 'id' => $id]);
	    return true;
	  }

	  // Delete an user from database
	  public function delete($id) {
	    $sql = 'DELETE FROM users WHERE id = :id';
	    $stmt = $this->conn->prepare($sql);
	    $stmt->execute(['id' => $id]);
	    return true;
	  }
      
	}

?>