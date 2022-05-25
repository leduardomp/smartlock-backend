<?php
require_once("claves.php");

class Config
{
	// Data Source Network
	private $dsn = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME . ';charset=utf8';
	// conn variable
	protected $conn = null;

	// Constructor Function
	public function __construct()
	{
		try {
			$this->conn = new PDO($this->dsn, DBUSER, DBPASS);
			$this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			die('Connectionn Failed : ' . $e->getMessage());
		}
		return $this->conn;
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
