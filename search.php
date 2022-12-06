<?php
$page_title = 'Rechercher';
require_once './template/header.php';
if(!isset($_SESSION['user'])){
	header('location:./login.php');
}
$link = "";
$url_name = "";

if($_GET){
	if (isset($_GET['query'])){
        if($_GET['query']===''){
            header('location:./search.php?discover=trending');
        }
        $link = "./search.php?query=".str_replace(' ', '+',$_GET['query']);
		$url_name = 'https://api.themoviedb.org/3/search/movie?query='
            .str_replace(' ', '+',$_GET['query'])
            .'&api_key=d3151e4e15cfce47f5840fd3c57988df&language=fr';
	}elseif(isset($_GET['discover'])){
		$link = "./search.php?discover=".$_GET['discover'];
        $req_type = $_GET['discover']==='trending'
            ?'trending/movie/week?'
            :'discover/movie?sort_by=primary_release_date.desc&primary_release_date.lte='.date('Y-m-d').'&';
		$url_name = 'http://api.themoviedb.org/3/' .$req_type .'api_key=d3151e4e15cfce47f5840fd3c57988df&language=fr';
    }elseif(isset($_GET['genre'])){
		$link = "./search.php?genre=".$_GET['genre'];
		$url_name = 'http://api.themoviedb.org/3/discover/movie?api_key=d3151e4e15cfce47f5840fd3c57988df&language=fr'
            .'&with_genres='.$_GET['genre'];
    }
    if($_SESSION['user']->getStuff()['want_adult']===1){
        $url_name .= '&include_adult=true';
    }
}
?>
<main>
    <?php
    if($_GET){
        if(isset($_GET['query'])){
            ?>
            <h1><span class="results"></span> résultats pour : "<?=$_GET['query']?>"</h1>
            <?php
        }elseif (isset($_GET['discover'])){
            ?>
            <h1>Les films les plus <?php echo $_GET['discover']==='trending'?  'tendances': 'récents'; ?>
            </h1>
            <?php } ?>
            
            <hr>
        <div class="sort">
            <h3>Trier par :</h3>
            <a href='<?=$link?>&sort=title'>Nom</a>
            <a href='<?=$link?>&sort=vote_average'>Note</a>
            <a href='<?=$link?>&sort=popularity'>Popularité</a>
            <label for="viewed">Masquer les films visionnés</label>
            <input type="checkbox" id="viewed">
            <script>
                let noSeen = false
                document.querySelector('#viewed').addEventListener('change', function(){
                    if(this.checked){
                        noSeen = true;
                        removeSeen();
                    }else{
                        document.querySelector('.movie-container').remove()
                        let container = document.createElement('div');
                        container.classList.add('movie-container')
                        document.querySelector('main').appendChild(container);
                        getMovies(1, false);
                        noSeen = false;
                    }
                })
                
            </script>
        </div>
            <div class="movie-container"></div>
				<?php
    
    }
    
    ?>
</main>
    <script>
        function getMovies(page, no_seen){
            console.log(page, no_seen);
            if(document.querySelector('#more_movies') !== null){
                document.querySelector('#more_movies').remove();
            }
            const container = document.querySelector('.movie-container');
            fetch("<?=$url_name?>&page="+page).then(response => response.json()).then(data =>{
                let movies = data['results'];
                let amount = data['total_results'];
                if(data['total_results'] >=10000){
                    amount += "+";
                }
                if(document.querySelector('.results')!==null){
                    document.querySelector(".results").innerHTML = amount;
                }
				<?php
				if((isset($_GET['query']) || isset($_GET['discover']) || isset($_GET['genre']))):
				if(isset($_GET['sort'])):
				?>
                let sort_param = '<?=$_GET['sort']?>';
                if(movies[0].hasOwnProperty(sort_param)){
                    movies.sort((a, b) => (a[sort_param] < b[sort_param])? 1: -1)
                    if(sort_param === "title") movies.reverse();
                }
		
				<?php endif; endif;?>

                movies.forEach(movie => {
                    let div = document.createElement('div');
                    div.innerHTML = `<a href="./movie.php?id=`+movie['id']+` " class="result">
                <section class="result">
                    <img src="./assets/img/blank_movie.jpeg"
                         alt="no poster for this movie">
                        <div>
                            <h2></h2>
                            <p>Indice de Popularité : `+movie['popularity']+`</p>
                            <p>Note : `+movie['vote_average']+`/10</p>
                            <p>`+movie['overview']+`</p>
                        </div>
                        <input type="hidden" name="id" value='`+movie['id']+`'>
                </section>
            </a>`
                    if(movie['poster_path']!==null){
                        div.querySelector('img').src = 'https://image.tmdb.org/t/p/w500'+movie['poster_path']
                        div.querySelector('img').alt = 'poster_for '+movie['id']
                    }
                    div.querySelector('h2').innerHTML = (movie['original_language']==='fr'
                        ?movie['original_title']
                        :movie['title']) + ' ('+movie['release_date'].split('-')[0]+')'

                    container.appendChild(div);
                })
                if(data['total_pages']>page){
                    let more = document.createElement('button');
                    more.innerHTML = 'Voir plus'
                    more.id="more_movies"
                    more.addEventListener('click', ()=>{
                        getMovies(page+1, noSeen)
                    })
                    container.appendChild(more);
                }
            }).then(()=>{
                if(no_seen){
                    removeSeen();
                }
            })
            
        }
        function removeSeen(){
            fetch('./api/view?user=<?=$_SESSION['id']?>').then(response => response.json())
                .then(data => {
                    document.querySelectorAll('.movie-container section').forEach(movie => {
                        let movie_id = movie.querySelector('input').value
                        if(data.includes(parseInt(movie_id))){
                            movie.remove()
                        }
                    })
                });
        }
        getMovies(1, noSeen)
        
    </script>
<?php
require_once './template/footer.php';
?>