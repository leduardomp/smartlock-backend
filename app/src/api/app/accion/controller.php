<?php
// Include CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once __DIR__ . '/../../../util/authorization.php';
include_once __DIR__ . '/../../../util/util.php';

define('MESSAGE_FILE_METHOD_REQUEST', "Failed method request");

$token = getBearerToken();

if (!isset($token)) {
	http_response_code(401);
	echo message("Recurso no autorizado", true);
	exit;
}

//validaToken con proveedor
include_once  __DIR__ .  '/../../../util/jwt.php';

try {
	$payload = tokenChech($token);
} catch (Exception $e) {
	http_response_code(401);
	echo message($e->getMessage(), true);
	exit;
}

// Include action.php file
include_once __DIR__ . '/db.php';

// Create object of Users class
$db = new Database();

// create a api variable to get HTTP method dynamically
$api = $_SERVER['REQUEST_METHOD'];



if ($api == 'GET') {
	echo message(MESSAGE_FILE_METHOD_REQUEST, true);
}

// Enviar accion apertura
if ($api == 'POST') {
	$data = json_decode(file_get_contents("php://input"));

	$num_serie = $data->num_serie;

	//mensaje a mqtts
	include_once __DIR__ . '/../../../util/mqtt.php';

	$payload = array(
		'numserie' => $num_serie,
		'accion' => 1
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

	echo message('accion enviada', false);
	//guardar en log
	
}

//Modificar chapa
if ($api == 'PUT') {
	echo message(MESSAGE_FILE_METHOD_REQUEST, true);
}

// Eliminar chapa
if ($api == 'DELETE') {
	echo message(MESSAGE_FILE_METHOD_REQUEST, true);
}
