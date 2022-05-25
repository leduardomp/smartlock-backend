<?php
require_once("claves.php");

class Config_OAuth
{
	// Data Source Network
	private $dsn = 'mysql:host=' . DBHOST_OA . ';dbname=' . DBNAME_OA . ';charset=utf8';
	// conn variable
	protected $conn = null;

	// Constructor Function
	public function __construct()
	{
		try {
			$this->conn = new PDO($this->dsn, DBUSER_OA, DBPASS_OA);
			$this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			die('Connectionn Failed : ' . $e->getMessage());
		}
		return $this->conn;
	}

	// Sanitize Inputs
	public function test_input($data)
	{
		$data = strip_tags($data);
		$data = htmlspecialchars($data);
		$data = stripslashes($data);
		$data = trim($data);
		return $data;
	}

	public function registroLog($user_email, $id_accion, $descripcion)
	{
		date_default_timezone_set('America/Mexico_City');
		$sql = 'INSERT INTO log_acciones (us_id, cta_id, la_fecha, la_desc) VALUES (:user, :idAccion, :fecha, :descripcion)';
		$stmt = $this->conn->prepare($sql);
		$stmt->execute([
			'user' => $user_email,
			'idAccion' => $id_accion,
			'fecha' => date("Y-m-d H:i:s"),
			'descripcion' => $descripcion
		]);
	}
}
