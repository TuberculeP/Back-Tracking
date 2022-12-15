<?php
$page_title = 'IIMovies';
require_once 'template/header.php';
if(!isset($_SESSION['user'])) {
	header('location:./login.php');
}


if($_GET && isset($_GET['id'])){
	require_once 'classes/album.php';
	$album = Album::find($_GET['id']);
    if($album){
		$stuff = $album->getStuff();
	
        if($_POST && isset($_POST['liked'])){
            $album->toggleLike($_SESSION['user']->getID());
            header('location:./album.php?id='.$album->id);
        }
        
		if($_POST && isset($_POST['delete_album'])){
			if($_SESSION['user']->isContributor($stuff)){
				Album::deleteAll($_GET['id']);
				header('location:./profile.php');
			}
		}
	
		if(sizeof($stuff['movie']) === 0){
			header('location:./profile.php');
		}
		if($album->is_public || $_SESSION['user']->isContributor($stuff)){
            $album->addView();
            $album->view
    
?>
	
	<main class="profile">
		
		<h1><?=$album->name?></h1>
        <form method="post">
            <input type="hidden" name="delete_album">
            <button type="submit">Supprimer l'album</button>
        </form>
        <h2>Par : <?php
            foreach ($stuff['contributor'] as $contributor){
            ?>
                <a href="profile.php?id=<?=$contributor['id']?>"><?=$contributor['pseudo']?></a>
                <?php
            }?></h2>
        
		
        <h2>Infos :</h2>
        <ul>
            <li>Vues : <?=$album->view?></li>
            <li>Likes : <?=$album->like?></li>
        </ul>

        <?php if($album->is_public):?>
                <form action="./album.php?id=<?=$album->id?>" method="post">
                    <input type="hidden" name="liked" value="<?=$_SESSION['user']->hasLiked($album->id)?1:0?>">
                    <button type="submit"><?=$_SESSION['user']->hasLiked($album->id)?'Supprimer des likes':'Liker'?></button>
                </form>
        <?php endif;?>
        
        <h2>Inviter à contribuer :</h2>
        <form action="./invite/create.php" method="post">
            <input type="hidden" name="album" value="<?=$_GET['id']?>">
            <button type="submit">Générer un lien</button>
        </form>
        
        <h1>Film(s) : </h1>
        
        <div>
            <?php
            foreach ($stuff['movie'] as $movie_id){
				$url_name = 'https://api.themoviedb.org/3/movie/' . $movie_id
                    . '?api_key=d3151e4e15cfce47f5840fd3c57988df&language=fr';
				$ch_session = curl_init();
				curl_setopt($ch_session, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch_session, CURLOPT_URL, $url_name);
				$result_url = curl_exec($ch_session);
				$result = json_decode($result_url, true);
            ?>
            <a href="./movie.php?id=<?=$result['id']?>" class="result">
                <section class="result">
                    <img src="https://image.tmdb.org/t/p/w500<?=$result['poster_path']?>"
                         alt="poster_for <?=$result['id']?>">
                    <div>
                        <h2>
							<?php
							if($result['original_language'] === 'fr'){
								echo $result['original_title'];
							}else{
								echo $result['title'];
							}
							echo ' ('.explode('-',$result['release_date'])[0].')'
							?>
                        </h2>
                        <p>Indice de Popularité : <?=$result['popularity']?></p>
                        <p>Note : <?=$result['vote_average']?>/10</p>
                        <p><?=$result['overview']?></p>
                        <?php if($_SESSION['user']->isContributor($stuff)):?>
                            <form action="./add_movie.php?id=<?=$result['id']?>" method="post">
                                <input type="hidden" name="<?=$_GET['id']?>" value="delete">
                                <button type="submit">Supprimer</button>
                            </form>
                        <?php endif?>
                    </div>
                </section>
            </a>
            <?php
			}
            ?>
        </div>
  
	</main>

<?php
        }else{
        ?>

        <main>
            <h1>Cet album est privé</h1>
            <p><i>Sa consultation est réservée aux contributeurs</i></p>
            <a href="./profile.php?id=<?=$stuff['contributor'][0]['id']?>">Retour</a>
        </main>
        
<?php
        }
	}else{
        echo '<h1>Pas d\'album trouvé</h1>';
	}
}else{
	header('location:./');
}

require_once './template/footer.php';
?>