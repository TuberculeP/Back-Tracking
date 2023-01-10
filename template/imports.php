<?php
if(isset($back)){
	require_once '../classes/connection.php';
	require_once '../classes/user.php';
	require_once '../classes/album.php';
}else{
	require_once './classes/connection.php';
	require_once './classes/user.php';
	require_once './classes/album.php';
}

session_start();

if(!isset($_SESSION['current'])){
	$_SESSION['current'] = $_SERVER['REQUEST_URI'];
}
if($_SERVER['REQUEST_URI'] !== '/favicon.ico'){
	$_SESSION['previous'] = $_SESSION['current'];
	$_SESSION['current'] = $_SERVER['REQUEST_URI'];
}
?>