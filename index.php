<?php

?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Document</title>
</head>
<body>
<pre>
    <?php


	$url_name = 'https://api.themoviedb.org/3/movie/1666?api_key=d3151e4e15cfce47f5840fd3c57988df';


	$ch_session = curl_init();

	curl_setopt($ch_session, CURLOPT_RETURNTRANSFER, 1);

	curl_setopt($ch_session, CURLOPT_URL, $url_name);

	$result_url = curl_exec($ch_session);
    
    $fight_club = json_decode($result_url, true);
    
    echo '<img src="https://image.tmdb.org/t/p/w500'.$fight_club['poster_path'].'">';
    echo '<h1>'.$fight_club['title'].'</h1>';
    
    print_r($fight_club);


	?>
</pre>
</body>
</html>
