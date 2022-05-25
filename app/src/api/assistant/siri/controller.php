<?php
date_default_timezone_set('America/Mexico_City');
// Include CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');


include_once __DIR__ . '/../../../util/util.php';


// Include action.php file
include_once 'db.php';
$db = new Database();

// create a api variable to get HTTP method dynamically
$api = $_SERVER['REQUEST_METHOD'];

//Alexa open the door
if ($api == 'PUT') {

	$data = json_decode(file_get_contents("php://input"));

	$num_serie = $data->num_serie;
	$id_user = $data->email;
	$token = $data->token;

	//write_log("variables:" . $num_serie ." ".$accion);

	if (isset($id_user) && isset($num_serie) && isset($token)) {
		$data = $db->updateAccionChapa($num_serie, $id_user, 1, $token);
		echo json_encode([['data'=>$data]]);

	} else {
		http_response_code(404);
		echo messageAlexa("Recurso no encontrado", true, 404);

	}
	exit;
}
