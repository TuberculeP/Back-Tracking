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
            $album->view++;
    
?>
	
	<main class=" bg-white w-full h-full py-8 profile">
	   <div class="w-11/12 mx-auto h-full">
		
		<h1 class=" uppercase font-bold mt-8 mb-4 lg:mt-0 text-2xl lg:text-3xl"><?=htmlspecialchars($album->name)?></h1>
        <form method="post" class="lg:w-2/12 w-1/2 mb-4">
            <input type="hidden" name="delete_album">
            <button class="session inline-flex w-full justify-center rounded-md border-2 border-bleu bg-white text-bleu px-4 py-2 text-sm font-medium " type="submit">Supprimer l'album</button>
        </form>
        <h2 >Par : <?php
         foreach ($stuff['contributor'] as $contributor){
         ?>
               <a class="text-bleu" href="profile.php?id=<?=htmlspecialchars($contributor['id'])?>">
                  <?=htmlspecialchars($contributor['pseudo'])?>
               </a>
               <?php
         }?></h2>
      
         <h2>Infos :</h2>
         <div class="flex flex-row lg:w-3/12 w-auto justify-between items-center">
            <ul class="flex flex-row w-5/12 justify-between">
               <li>Vues : <?=htmlspecialchars($album->view)?></li>
               <li>Likes : <?=htmlspecialchars($album->like)?></li>
            </ul>
         </div>

         <?php if($album->is_public):?>
               <form class="lg:w-2/12 w-1/2 my-4" action="./album.php?id=<?=htmlspecialchars($album->id)?>" method="post">
                  <input type="hidden" name="liked" value="<?=$_SESSION['user']->hasLiked($album->id)?1:0?>">
                  <button class="session inline-flex w-full justify-center rounded-md bg-bleu text-white px-4 py-2 text-sm font-medium " type="submit">
                        <?=$_SESSION['user']->hasLiked($album->id)?'Supprimer des likes':'Liker'?>
                  </button>
               </form>
            <?php endif;?>
        
         <div class="flex flex-row mb-4">
            <h2>Inviter à contribuer :</h2>
            <form class="ml-4" action="./invite/create.php" method="post">
                  <input type="hidden" name="album" value="<?=htmlspecialchars($_GET['id'])?>">
                  <button class="underline underline-offset-4" type="submit">Générer un lien</button>
            </form>
         </div>
        
        <h1 class="mb-4 uppercase font-bold text-2xl lg:text-xl" >Film(s) : </h1>

         <div class="wishes flex snap-x snap-mandatory h-max w-full mx:auto overflow-scroll overflow-y-hidden justify-between  mb-16">
        
        <div class="mr-8 snap-start shrink-0 flex flex-row text-center my-6">
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
            <a class="result w-[35vh] shadow-lg rounded-lg bg-white mr-8" href="./movie.php?id=<?=$result['id']?>" class="result">
                <section class="result h-[70vh] relative w-full" class="result">
                    <img class="rounded-t-lg h-[50vh] border-b border-gris w-full" src="https://image.tmdb.org/t/p/w500<?=$result['poster_path']?>"
                         alt="poster_for <?=$result['id']?>">
                    <div class="flex flex-col relative justify-between h-max">
                      <h2 class="my-4 h-[5vh] font-bold uppercase text-black text-base">
                          <?php
                          if($result['original_language'] === 'fr'){
                            echo $result['original_title'];
                          }else{
                            echo $result['title'];
                          }
                          echo ' ('.explode('-',$result['release_date'])[0].')'
                          ?>
                      </h2>
                      <div class="flex flex-row w-11/12 mx-auto opacity-70 justify-between my-4">
                        <div class="h-2 w-2 bg-gris rounded-full"></div>
                        <div class="h-2 w-2 bg-gris rounded-full"></div>
                        <div class="h-2 w-2 bg-gris rounded-full"></div>
                        <div class="h-2 w-2 bg-gris rounded-full"></div>
                        <div class="h-2 w-2 bg-gris rounded-full"></div>
                        <div class="h-2 w-2 bg-gris rounded-full"></div>
                        <div class="h-2 w-2 bg-gris rounded-full"></div>
                        <div class="h-2 w-2 bg-gris rounded-full"></div>
                        <div class="h-2 w-2 bg-gris rounded-full"></div>
                        <div class="h-2 w-2 bg-gris rounded-full"></div>
                        <div class="h-2 w-2 bg-gris rounded-full"></div>
                      </div>
                      <div class="flex justify-center h-[5vh] items-center">
                        <?php if($_SESSION['user']->isContributor($stuff)):?>
                          <form action="./add_movie.php?id=<?=$result['id']?>" method="post">
                            <input type="hidden" name="<?=htmlspecialchars($_GET['id'])?>" value="delete">
                            <button class="underline underline-offset-4 uppercase hover:opacity-50" type="submit">Supprimer</button>
                          </form>
                        <?php endif?>
                      </div>
                    </div>
                </section>
            </a>
            <?php
			}
            ?>
        </div>
        </div>
      </div>
	</main>

<?php
        }else{
        ?>

        <main>
            <h1>Cet album est privé</h1>
            <p><i>Sa consultation est réservée aux contributeurs</i></p>
            <a href="./profile.php?id=<?=htmlspecialchars($stuff['contributor'][0]['id'])?>">Retour</a>
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
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>