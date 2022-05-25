<?php
	// Include CORS headers
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	header('Content-Type: application/json; charset=UTF-8');
	header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

	include_once __DIR__ . '/../authorization.php';
	include_once __DIR__ . '/../util.php';
	$token = getBearerToken();

	if (!isset($token)) {
		http_response_code(401);
		echo message("Recurso no autorizado", true);
		exit;
	}

	//validaToken con proveedor
	include_once __DIR__ . '/../jwt.php';

	try{
		$payload = tokenChech($token);
	}catch(Exception $e){
		http_response_code(401);
		echo message($e->getMessage(), true);
		exit;
	}

	// Include action.php file
	include_once 'dis_db.php';
	// Create object of Users class
	$db = new Database();

	// create a api variable to get HTTP method dynamically
	$api = $_SERVER['REQUEST_METHOD'];

	// Accion apertura
	if ($api == 'POST') {
		$data = json_decode(file_get_contents("php://input"));
		$numSerie = $data->num_serie;

		if ($db->apertura($payload->data->email, $numSerie)) {
			echo message('Open successfully!', false);
		} else {
			echo message('Failed to open the lock!', true);
		}
	}
?>