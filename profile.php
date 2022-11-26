<?php
$page_title = 'IIMovies';
require_once 'template/header.php';
if(!isset($_SESSION['user'])) {
	header('location:./login.php');
}
if(!isset($_GET['id'])){
    header('location:./profile.php?id='.$_SESSION['id']);
}

require_once 'classes/connection.php';
$db = new Connection();
$albums = $db->getAlbums($_GET['id']);

$user = User::getName($_GET['id']);

?>
	
	<main class="profile">
		<h1><?=$user['pseudo']?></h1>
        <h2>Albums</h2>
        <div>
            <?php foreach ($albums as $album):?>
            <section>
                <h3><?=$album['name']?></h3>
                <ul>
                    <li>Vues : <?=$album['view']?></li>
                    <li>Likes : <?=$album['like']?></li>
                </ul>
                <a href="album.php?id=<?=$album['id']?>">Voir</a>
            </section>
            <?php endforeach; ?>
        </div>
        
	</main>

<?php
require_once './template/footer.php';
?>