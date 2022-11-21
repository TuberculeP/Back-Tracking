<?php
$page_title = 'IIMovies';
require_once './template/header.php';
if(!isset($_SESSION['user'])){
	header('location:./login.php');
}

$url_name = 'https://api.themoviedb.org/3/movie/' . $_GET['id'] . '?api_key=d3151e4e15cfce47f5840fd3c57988df&language=fr';
$ch_session = curl_init();
curl_setopt($ch_session, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch_session, CURLOPT_URL, $url_name);
$result_url = curl_exec($ch_session);
$movie = json_decode($result_url, true);

//on va s'amuser a convertir un peu tout comme on peut
$genres = "";
foreach ($movie['genres'] as $genre){
    $genres .= $genre['name']." ";
}

$relArr = explode("-", $movie['release_date']);
$releaseFormat = $relArr[2].'/'.$relArr[1].'/'.$relArr[0];

function clean($string) {
	$string = str_replace(' ', '', $string);
	$string = str_replace('-', '', $string);
 
	return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
}

?>
	<main>
		<div class="movie">
            <img src="https://image.tmdb.org/t/p/w500<?=$movie['poster_path']?>" alt="poster_for <?=$movie['id']?>">
            <section>
                <h2><?=$movie['title']?></h2>
                <?php
                if(clean($movie['title'])!==clean($movie['original_title'])){
                    echo '<h3>Titre original : '.$movie['original_title'].'</h3>';
                }
                ?>
                <p><i><?=$movie['tagline']?></i></p>
                <p>Genre(s) : <?=$genres?></p>
                <a href="<?=$movie['homepage']?>"><?=$movie['homepage']?></a>
                <p>Date de sortie : <?=$releaseFormat?></p>
                <p>Budget :
                    <?=$movie['budget']>0
                        ?number_format($movie['budget'], 0, '', '.').' $'
                        :'Inconnu'
                    ?>
                </p>
                <p>Durée : <?=$movie['runtime']?> minutes</p>
                <p><?=$movie['overview']?></p>
                <button id="view">Noté comme visionné</button>
                <button id="album">Ajouter à un album</button>
                <p><?=$movie['id']?></p>
                <script>
                    document.querySelector("#album").addEventListener('click', function(){
                        document.querySelector('.modal-container').style.display = 'flex';
                    })
                </script>
                <div class="modal-container">
                    <form class="modal" method="post" action="add_movie.php?id=<?=$_GET['id']?>">
                        <input type="hidden" name="form_toggler">
                        <?php
                        $db = new Connection();
                        foreach($db->getAlbums($_SESSION['id']) as $album):
                        ?>
                            <label for="<?=$album['id']?>"><?=$album['name']?></label>
                            <input type="checkbox" id="<?=$album['id']?>" name="<?=$album['id']?>"
                                <?php
                                if($db->movieInAlbum($movie['id'], $album['id'])){
                                    echo ' checked';
                                }
                                ?>
                            >
                        <?php endforeach;?>
                        <button type="submit">Terminé</button>
                    </form>
                </div>
            </section>
		</div>
		<div>
			<pre>
				<?php
				echo '<p>';
				print_r($movie);
				echo '</p>';
				
				?>
			</pre>
		</div>
	</main>
<?php
require_once './template/footer.php';
?>

