<?php
// Include CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once __DIR__ . '/../../util/util.php';
require_once __DIR__ . '/../../util/jwt.php';
include_once 'db.php';

// Create object of Users class
$db = new Database();

// create a api variable to get HTTP method dynamically
$api = $_SERVER['REQUEST_METHOD'];

// Add a new user into database
if ($api == 'POST') {

	$data = json_decode(file_get_contents("php://input"));
	$username = $data->username;
	$password = $data->password;

	if (isset($username)) {
		$val = $db->acceso($username);

		if (count($val) == 1) {

			if ($val[0]['us_password'] != null && $val[0]['us_password'] == sha1($password)) {
				//obtener token
				$time = time();

				$payload = array(
					'exp' => $time + (60 * 60),
					'email' => $username
				);

				$token = encodeToken($payload);

				if($db->saveToken($token, $username)){
					echo json_encode(['token' => $token, 'code' => 0]);
				}else{
					echo json_encode(['code' => 2]);
				}
				
			} else {
				echo json_encode(['code' => 1]);
			}
			
		} else {
			echo json_encode(['code' => 1]);
		}
	} else {
		echo json_encode(['code' => 1]);
	}
}