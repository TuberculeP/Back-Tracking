<?php
$page_title = 'Rechercher';
require_once './template/imports.php';
if(!isset($_SESSION['user'])){
	header('location:./login.php');
}
require_once 'template/header.php';
$link = "";
$url_name = "";
$stuff = $_SESSION['user']->getStuff();

if($_GET){
	if (isset($_GET['query'])){
        if($_GET['query']===''){
            header('location:./search.php?discover=trending');
        }
        $link = "./search.php?query=".str_replace(' ', '+',$_GET['query']);
		$url_name = 'search/movie?query='.str_replace(' ', '+',$_GET['query']);
	}elseif(isset($_GET['discover'])){
		$link = "./search.php?discover=".$_GET['discover'];
        $url_name = $_GET['discover']==='trending'
            ?'trending/movie/week?'
            :'discover/movie?sort_by=primary_release_date.desc@primary_release_date.lte='.date('Y-m-d');
    }elseif(isset($_GET['genre'])){
		$link = "./search.php?genre=".$_GET['genre'];
		$url_name = 'discover/movie'
            .'@with_genres='.$_GET['genre'];
		if(isset($_GET['sort'])){
			if($_GET['sort']==='title'){
				$sort_param = 'original_title.asc';
			}elseif($_GET['sort']==='vote_average'){
				$sort_param = 'vote_average.desc';
			}else{
				$sort_param = 'popularity.desc';
				
			}
			$url_name .= '@sort_by='.$sort_param;
        }
    }
    if(isset($stuff['want_adult']) && $stuff['want_adult']===1){
        $url_name .= '@include_adult=true';
    }
}
?>
<main class=" bg-white w-full h-full pt-8">
  <div class="w-11/12 mx-auto h-full">
    <?php
    if($_GET){
        if(isset($_GET['query'])){
            ?>
            <h1><span class="results"></span> résultats pour : "<?=htmlspecialchars($_GET['query'])?>"</h1>
            <?php
        }elseif (isset($_GET['discover'])){
            ?>
            <h1 class="titre uppercase text-rouge my-8 pt-8 font-bold text-2xl">Les films les plus <?php echo $_GET['discover']==='trending'?  'tendances': 'récents'; ?>
            </h1>
            <?php } ?>
            
            <hr>
        
        <?php if(isset($_GET['genre']) || isset($_GET['query'])):?>
        <div class="sort ">
            <h3>Trier par :</h3>
            <a class="underline underline-offset-4"" href='<?=htmlspecialchars($link)?>&sort=title'>Nom</a>
            <a class="underline underline-offset-4"" href='<?=htmlspecialchars($link)?>&sort=vote_average'>Note</a>
            <a class="underline underline-offset-4"" href='<?=htmlspecialchars($link)?>&sort=popularity'>Popularité</a>
            <span>
                <label for="viewed">Masquer les films visionnés</label>
                <input type="checkbox" id="viewed">
            </span>
            <?php
			if(isset($stuff['want_adult']) && $stuff['want_adult']===1){
				?>
                <span>
                    <label for="oleole">Type : </label>
                <select name="oleole" id="oleole">
                    <option value="2">Tout</option>
                    <option value="1">Uniquement +18</option>
                    <option value="0">Sans +18</option>
                </select>
                </span>
                <?php
			}
            ?>
            
            <script>
                let noSeen = document.querySelector('#viewed').checked;
                document.querySelector('#viewed').addEventListener('change', function(){
                    noSeen = this.checked;
                    maskMaskable()
                })

				<?php
				if(isset($stuff["want_adult"]) && $stuff['want_adult']===1){
				?>
                
                let boules = document.querySelector('#oleole').value;
                document.querySelector('#oleole').addEventListener('change', function(){
                    boules = parseInt(this.value);
                    maskMaskable()
                })
                
				<?php
				}
				?>
            
            </script>
        </div>
        <?php endif;?>
        
        <div class="movie-container grid grid-cols-2 lg:grid-cols-6 gap-4">
              </div>
            </div>
				<?php
    }
    ?>
  </div>
</main>

<?php if(isset($_GET['query']) && $_GET['query']==="flip"):?>
    <script>
        document.body.style.transition = "all 1s";
        setTimeout(function() {
            document.body.style.transform = "rotateZ(360deg)";
        }, 1000)
    </script>
<?php endif;?>

<?php if(isset($_GET['query']) && $_GET['query']==="mirror"):?>
    <script>
        document.body.style.transition = "all 1s";
        setTimeout(function() {
            document.body.style.transform = "rotateY(180deg)";
        }, 1000)
    </script>
<?php endif;?>
<?php if(isset($_GET['query']) && $_GET['query']==="dance"):?>
    <style>
        @keyframes dance {
            0%{
                transform: scale(1);
            }
            50%{
                transform: scale(1.05);
            }
            100%{
                transform: scale(1);
            }
        }
    </style>
    <script>
        document.body.style.transition = "all 1s";
        setTimeout(function() {
            document.querySelectorAll('.result').forEach(function(movie, index){
                setTimeout(function(){
                    movie.style.animation = "dance 1s infinite";
                }, 100*index)
            })
        }, 1000)
    </script>
