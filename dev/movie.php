<!doctype html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<style>
		main {
			display: flex;
			width: 100%;
		}
		main div{
			max-width: 50%;
		}
	</style>
	<title>MOVIE - DEV</title>
</head>
<body>
	<main>
		<div>
			<?php
			
			
			$url_name = 'https://api.themoviedb.org/3/movie/'.$_GET['id'].'?api_key=d3151e4e15cfce47f5840fd3c57988df';
			
			
			$ch_session = curl_init();
			
			curl_setopt($ch_session, CURLOPT_RETURNTRANSFER, 1);
			
			curl_setopt($ch_session, CURLOPT_URL, $url_name);
			
			$result_url = curl_exec($ch_session);
			
			$fight_club = json_decode($result_url, true);
			
			echo '<h1>'.$fight_club['title'].'</h1>';
			echo '<img src="https://image.tmdb.org/t/p/w500'.$fight_club['poster_path'].'"><br>';
			
			
			
			
			?>
		</div>
		<div>
			<pre>
				<?php
				echo '<p>';
				print_r($fight_club);
				echo '</p>';
				
				?>
			</pre>
		</div>
	</main>
</body>
</html>

