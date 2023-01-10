<?php
$page_title = 'IIMovies';
require_once 'template/header.php';
if(!isset($_SESSION['user'])) {
	header('location:./login.php');
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
	
	<main class=" bg-white w-full h-full pt-8 profiles">
    <div class="w-11/12 mx-auto h-full">
		<h1 class=" uppercase font-bold lg:mt-0 text-2xl lg:text-2xl"><?=htmlspecialchars($user->pseudo)?></h1>
        
        <div class="description mt-2 mb-4">
          <h3 class=" uppercase font-bold lg:mt-0 text-2xl lg:text-xl"><?=htmlspecialchars($user->first_name)." ".htmlspecialchars($user->last_name)?></h3>
          <p><?=isset($stuff['description'])?htmlspecialchars($stuff['description']):''?></p>
        </div>
        
        <?php if($_SESSION['id'] === (int)$_GET['id']):?>
            <?php
        
            ?>
        <form class="preferences bg-bleu text-white px-7 py-2 rounded-lg lg:w-1/3 w-auto" method="post">
            <input type="hidden" name="preferences">
            <h4 class="mb-4 mt-2">Préférences :</h4>
            <hr>
            <div class="flex flex-col">
              <label class="mt-4" for="desc">Description</label>
              <textarea
                      class="focus:outline-none rounded-md my-2 w-2/3"
                      name="description"
                      id="desc"><?=isset($stuff['description'])?htmlspecialchars($stuff['description']):''?></textarea>
              <div class=" flex flex-row items-center lg:w-1/2 w-8/12 justify-between mb-4">
                <label for="want_adult">Voir les films +18 ans</label>
                <input class="w-4 h-4" type="checkbox" name="want_adult" id="want_adult"
                  <?=(isset($stuff['want_adult']) && $stuff['want_adult']===1)?'checked':''?>>
              </div>
            </div>
            <hr>
            <button class="mt-4 mb-2 bg-white text-bleu border-2 border-bleu px-4 py-1 rounded-lg" type="submit">
                Envoyer
            </button>
        </form>
        <?php endif;?>
        
        <div class="flex flex-col my-4">
            <h2 class=" uppercase font-bold lg:mt-0 text-2xl lg:text-xl">Albums</h2>

          <div class="seen flex snap-x snap-mandatory h-max w-full mx:auto overflow-scroll overflow-y-hidden justify-between pb-12">

            <div class="album-container mr-8 snap-start shrink-0 flex flex-row text-center my-6">
				      <?php foreach ($albums as $album):
                    if($album->is_public || $_SESSION['user']->isContributor($album->getStuff())):?>
                    <a class="result w-[50vh] shadow-lg rounded-lg bg-white mr-8" href="album.php?id=<?=htmlspecialchars($album->id)?>">
                        <section class="result h-9/12 relative w-full">
                            <img class="rounded-t-lg w-full z-10" src='<?=$album->getThumbnail()!='https://image.tmdb.org/t/p/w500'
                                ?$album->getThumbnail()
                                :'https://cdn.pixabay.com/photo/2012/04/15/18/57/dvd-34919_960_720.png'?>'
                                 alt='<?=htmlspecialchars($album->name)?>'>
                            <div class=" text-left w-11/12 mx-auto pb-4">
                                <h3 class="my-4 font-bold uppercase text-black text-base"><?=htmlspecialchars($album->name)?></h3>
                                <ul class="text-bleu">
                                    <li>Vues : <?=htmlspecialchars($album->view)?></li>
                                    <li>Likes : <?=htmlspecialchars($album->like)?></li>
                                </ul>

                            </div>
                        </section>
                    </a>
				      <?php  endif; endforeach; ?>
            </div>
          </div>
        </div>
        <div>
            <h2 class=" uppercase font-bold lg:mt-0 text-2xl lg:text-xl">Likes</h2>

          <div class="seen flex snap-x snap-mandatory h-max w-full mx:auto overflow-scroll overflow-y-hidden justify-between pb-12">

            <div class="album-container mr-8 snap-start shrink-0 flex flex-row text-center my-6">
              <?php $albums = Album::getLiked($_SESSION['user']->getID());
                      foreach ($albums as $album):
                if($album->is_public || $_SESSION['user']->isContributor($album->getStuff())):?>
                  <a class="result w-[50vh] shadow-lg rounded-lg bg-white mr-8" href="album.php?id=<?=htmlspecialchars($album->id)?>">
                    <section class="result h-9/12 relative w-full">
                        <img class="rounded-t-lg w-full z-10" src='<?=$album->getThumbnail()!='https://image.tmdb.org/t/p/w500'
                      ?$album->getThumbnail()
                      :'https://cdn.pixabay.com/photo/2012/04/15/18/57/dvd-34919_960_720.png'?>'
                      alt='<?=htmlspecialchars($album->name)?>'>
                        <div class=" text-left w-11/12 mx-auto pb-4">
                            <h3 class="my-4 font-bold uppercase text-black text-base"><?=htmlspecialchars($album->name)?></h3>
                            <ul class="text-bleu">
                                <li>Vues : <?=htmlspecialchars($album->view)?></li>
                                <li>Likes : <?=htmlspecialchars($album->like)?></li>
                            </ul>

                        </div>
                    </section>
                  </a>
                <?php  endif; endforeach; ?>
              </div>
            </div>
        </div>
    </div>
        
	</main>

<?php
require_once './template/footer.php';
?>