<?php
$page_title = 'Rechercher';
require_once './template/header.php';
if(!isset($_SESSION['user'])){
	header('location:./login.php');
}
$link = "";

if($_GET){
	if (isset($_GET['query'])){
        if($_GET['query']===''){
            header('location:./search.php?discover=trending');
        }
        $link = "./search.php?query=".str_replace(' ', '+',$_GET['query']);
		$url_name = 'https://api.themoviedb.org/3/search/movie?query='
            .str_replace(' ', '+',$_GET['query'])
            .'&api_key=d3151e4e15cfce47f5840fd3c57988df&language=fr';
		$ch_session = curl_init();
		curl_setopt($ch_session, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch_session, CURLOPT_URL, $url_name);
		$result_url = json_decode(curl_exec($ch_session), true)['results'];
	}elseif(isset($_GET['discover'])){
		$link = "./search.php?discover=".$_GET['discover'];
        $req_type = $_GET['discover']==='trending'
            ?'trending/movie/week?'
            :'discover/movie?sort_by=primary_release_date.desc&primary_release_date.lte='.date('Y-m-d').'&';
		$url_name = 'http://api.themoviedb.org/3/' .$req_type .'api_key=d3151e4e15cfce47f5840fd3c57988df&language=fr';
        if(isset($_GET['genre'])){
			$url_name .= '&with_genres='.$_GET['genre'];
        }
		$ch_session = curl_init();
		curl_setopt($ch_session, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch_session, CURLOPT_URL, $url_name);
		$result_url = array_slice(
                json_decode(curl_exec($ch_session), true)['results'], 0, 20, true
        );
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
				if((isset($_GET['query']) || isset($_GET['discover'])) && isset($result_url)){
					if(isset($_GET['sort']) && array_key_exists($_GET['sort'], $result_url[0])){
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
    }
    ?>
</main>

<?php
require_once './template/footer.php';
?>