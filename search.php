<?php
$page_title = 'Rechercher';
require_once './template/header.php';

if($_GET){
	if (isset($_GET['query'])){
		$url_name = 'https://api.themoviedb.org/3/search/movie?query='
            .str_replace(' ', '+',$_GET['query'])
            .'&api_key=d3151e4e15cfce47f5840fd3c57988df';
		$ch_session = curl_init();
		curl_setopt($ch_session, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch_session, CURLOPT_URL, $url_name);
		$result_url = json_decode(curl_exec($ch_session), true);
	}elseif(isset($_GET['discover'])){
        $req_type = $_GET['discover']==='popular'
            ?'trending/movie/week?'
            :'discover/movie?sort_by=primary_release_date.desc&';
		$url_name = 'http://api.themoviedb.org/3/' .$req_type .'api_key=d3151e4e15cfce47f5840fd3c57988df';
		$ch_session = curl_init();
		curl_setopt($ch_session, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch_session, CURLOPT_URL, $url_name);
		$result_url = array_slice(
                json_decode(curl_exec($ch_session), true)['results'], 0, 10, true
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
            <h1>Les films les plus <?php echo $_GET['discover']==='popular'?  'populaires cette semaine': 'récents'; ?>
            </h1>
            <?php } ?>
            
            <hr>
            <div class="movie-container">
				<?php
				if((isset($_GET['query']) || isset($_GET['discover'])) && isset($result_url)){
					foreach ($result_url as $result){
						?>

                        <section class="result">
                            <img src="https://image.tmdb.org/t/p/w500<?=$result['poster_path']?>" alt="poster_for <?=$result['id']?>">
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
                                <p><?=$result['overview']?></p>
                            </div>
                        </section>
				
                        
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