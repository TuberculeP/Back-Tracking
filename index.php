<?php
$page_title = 'IIMovies';
require_once 'template/header.php';
if(!isset($_SESSION['user'])){
	header('location:./login.php');
}
?>

<main>
    <h1>Bonjour <?=$_SESSION['user']->first_name?></h1>
    <ul></ul>
</main>

<?php
require_once './template/footer.php';
?>
