<?php
// Include CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../../authorization.php';
include_once __DIR__ . '/../../util.php';

$token = getBearerToken();

if (!isset($token)) {
	http_response_code(401);
	echo message("Recurso no autorizado", true);
	exit;
}

//validaToken con proveedor
include_once '../../jwt.php';

try {
	$val = tokenChech($token);
} catch (Exception $e) {
	http_response_code(401);
	echo message($e->getMessage(), true);
	exit;
}

// Include action.php file
include_once 'ch_us_db.php';
// Create object of Users class
$db = new Database();

// create a api variable to get HTTP method dynamically
$api = $_SERVER['REQUEST_METHOD'];

// get id from url
$id_usuario = $_GET['id_usuario'];

// Get all or a single user from database
if ($api == 'GET') {
	if (isset($id_usuario) && !empty($id_usuario)) {
		$data = $db->fetch($id_usuario);
	} else {
		http_response_code(404);
		echo message("Recurso no encontrado", false);
		exit;
	}
	echo json_encode($data);
}

// Add a new user into database
if ($api == 'POST') {
	$name = test_input($_POST['name']);
	$email = test_input($_POST['email']);
	$phone = test_input($_POST['phone']);

	if ($db->insert($name, $email, $phone)) {
		echo message('User added successfully!', false);
	} else {
		echo message('Failed to add an user!', true);
	}
}

// Update an user in database
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

// Delete an user from database
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
