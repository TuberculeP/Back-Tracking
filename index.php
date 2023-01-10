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
        <a href="./movie.php?id=<?=$result['id']?>" class="result w-[35vh] shadow-lg rounded-lg bg-white mr-8">
            <section class="result h-9/12 relative w-full">
                <img class="rounded-t-lg h-[50vh] border-b border-gris w-full" src="https://image.tmdb.org/t/p/w500<?=$result['poster_path']?>"
                     alt="poster_for <?=$result['id']?>">
                <div>
                    <h2 class="my-4 font-bold uppercase text-black text-base">
                      <?php
                      if($result['original_language'] === 'fr'){
                        echo $result['original_title'];
                      }else{
                        echo $result['title'];
                      }
                      echo ' ('.explode('-',$result['release_date'])[0].')'
                      ?>
                    </h2>
                    <div class="hidden text-left">
                      <p>Indice de Popularité : <?=$result['popularity']?></p>
                      <p>Note : <?=$result['vote_average']?>/10</p>
                      <p><?=$result['overview']?></p>
                    </div>
                </div>
            </section>
        </a>
		<?php
	}
}
?>

    <main class=" bg-white w-full h-full pt-8">

      


	    <div class="w-11/12 mx-auto h-full">
        <h1 class=" uppercase font-bold mt-8 lg:mt-0 text-2xl lg:text-3xl">Bonjour <?=$_SESSION['user']->first_name?></h1>
        <h2 class=" uppercase font-bold mt-8 lg:mt-0 text-2xl lg:text-xl">Votre liste de souhaits :</h2>
        <div class="items-center flex flex-row w-full mx:auto justify-between z-10 relative">
          <div id="souhaits" class="absolute w-1/12 right-0 h-[81.7%] opacity-50 z-50 gradient -mt-16"></div>
          <div class="wishes flex snap-x snap-mandatory items-center h-max w-full mx:auto overflow-scroll overflow-y-hidden justify-between mb-16 relative">
            <div id="objet-souhaits" class="mr-8 snap-start shrink-0 flex flex-row text-center my-6">
              <?php
              $wanted = $_SESSION['user']->getWanted();
                    if(empty($wanted)){
                        echo '<p>Vous n\'avez pas encore de films dans votre liste de souhaits</p>';
                    }else{
                        printmovie($wanted);
                    }
              ?>
            </div>
          </div >
        </div>


        <h2 class=" uppercase font-bold lg:mt-0 text-2xl lg:text-xl">Vos films déjà vu :</h2>
        <div class="items-center flex flex-row w-full mx:auto justify-between z-10 relative">
          <div class="seen flex snap-x snap-mandatory h-max w-full mx:auto overflow-scroll overflow-y-hidden justify-between pb-12">
			      <div class="mr-8 snap-start shrink-0 flex flex-row text-center my-6">
              <?php
              $seen = $_SESSION['user']->getSeen();
                    if(empty($seen)){
                        echo '<p>Vous n\'avez pas encore de films visionnés</p>';
                    }else{
                        printmovie($seen);
                    }
              ?>
            </div>
          </div>
        </div >
      </div>
    </main>

<?php
require_once './template/footer.php';
?>

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>