<?php
$page_title = 'IIMovies';
require_once './template/header.php';
if(!isset($_SESSION['user'])){
	header('location:./login.php');
}
$url_name = 'https://api.themoviedb.org/3/movie/'
    . $_GET['id']
    . '?api_key=d3151e4e15cfce47f5840fd3c57988df&language=fr';
$ch_session = curl_init();
curl_setopt($ch_session, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch_session, CURLOPT_URL, $url_name);
$result_url = curl_exec($ch_session);
$movie = json_decode($result_url, true);

//on va s'amuser a convertir un peu tout comme on peut
$genres = "";
foreach ($movie['genres'] as $genre){
    $genres .= '<a href="./search.php?genre='.$genre['id'].'">'.$genre['name']."</a> ";
}

$relArr = explode("-", $movie['release_date']);
$releaseFormat = $relArr[2].'/'.$relArr[1].'/'.$relArr[0];

function clean($string) {
	$string = str_replace(' ', '', $string);
	$string = str_replace('-', '', $string);
 
	return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
}

if($_POST && isset($_POST['see_movie'])){
    if(in_array($_GET['id'], $_SESSION["user"]->getSeen())){
		$_SESSION['user']->forgor($_POST['see_movie']);
	}else{
		$_SESSION['user']->rember($_POST['see_movie']);
	}
    header('location:./movie.php?id='.$_GET['id']);
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
                <form method="post">
                    <input type="hidden" name="see_movie" value="<?=$_GET['id']?>">
                    <button type="submit">
                        <?php
                            echo in_array($_GET['id'], $_SESSION["user"]->getSeen())?'Supprimer des':'Ajouter aux'
                        ?> visionnés
                    </button>
                </form>
                <button>Ajouter à un album</button>
                <p><?=$movie['id']?></p>
                
                <div class="modal-container">
                    <form class="modal" method="post" action="add_movie.php?id=<?=$_GET['id']?>">
                        <h4>Ajouter à une liste existante</h4>
                        <?php
                        $db = new Connection();
                        foreach(Album::all($_SESSION['id']) as $album):
                        ?>
                            <div>
                                <input type="checkbox" id="<?=$album->id?>" name="<?=$album->id?>"
									<?php
									if($album->contains($movie['id'])){
										echo ' checked';
									}
									?>
                                >
                                <label for="<?=$album->id?>"><?=$album->name?></label>
                            </div>
                            
                        <?php endforeach;?>
                        <h4>Nouvel album</h4>
                        <div>
                            <label for="new">Nom</label>
                            <input type="text" name="new_album" id="new">
                            <select name="is_public" id="privacy">
                                <option value="1">Publique</option>
                                <option value="0">Privé</option>
                            </select>
                        </div>
                        <button type="submit">Terminé</button>
                    </form>
                </div>
                <script>
                    document.querySelector("#album").addEventListener('click', function(){
                        document.querySelector('.modal-container').style.display = 'flex';
                    })
                    let modalClicked = false;
                    document.querySelector('form.modal').addEventListener('click',()=>{
                        modalClicked = true;
                    })
                    document.querySelector('.modal-container').addEventListener('click',function(){
                        if(!modalClicked){
                            this.style.display = 'none'
                        }
                        modalClicked = false;
                    })
                </script>
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

