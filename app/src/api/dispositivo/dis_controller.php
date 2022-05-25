<?php
	// Include CORS headers
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	header('Content-Type: application/json; charset=UTF-8');
	header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

	include_once '../authorization.php';
	include_once __DIR__.'/../util.php';

	$token = getBearerToken();

	if (!isset($token)) {
		http_response_code(401);
		echo message('Recurso no autorizado', true);
		exit;
	}

	include_once 'dis_db.php';
	$db = new Database();
	// create a api variable to get HTTP method dynamically
	$api = $_SERVER['REQUEST_METHOD'];

	// get id from url
	$numSerie = $_GET['num_serie'];

	// Me tengo que abrir ¿?
	if ($api == 'GET') {
		if (isset($numSerie)) {
			$data = $db->fetch($numSerie);
		} else {
			http_response_code(404);
			echo message("Recurso no encontrado", true);
			exit;
		}
		echo json_encode($data);
	}

	// Accion cerrar
	if ($api == 'POST') {

		$data = json_decode(file_get_contents("php://input"));
		$numSerie = $data->num_serie;

		if ($db->cerrar($numSerie)) {
			echo message('Close successfully!', false);
		} else {
			echo message('Failed to close the lock!', true);
		}
	}
?>