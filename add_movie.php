<pre>
<?php
require_once './classes/user.php';
require_once './classes/connection.php';
session_start();

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
	
	if(isset($_POST['new_album']) && $_POST['new_album'] !== ""){
		$new_album = $_SESSION['user']->createAlbum($_POST['new_album']);
		var_dump($new_album);
		$db->albumUpdate('insert', $_GET['id'], $new_album['id']);
	}
	
}
header('location:.'.$_SESSION['current']);