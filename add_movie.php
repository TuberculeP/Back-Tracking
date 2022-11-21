
<?php
require_once './classes/user.php';
require_once './classes/connection.php';
session_start();
var_dump($_GET, $_POST);

if($_GET && isset($_GET['id']) && $_POST){
	//stuff to do here
	$db = new Connection();
	$albums = $db->getAlbums($_SESSION['id']);
	
	foreach ($albums as $album){
		if(in_array($album['id'], array_keys($_POST))){
			if(!$db->movieInAlbum($_GET['id'], $album['id'])){
				
				echo 'Cas1';
				
				$db->albumUpdate('insert', $_GET['id'], $album['id']);
				
			}else{
				if($_POST[$album['id']] === 'delete'){
					
					echo 'Cas2';
					
					$db->albumUpdate('delete', $_GET['id'], $album['id']);
				}
			}
		}else{
			if($db->movieInAlbum($_GET['id'], $album['id'])){
				
				echo 'Cas3';
				
				$db->albumUpdate('delete', $_GET['id'], $album['id']);
				
			}
			
		}
	}
	header('location:.'.$_SESSION['current']);
}