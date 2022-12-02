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
            <h1>Résultats pour : "<?=$_GET['query']?>"</h1>
            <?php
        }elseif (isset($_GET['discover'])){
            ?>
            <h1>Les 20 films les plus <?php echo $_GET['discover']==='trending'?  'tendances': 'récents'; ?>
            </h1>
            <?php } ?>
            
            <hr>
        <div class="sort">
            <h3>Trier par :</h3>
            <a href='<?=$link?>&sort=title'>Nom</a>
            <a href='<?=$link?>&sort=vote_average'>Note</a>
            <a href='<?=$link?>&sort=popularity'>Popularité</a>
        </div>
            <div class="movie-container">
				<?php
                /*
				
					{
						$sort_parameter = array_column($result_url, $_GET['sort']);
						array_multisort($sort_parameter,
                            $_GET['sort']==='title'?SORT_ASC:SORT_DESC,
                            $result_url);
					}
					foreach ($result_url as $result){
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
				?>
            </div>
       
            <?php
        }
                */
    }
    
    ?>
</main>
    <script>
        fetch("<?=$url_name?>").then(response => response.json()).then(data =>{
            const container = document.querySelector('.movie-container');
            let movies = data['results'];

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
        })
    </script>
<?php
require_once './template/footer.php';
?>