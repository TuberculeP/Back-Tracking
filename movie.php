<?php
$page_title = 'IIMovies';
require_once './template/imports.php';
if(!isset($_SESSION['user'])){
	header('location:./login.php');
}
require_once './template/header.php';
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
    $genres .= '<a class="underline underline-offset-4" href="./search.php?genre='.$genre['id'].'">'.$genre['name']."</a> ";
}

$relArr = explode("-", $movie['release_date']);
$releaseFormat = $relArr[2].'/'.$relArr[1].'/'.$relArr[0];

function clean($string) {
	$string = str_replace(' ', '', $string);
	$string = str_replace('-', '', $string);
 
	return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
}
if($_POST){
    if(isset($_POST['see_movie'])){
		if(in_array($_GET['id'], $_SESSION["user"]->getSeen())){
			$_SESSION['user']->forgor($_POST['see_movie'], 'seen');
		}else{
			$_SESSION['user']->rember($_POST['see_movie'], 'seen');
		}
		header('location:./movie.php?id='.$_GET['id']);
    }elseif(isset($_POST['want_movie'])){
		if(in_array($_GET['id'], $_SESSION["user"]->getWanted())){
			$_SESSION['user']->forgor($_POST['want_movie'], 'wanted');
		}else{
			$_SESSION['user']->rember($_POST['want_movie'], 'wanted');
		}
		header('location:./movie.php?id='.$_GET['id']);
    }
}

?>

	<main class=" bg-white w-full h-full pt-8 relative pb-10">
    <div class="w-11/12 mx-auto h-full flex flex-col">
      <div class="movie flex lg:flex-row flex-col">
        <img class="h-[80vh] rounded-2xl" src="https://image.tmdb.org/t/p/w500<?=$movie['poster_path']?>" alt="poster_for <?=$movie['id']?>">
        <section class="flex flex-col lg:ml-8 justify-between lg:h-[50vh] h-full w-full">
            <h2 class=" uppercase font-bold mt-8 lg:mt-0 text-2xl lg:text-3xl"><?=$movie['title']?></h2>
            <?php
            if(clean($movie['title'])!==clean($movie['original_title'])){
              ?> <h3 class=" uppercase font-bold mt-8 lg:mt-0 text-2xl lg:text-xl"> <?php
                echo 'Titre original : '.$movie['original_title'].'';
                ?> </h3> <?php
            }
            ?>
            <p class="font-bold mt-8 lg:mt-0"><i><?=$movie['tagline']?></i></p>
            <p class="text-bleu">Genre(s) : <?=$genres?></p>
            <a class="text-gris" href="<?=$movie['homepage']?>"><?=$movie['homepage']?></a>
            <p>Date de sortie : <?=$releaseFormat?></p>
            <p>Budget :
                <?=$movie['budget']>0
                    ?number_format($movie['budget'], 0, '', '.').' $'
                    :'Inconnu'
                ?>
            </p>
            <p>Durée : <?=$movie['runtime']?> minutes</p>
            <p><?=$movie['overview']?></p>
            <div class="mt-2 flex lg:flex-row flex-col justify-between lg:w-10/12 w-12/12 lg:items-center text-center lg:text-left md:w-12/12">
              <form class="mt-4 mg:mt-0 bg-bleu text-white px-7 py-2 rounded-lg" method="post">
                  <input type="hidden" name="see_movie" value="<?=htmlspecialchars($_GET['id'])?>">
                  <button type="submit">
                      <?php
                          echo in_array($_GET['id'], $_SESSION["user"]->getSeen())?'Supprimer des':'Ajouter aux'
                      ?> visionnés
                  </button>
              </form>
              <form class="mt-4 mg:mt-0 bg-bleu text-white px-7 py-2 rounded-lg" method="post">
                <input type="hidden" name="want_movie" value="<?=htmlspecialchars($_GET['id'])?>">
                <button type="submit">
                <?php
                echo in_array($_GET['id'], $_SESSION["user"]->getWanted())?'Supprimer de':'Ajouter à'
                ?> la liste de souhaits
              </button>
            </form>
            <button class="mt-4 mg:mt-0 bg-white text-bleu border-2 border-bleu px-7 py-2 rounded-lg" id="album">Ajouter à un album</button>
            <!-- <p><?=$movie['id']?></p> -->
            
            <div class="hidden flex flex-col modal-container absolute bg-white bottom-10 text-bleu border-2 border-bleu rounded-lg p-1 px-2 justify-between lg:w-6/12">

                <ion-icon class="close z-50 plus absolute w-6 h-6 fill-black top-2 right-2 bg-gris rounded-full p-1 bg-opacity-50" name="close-outline"></ion-icon>

                <form class="p-1 px-2 modal" method="post" action="add_movie.php?id=<?=htmlspecialchars($_GET['id'])?>">
                    <h4 class="mb-4">Ajouter à une liste existante</h4>
                    <?php
                    $db = new Connection();
                    ?>
                    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 lg:items-center">
                      <?php
                      foreach(Album::all($_SESSION['id']) as $album):
                      ?>
                      <div class="mb-4">
                          <input class="mb-2" type="checkbox" id="<?=$album->id?>" name="<?=$album->id?>"
                          <?php
                          if($album->contains($movie['id'])){
                            echo ' checked';
                          }
                          ?>
                          >
                          <label for="<?=$album->id?>"><?=$album->name?></label>
                      </div>
                      
                      <?php endforeach;?>
                    </div>

                    <h4 class="mb-4">Nouvel album</h4>
                    <div class="mb-4">
                        <label for="new">Nom</label>
                        <input class="bg-transparent mx-4 text-gris border border-gris p-1 px-2 rounded" type="text" name="new_album" id="new">
                        <select name="is_public" id="privacy">
                            <option value="1">Publique</option>
                            <option value="0">Privé</option>
                        </select>
                    </div>
                    <button class="bg-bleu text-white px-7 py-2 rounded-lg" type="submit">Terminé</button>
                </form>
            </div>
            </div>



            <script>
              document.querySelector("#album").addEventListener('click', function(){
                  document.querySelector('.modal-container').classList.remove('hidden');
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
                document.querySelector('.close').addEventListener('click',function(){
                    document.querySelector('.modal-container').classList.add('hidden');
                })
            </script>
        </section>
      </div>
      <div>
        <!-- <pre>
          <?php
          echo '<p>';
          print_r($movie);
          echo '</p>';
          
          ?>
        </pre> -->
      </div>
    </div>
	</main>
<?php
require_once './template/footer.php';
?>

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>