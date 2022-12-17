<?php
$page_title = "Invitation";
$back = true;
require_once '../template/header.php';
$link = false;
if ($_GET && isset($_GET['key'])) {
	$link = $_SESSION['user']->link_get($_GET['key']);
}
?>
<main>
	<?php
if($link === false){
    echo '<h1>Le lien n\'existe pas ou a expiré</h1>';
}else{
    if($link['user_id'] === $_SESSION['id']){
		echo '<h2>Tu peux pas t\'inviter toi-même incapable</h2>';
	}else{
        
        $album = Album::find($link['album_id']);
        $stuff = $album->getStuff();
        
        if($_SESSION['user']->isContributor($stuff)){
            echo '<h2>Tu es déjà contributeur de cet album</h2>';
        }else{
            echo '<h1>Vous avez été invité en temps que contributeur</h1>';
			$_SESSION['user']->contribute($link['album_id']);
        }
		?>
        
        <a href="../album.php?id=<?=htmlspecialchars($link['album_id'])?>">Voir l'album</a>
        <?php
	}
}

?>

</main>
<?php
require_once '../template/footer.php';