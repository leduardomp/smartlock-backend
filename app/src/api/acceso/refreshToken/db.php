<?php
// Include config.php file
include_once __DIR__ . '/../../../util/config.php';

// Create a class Users
class Database extends Config
{

	public function getUltimoToken($username){
		$sql = 'SELECT token FROM usuario';
		$sql .= ' WHERE us_id_email = :username';
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['username' => $username]);
		return ($stmt->fetchAll())[0]['token']; 
	}

	public function saveToken($token, $username)
	{
		$sql = 'UPDATE usuario SET token=:token WHERE us_id_email=:user';
		$stmt = $this->conn->prepare($sql);
		return $stmt->execute(['token'=>$token, 'user' => $username]);
	}
}
