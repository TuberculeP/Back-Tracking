<?php
    require_once './classes/connection.php';
    require_once './classes/user.php';
    session_start();
?>

<!--  $page_title doit être référencé avant tout require ou require_once de cette page --->


<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="/assets/img/IIMovies_Icon.png" type="image/png">
    <title><?=$page_title?></title>
</head>
<body>

<header>
    <div class="logo title">
        <a href="./">
            <img src="/assets/img/IIMovies_LogoText.png" alt="IIMovies">
        </a>
    </div>
    
    <?php if($_SESSION && isset($_SESSION['user'])):?>

        <div class="search">
            <div>
                <a href="/search.php?filter=popular">
                    Les plus populaires
                </a>
                <a href="/search.php?filter=recent">
                    Les plus récents
                </a>
                <form action="/search.php">
                    <div class="form">
                        <input type="text" name="query" id="search" placeholder="Rechercher un film...">
                        <button type="submit">Go</button>
                    </div>
                    <div class="search-modal hidden">
                        <p class="result"></p>
                        <hr>
                        <ul></ul>
                    </div>
                </form>
            </div>
        </div>
    
    <div class="profile">
        <h3><?=$_SESSION['user']->pseudo?></h3>
        <a href="disconnect.php">Se déconnecter</a>
    </div>
    
    <?php endif;?>
</header>