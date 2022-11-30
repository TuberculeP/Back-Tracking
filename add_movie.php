<pre>
<?php
require_once './classes/user.php';
require_once './classes/album.php';
session_start();

if($_GET && isset($_GET['id']) && $_POST){
	//stuff to do here
	
	$albums = Album::all($_SESSION['id']);
	foreach ($albums as $album){
		if(in_array($album->id, array_keys($_POST))){
			if(!$album->contains($_GET['id'])){
				$album->add($_GET['id']);
				
			}else{
				if($_POST[$album['id']] === 'delete'){
					
					$album->delete($_GET['id']);
				}
			}
		}else{
			if($album->contains($_GET['id'])){
				
				echo 'Cas3';
				
				$album->delete($_GET['id']);
				
			}
			
		}
	}
	
	if(isset($_POST['new_album']) && $_POST['new_album'] !== ""){
		$new_album = $_SESSION['user']->createAlbum($_POST['new_album']);
		var_dump($new_album);
		$new_album->update('insert', $_GET['id']);
	}
	
}
header('location:.'.$_SESSION['current']);