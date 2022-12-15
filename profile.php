<?php
$page_title = 'IIMovies';
require_once 'template/header.php';
if(!isset($_SESSION['user'])) {
	header('location:./login.php');
}
if(!isset($_GET['id'])){
    header('location:./profile.php?id='.$_SESSION['id']);
}

if($_POST && isset($_POST['preferences'])){
    //il faut update le profil
	$_SESSION['user']->updateStuff([
		'description'=>$_POST['description'],
		'want_adult'=>$_POST['want_adult']??false
    ]);
	header('location:./profile.php?id='.$_SESSION['id']);
}

require_once 'classes/album.php';
$albums = Album::all($_GET['id']);

$user = User::find($_GET['id']);
$stuff = $user->getStuff();
?>
	
	<main class="profile">
		<h1><?=$user->pseudo?></h1>
        
        <div class="description">
            <h3><?=$user->first_name." ".$user->last_name?></h3>
            <p><?=$stuff['description']??""?></p>
        </div>
        
        <?php if($_SESSION['id'] === (int)$_GET['id']):?>
            <?php
        
            ?>
        <form class="preferences" method="post">
            <input type="hidden" name="preferences">
            <h4>Préférences :</h4>
            <hr>
            <label for="desc">Description</label>
            <textarea name="description" id="desc"><?=$stuff['description']??""?></textarea>
            <label for="want_adult">Voir les films +18 ans</label>
            <input type="checkbox" name="want_adult" id="want_adult"
                <?=(isset($stuff['want_adult']) && $stuff['want_adult']===1)?'checked':''?>>
            <hr>
            <button type="submit">Envoyer</button>
        </form>
        <?php endif;?>
        
        <div>
            <h2>Albums</h2>
            <div class="album-container">
				<?php foreach ($albums as $album):
                    if($album->is_public || $_SESSION['user']->isContributor($album->getStuff())):?>
                    <a href="album.php?id=<?=$album->id?>">
                        <section>
                            <img src='<?=$album->getThumbnail()!='https://image.tmdb.org/t/p/w500'
                                ?$album->getThumbnail()
                                :'https://cdn.pixabay.com/photo/2012/04/15/18/57/dvd-34919_960_720.png'?>'
                                 alt='<?=$album->name?>'>
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
				<?php $albums = Album::getLiked($_SESSION['user']->getID());
                foreach ($albums as $album):
					if($album->is_public || $_SESSION['user']->isContributor($album->getStuff())):?>
                        <a href="album.php?id=<?=$album->id?>">
                            <section>
                                <img src='<?=$album->getThumbnail()!='https://image.tmdb.org/t/p/w500'
									?$album->getThumbnail()
									:'https://cdn.pixabay.com/photo/2012/04/15/18/57/dvd-34919_960_720.png'?>'
                                     alt='<?=$album->name?>'>
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
        
	</main>

<?php
require_once './template/footer.php';
?>