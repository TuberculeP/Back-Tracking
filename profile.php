<?php
$page_title = 'IIMovies';
require_once 'template/header.php';
if(!isset($_SESSION['user'])) {
	header('location:./login.php');
}
if(!isset($_GET['id'])){
    header('location:./profile.php?id='.$_SESSION['id']);
}

require_once 'classes/album.php';
$albums = Album::all($_GET['id']);

$user = User::getName($_GET['id']);

?>
	
	<main class="profile">
		<h1><?=$user['pseudo']?></h1>
        
        <div>
            <h2>Albums</h2>
            <div class="album-container">
				<?php foreach ($albums as $album):
                    if($album->is_public || $_SESSION['user']->isContributor($album->getStuff())):?>
                    <a href="album.php?id=<?=$album->id?>">
                        <section>
                            <img src='<?=$album->getThumbnail()?>' alt='<?=$album->name?>'>
                            <div>
                                <h3><?=$album->name?></h3>
                                <ul>
                                    <li>Vues : <?=$album->view?></li>
                                    <li>Likes : <?=$album->like?></li>
                                </ul>

                            </div>
                        </section>
                    </a>
				<?php  endif; endforeach; ?>
            </div>
        </div>
        <div>
            <h2>Likes</h2>
            <div class="album-container">
				
            </div>
        </div>
        
	</main>

<?php
require_once './template/footer.php';
?>