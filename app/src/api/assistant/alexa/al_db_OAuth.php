<?php
// Include config.php file
include_once __DIR__ . '/../../../util/config_OAuth.php';

// Create a class Users
class DatabaseOAuth extends Config_OAuth
{
	//Trear infarmacion del Token
	public function getDataToken($token)
	{
		$sql = "SELECT * FROM oauth_access_tokens WHERE access_token = :token and scope = 'admin'";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['token' => $token]);
		return $stmt->fetchAll();
	}

}
