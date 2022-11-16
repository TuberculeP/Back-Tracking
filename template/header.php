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
    <title><?=$page_title?></title>
</head>
<body>

<header>
    <div class="logo">
        <img src="https://assets.afcdn.com/recipe/20160401/38946_w1024h768c1cx2690cy1793.jpg" alt="site_logo">
    </div>
    <div class="title">
        <h1>Tartiflette</h1>
    </div>
    <?php if($_SESSION && isset($_SESSION['user'])):?>
    <div class="menu"></div>
    <div class="profile">
        <h3><?=$_SESSION['user']->pseudo?></h3>
        <a href="disconnect.php">Se déconnecter</a>
    </div>
    <?php endif;?>
</header>