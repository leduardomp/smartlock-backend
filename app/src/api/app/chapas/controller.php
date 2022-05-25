<?php
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
include_once 'db.php';

// Create object of Users class
$db = new Database();

// create a api variable to get HTTP method dynamically
$api = $_SERVER['REQUEST_METHOD'];

// get id from url
$numSerie = $_GET['num_serie'];
$username = $payload->data->email;

// Obtener chapas by username
if ($api == 'GET') {
	if (isset($numSerie) && !empty($numSerie)) {
		$data = $db->fetch($username, $numSerie);
	} else {
		$data = $db->fetch($username);
	}
	echo json_encode($data);
}

// Agregar chapa
if ($api == 'POST') {
	$data = json_decode(file_get_contents("php://input"));

	$num_serie = $data->num_serie;
	$alias = $data->alias;

	//valida que no este registrada

	if(isset($num_serie) || isset($alias)){
		if ($db->insert($num_serie, $alias, $username)) {
			echo message('Chapa agregada correctamente', false);
		} else {
			echo message('Falla 1 al agregar la chapa', true);
		}
	}else{
		echo message('Falla 2 al agregar la chapa', true);
	}
	
}

//Modificar chapa
if ($api == 'PUT') {
	parse_str(file_get_contents('php://input'), $post_input);

	$name = test_input($post_input['name']);
	$email = test_input($post_input['email']);
	$phone = test_input($post_input['phone']);

	if ($id != null) {
		if ($db->update($name, $email, $phone, $id)) {
			echo message('User updated successfully!', false);
		} else {
			echo message('Failed to update an user!', true);
		}
	} else {
		echo message('User not found!', true);
	}
}

// Eliminar chapa
if ($api == 'DELETE') {
	if ($id != null) {
		if ($db->delete($id)) {
			echo message('User deleted successfully!', false);
		} else {
			echo message('Failed to delete an user!', true);
		}
	} else {
		echo message('User not found!', true);
	}
}
