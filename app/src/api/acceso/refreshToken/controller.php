<?php
// Include CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require_once __DIR__ . '/../../../util/authorization.php';
require_once __DIR__ . '/../../../util/util.php';
require_once __DIR__ . '/../../../util/jwt.php';

$token = getBearerToken();

if (!isset($token)) {
	http_response_code(401);
	echo message("Recurso no autorizado", true);
	exit;
}

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

// Add a new user into database
if ($api == 'POST') {

	$username = $payload->data->email;

	//ultimo token
	$lastToken = $db->getUltimoToken($username);

	if (strcmp($token, $lastToken) == 0) {
		$time = time();

		$payload = array(
			'exp' => $time + (60 * 60),
			'email' => $username
		);

		$newtoken = encodeToken($payload);

		if ($db->saveToken($newtoken, $username)) {
			echo json_encode(['token' => $newtoken, 'code' => 0]);
		} else {
			echo json_encode(['code' => 1]);
		}
	} else {
		echo json_encode(['code' => 2]);
	}
} else {
	echo message("Failed method request", true);
}
