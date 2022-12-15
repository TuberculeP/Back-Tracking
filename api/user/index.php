<?php
require_once '../../classes/user.php';
$raw = User::search($_GET['query']??"");
$data = ["users" => []];
$count = 0;
foreach($raw as $raw_line){
	
	$user = [
		"pseudo" => $raw_line["pseudo"],
		"id" => $raw_line['id']
	];
	
	$data['users'][] = $user;
	$count++;
}

$data['total_result'] = $count;
header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);