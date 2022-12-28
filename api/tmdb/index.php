<?php

if($_GET && $_GET['q']){
	$colle = '?';
	
	
	if(str_contains($_GET['q'], '?')){
		$colle = '&';
	}
	
	$url = 'https://api.themoviedb.org/3/'
		.$_GET['q']
		.$colle.'api_key=d3151e4e15cfce47f5840fd3c57988df&language=fr';
	$url = str_replace('@', '&', $url);
	
	$ch_session = curl_init();
	curl_setopt($ch_session, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch_session, CURLOPT_URL, $url);
	$result_url = curl_exec($ch_session);
	header('Content-Type: application/json; charset=utf-8');
	echo $result_url;
}else{
	echo json_encode(['error' => 'No search provided']);
}