<?php
date_default_timezone_set('America/Mexico_City');
// Include CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');


include_once __DIR__ . '/../../../util/authorization.php';
include_once __DIR__ . '/../../../util/util.php';

$token = getBearerToken();

if (!isset($token)) {
	http_response_code(401);
	echo messageAlexa("Recurso no autorizado", true, 1);
	exit;
}

//validaToken con Servidor OAuth2
include_once 'al_db_OAuth.php';
$db_OAuth = new DatabaseOAuth();
$dataOAuth = $db_OAuth->getDataToken($token);

//validar que existe
if (isset($dataOAuth)) {
	//validar que no haya expirado
	if (!vigenciaToken($dataOAuth[0]['expires'])) {
		//EXPIRED_AUTHORIZATION_CREDENTIAL
		echo messageAlexa('token expired', true, 1);
		exit;
	}
} else {
	//INVALID_AUTHORIZATION_CREDENTIAL
	echo messageAlexa('token invalid', true, 2);
	exit;
}

// Include action.php file
include_once 'al_db.php';
$db = new Database();

// create a api variable to get HTTP method dynamically
$api = $_SERVER['REQUEST_METHOD'];

// Alexa - Discovery
if ($api == 'GET') {

	$id_user = $dataOAuth[0]['user_id'];

	if ($_GET['num_serie']) {

		$num_serie = $_GET['num_serie'];
		$data = $db->getEstatusByUserChapa($num_serie, $id_user);
		echo json_encode($data);

	}else{

		if (isset($id_user)) {
			$data = $db->getChapasUsuario($id_user);
			echo json_encode($data);

		} else {
			http_response_code(404);
			echo messageAlexa("Recurso no encontrado", true, 404);
		}
	}
}

//Alexa open the door
if ($api == 'PUT') {

	$id_user = $dataOAuth[0]['user_id'];
	$data = json_decode(file_get_contents("php://input"));

	$num_serie = $data->num_serie;
	$accion = $data->id_accion;

	//write_log("variables:" . $num_serie ." ".$accion);

	if (isset($id_user) && isset($num_serie)) {
		$data = $db->updateAccionChapa($num_serie, $id_user, $accion);
		echo json_encode([['data'=>$data]]);

	} else {
		http_response_code(404);
		echo messageAlexa("Recurso no encontrado", true, 404);

	}
	exit;
}
