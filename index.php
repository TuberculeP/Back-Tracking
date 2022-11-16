<?php
$page_title = 'Tartiflette';
require_once 'template/header.php';
if(!isset($_SESSION['user'])){
	header('location:./login.php');
}
?>

<main>
    <h1>Bonjour <?=$_SESSION['user']->first_name?></h1>
</main>

</body>
</html>
