<?php
require_once '../classes/user.php';
$raw = User::search($_GET['query']);
$data = ["users" => [], "total_result" => 0];
$count = 0;
foreach($raw as $raw_line){
	
	$user = [
		"pseudo" => $raw_line["pseudo"],
		"id" => $raw_line['id']
	];
	
	array_push($data['users'], $user);
	$count++;
}

$data['total_result'] = $count;
header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);
