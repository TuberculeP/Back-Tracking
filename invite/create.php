<?php
$back = true;
$page_title = "Invitation";
require_once '../template/imports.php';
$key = hash('sha256', date("Y-m-d H:i:s"));
require_once '../template/header.php';
if (($_POST && isset($_POST['album'])) || isset($_GET['test'])) {
    if(sizeof($_SESSION['user']->link_getAll($_POST['album']))===0){
		if(isset($_POST['album'])){
			$_SESSION['user']->link_generate($_POST['album'], $key);
		}
    }else{
        $key = $_SESSION['user']->link_getAll($_POST['album'])['key'];
    }
 ?>

<main class="invite">
	<h1>Le lien a été créé</h1>
    <h2></h2>
    <p>http://devlab.test/invite/?key=<?=htmlspecialchars($key)?></p>
    <button class="copy">Copier</button>
    <script>
        let copy = document.querySelector('main.invite p')
        document.querySelector('button.copy').addEventListener('click', function(){
            navigator.clipboard.writeText(copy.innerHTML);
            this.innerHTML = 'Copié !';
        })
    </script>
</main>
<?php }else{
	?>
	<main>
		<h1>Erreur de formulaire (ERROR CU1LL€R€)</h1>
	</main>
    
    <?php
}
require_once '../template/header.php';