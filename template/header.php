<?php
    if(isset($back)){
		require_once '../classes/connection.php';
		require_once '../classes/user.php';
		require_once '../classes/album.php';
    }else{
		require_once './classes/connection.php';
		require_once './classes/user.php';
		require_once './classes/album.php';
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
    <title><?=$page_title?></title>
    <link href="../src/input.css" rel="stylesheet">
    <link href="../build/css/style.css" rel="stylesheet">
    <link rel="icon" href="/assets/img/IIMovies_Icon.png" type="image/png">
</head>
<body>

<header class="bg-bleu flex flex-row w-full items-center h-[96px] justify-between">
  <nav class="w-11/12 flex flex-row mx-auto justify-between">
    <div class="logo hidden lg:flex title w-[170px]">
        <a href="/">
            <img src="/assets/img/IIMovies_LogoText_blanc.png" alt="IIMovies">
        </a>
    </div>
    
    <?php if($_SESSION && isset($_SESSION['user'])):?>

    <div class="search flex flex-row items-center bg-bleu">

      <div class="flex lg:relative lg:flex-row lg:w-auto left-0 bg-bleu pt-12 lg:pt-0 lg:pb-0 pb-8 lg:h-auto h-[35vh] top-0 absolute z-30 flex-col-reverse lg:justify-between">
        <form action="/search.php">
          <div class="form ml-8 lg:ml-0">
            <input class="bg-transparent lg:w-auto w-9/12 text-white border border-gris p-1 px-2 rounded focus:outline-none" type="text" name="query" id="search"
              placeholder="Rechercher..." autocomplete="off">
            <button class="lg:ml-2 cursor-pointer rounded py-1 px-2 bg-white" type="submit">Go</button>
          </div>
          <div class=" z-50 search-modal hidden absolute bg-bleu p-4 rounded-lg text-white">
              <p class="resultMovie text-white"></p>
              <hr>
              <ul class="resultMovie text-white"></ul>
              <hr>
              <p class="resultUser text-white"></p>
              <hr>
              <ul class="resultUser text-white"></ul>
          </div>
        </form>
        <div class="lg:items-center items-start flex lg:flex-row flex-col ml-8 justify-between w-44">
          <a href="/search.php?discover=trending" class="button mb-4 lg:mb-0 text-white">
              Tendances
          </a>
          <a href="/search.php?discover=new" class="button mb-4 lg:mb-0 text-white">
              Nouveauté
          </a>
        </div>
      </div>
    </div>
    
    <div class="profile items-center flex relative">
      <div class="relative inline-block text-left">
        <div>
          <button type="button" class="session inline-flex w-full justify-center rounded-md border bg-white text-bleu px-4 py-2 text-sm font-medium " id="menu-button" aria-expanded="true" aria-haspopup="true">
            <h3><?=$_SESSION['user']->pseudo?></h3>
            
            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
        <div class="menu hidden absolute top-10 right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
          <div class="py-1" role="none">

            <a class="text-gray-700 block px-4 py-2 text-sm" href="/disconnect.php" role="menuitem" tabindex="-1" id="menu-item-1">Se déconnecter</a>
            <a class="text-gray-700 block px-4 py-2 text-sm" href="/profile.php" role="menuitem" tabindex="-1" id="menu-item-1">Mon profil</a>
          </div>
        </div>
      </div>
    </div>
    
    <?php endif;?>
  </nav>
</header>


<script>
  document.querySelector('.session').addEventListener('click', function(){
      document.querySelector('.menu').classList.toggle('hidden');
  });

</script>
