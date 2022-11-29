<?php
    if(isset($back)){
		require_once '../classes/connection.php';
		require_once '../classes/user.php';
    }else{
		require_once './classes/connection.php';
		require_once './classes/user.php';
    }
    
    session_start();
    
    if(!isset($_SESSION['current'])){
		$_SESSION['current'] = $_SERVER['REQUEST_URI'];
    }
    if($_SERVER['REQUEST_URI'] !== '/favicon.ico'){
		$_SESSION['previous'] = $_SESSION['current'];
		$_SESSION['current'] = $_SERVER['REQUEST_URI'];
    }
?>

<!--  $page_title doit être référencé avant tout require ou require_once de cette page --->


<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/style.css">
    <link rel="icon" href="/assets/img/IIMovies_Icon.png" type="image/png">
    <title><?=$page_title?></title>
</head>
<body>

<header>
    <div class="logo title">
        <a href="/">
            <img src="/assets/img/IIMovies_LogoText.png" alt="IIMovies">
        </a>
    </div>
    
    <?php if($_SESSION && isset($_SESSION['user'])):?>

        <div class="search">
            <div>
                <form action="/search.php">
                    <div class="form">
                        <input type="text" name="query" id="search"
                               placeholder="Rechercher..." autocomplete="off">
                        <button type="submit">Go</button>
                    </div>
                    <div class="search-modal hidden">
                        <p class="resultMovie"></p>
                        <hr>
                        <ul class="resultMovie"></ul>
                        <hr>
                        <p class="resultUser"></p>
                        <hr>
                        <ul class="resultUser"></ul>
                    </div>
                </form>
                <a href="/search.php?discover=trending" class="button">
                    Tendances
                </a>
                <a href="/search.php?discover=new" class="button">
                    Nouveauté
                </a>
            </div>
        </div>
    
    <div class="profile">
        <a href="/profile.php"><h3><?=$_SESSION['user']->pseudo?></h3></a>
        <a href="/disconnect.php">Se déconnecter</a>
    </div>
    
    <?php endif;?>
</header>