<?php endif;?>

    <script>
        function maskMaskable(){
             fetch('./api/view?user=<?=$_SESSION['id']?>').then(response => response.json())
                 .then(data => {
                     document.querySelectorAll('.movie-container>div').forEach(movie => {
                         if(boules === 0){
                             if(movie.querySelector('input.is_adult').value === 'true'){
                                 if(!movie.classList.contains('adult_hidden')){
                                     movie.classList.add('adult_hidden')
                                 }
                             }else{
                                 if(movie.classList.contains('adult_hidden')){
                                     movie.classList.remove('adult_hidden')
                                 }
                             }
                         }else if(boules === 1){
                             if(movie.querySelector('input.is_adult').value === 'false'){
                                 if(!movie.classList.contains('adult_hidden')){
                                     movie.classList.add('adult_hidden')
                                 }
                             }else{
                                 if(movie.classList.contains('adult_hidden')){
                                     movie.classList.remove('adult_hidden')
                                 }
                             }
                         }else{
                             if(movie.classList.contains('adult_hidden')){
                                 movie.classList.remove('adult_hidden')
                             }
                         }
                         
                         //masquer des trucs visionnés
                         if(noSeen){
                             if(data.includes(parseInt(movie.querySelector('input.movie_id').value))){
                                if(!movie.classList.contains('seen_hidden')){
                                    movie.classList.add('seen_hidden')
                                }
                             }
                         }
                         //démasquer des trucs visionnés
                         else{
                             if(data.includes(parseInt(movie.querySelector('input.movie_id').value))){
                                 if(movie.classList.contains('seen_hidden')){
                                     movie.classList.remove('seen_hidden')
                                 }
                             }
                         }
                     })
                 })
        }
        
        function getMovies(page){
            if(document.querySelector('#more_movies') !== null){
                document.querySelector('#more_movies').remove();
            }
            const container = document.querySelector('.movie-container');
            let url = './api/tmdb?q=<?=$url_name?>@page='+page;
            console.log(url)
            fetch(url).then(response => response.json()).then(data =>{
                let movies = data['results'];
                let amount = data['total_results'];
                if(data['total_results'] >=10000){
                    amount += "+";
                }
                if(document.querySelector('.results')!==null){
                    document.querySelector(".results").innerHTML = amount;
                }
				<?php
				if(isset($_GET['query'])):
				if(isset($_GET['sort'])):
				?>
                let sort_param = '<?=htmlspecialchars($_GET['sort'])?>';
                if(movies[0].hasOwnProperty(sort_param)){
                    movies.sort((a, b) => (a[sort_param] < b[sort_param])? 1: -1)
                    if(sort_param === "title") movies.reverse();
                }
		
				<?php endif; endif;?>

                movies.forEach(movie => {
                    let div = document.createElement('div');
                    div.innerHTML = `<a href="./movie.php?id=`+movie['id']+` " class="result">
                <section class="z-10 relative result lg:h-[450px] h-[380px] rounded-xl shadow-lg mb-6">
                  <img class="rounded-t-lg h-[40vh] border-b border-gris w-full" src="./assets/img/blank_movie.jpeg"
                         alt="no poster for this movie">
                        <div>
                          <h2 class="w-11/12 mx-auto mt-4 font-bold uppercase text-black text-base"></h2>
                          <div class="desc">
                            <p>Indice de Popularité : `+movie['popularity']+`</p>
                            <p>Note : `+movie['vote_average']+`/10</p>
                            <p>`+movie['overview']+`</p>
                          </div>
                        </div>
                        <input type="hidden" class="movie_id" value='`+movie['id']+`'>
                        <input type="hidden" class="is_adult" value='`+movie['adult']+`'>
                </section>
            </a>`
                    if(movie['poster_path']!==null){
                        div.querySelector('img').src = 'https://image.tmdb.org/t/p/w500'+movie['poster_path']
                        div.querySelector('img').alt = 'poster_for '+movie['id']
                    }
                    div.querySelector('h2').innerHTML = (movie['original_language']==='fr'
                        ?movie['original_title']
                        :movie['title']);
                    if(movie['release_date']!==undefined){
                        div.querySelector('h2').innerHTML += ' ('+movie['release_date'].substring(0,4)+')'
                    }

                    container.appendChild(div);
                })
                if(data['total_pages']>page){
                    let more = document.createElement('button');
                    more.innerHTML = 'Voir plus'
                    more.id="more_movies"
                    more.addEventListener('click', ()=>{
                        getMovies(page+1)
                    })
                    container.appendChild(more);
                }
            }).then(()=>{
                maskMaskable()
            })
            
        }
        
        function sortAdult(){
            document.querySelectorAll('.movie-container>div').forEach(movie => {
                let is_adult = movie.querySelector('input.is_adult').value;
                if(boules === 0){
                    if(is_adult === 'true'){
                        if(!movie.classList.contains('adult')){
                            movie.classList.add('adult')
                        }if(!movie.classList.contains('movie_hidden')){
                            movie.classList.add('movie_hidden')
                        }
                    }
                }else{
                    movie.classList.remove('movie_hidden')
                    if(boules === 1 && is_adult === 'false'){
                        movie.classList.add('movie_hidden')
                    }
                }
            })
        }
        getMovies(1)
        
    </script>
<?php
require_once './template/footer.php';
?>