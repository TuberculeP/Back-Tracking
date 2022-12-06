<?php
require_once '../../classes/user.php';
if (!($_GET && isset($_GET['user']))) {
	http_response_code(404);
}else{
	$user = User::find($_GET['user']);
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($user->getSeen());
}