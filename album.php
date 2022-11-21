<?php
$page_title = 'IIMovies';
require_once 'template/header.php';
if(!isset($_SESSION['user'])) {
	header('location:./login.php');
}
if($_GET && isset($_GET['id'])){
	require_once 'classes/connection.php';
	$db = new Connection();
	$album = $db->getAlbumSpec($_GET['id']);
}else{
	header('./');
}

?>
	
	<main class="profile">
		
		<h1><?=$album['info']['name']?></h1>
        <h2>Par : <?php
            foreach ($album['contributor'] as $contributor){
            ?>
                <a href="profile.php?id=<?=$contributor['id']?>"><?=$contributor['pseudo']?></a>
                <?php
            }?></h2>
		
        <h2>Infos :</h2>
        <ul>
            <li>Vues : <?=$album['info']['view']?></li>
            <li>Likes : <?=$album['info']['like']?></li>
        </ul>
        
        <h1>Film(s) : </h1>
        
        <div>
            <?php
            foreach ($album['movie'] as $movie_id){
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
                        <form action="./add_movie.php?id=<?=$result['id']?>" method="post">
                            <input type="hidden" name="<?=$_GET['id']?>" value="delete">
                            <button type="submit">Supprimer</button>
                        </form>
                    </div>
                </section>
            </a>
            <?php
			}
            ?>
        </div>
  
	</main>

<?php
require_once './template/footer.php';
?>