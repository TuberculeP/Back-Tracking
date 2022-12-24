<?php
$page_title = 'IIMovies';
require_once 'template/header.php';
if(!isset($_SESSION['user'])){
	header('location:./login.php');
}


function printmovie($list){
	foreach ($list as $movie_id){
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
                </div>
            </section>
        </a>
		<?php
	}
}
?>

    <main>
        <h1>Bonjour <?=$_SESSION['user']->first_name?></h1>
        <h2>Votre liste de souhaits :</h2>
        <div class="wishes">
			<?php
			printmovie($_SESSION['user']->getWanted());
			?>
        </div>
        <h2>Vos films déjà vu :</h2>
        <div class="seen">
			<?php
			printmovie($_SESSION['user']->getSeen());
			?>
        </div>
    </main>

<?php
require_once './template/footer.php';
?>
