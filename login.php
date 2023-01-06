<?php
$page_title = "Se connecter";
require_once 'template/header.php';

if($_POST && isset($_POST['login'])){
    if($_POST['email'] === '' || $_POST['email'] === null || $_POST['password'] === '' || $_POST['password'] === null){
		header('location:./login.php?error=empty');
    }else{
		$db = new Connection();
		$result = $db->getFromEmail($_POST['email']);
		if($result){
			if($result['password'] === hash('sha256',$_POST['password'].'p€@NÜt-_-BüTt€R')){
				$_SESSION['user'] = new User($result);
				$_SESSION['id'] = $result['id'];
			
			}else{
				header('location:./login.php?error=password');
			}
		}else{
			header('location:./login.php?error=email');
		}
    }
}
if(isset($_SESSION["user"])){
	header('location:./');
}
?>
<main class="bg-white h-screen pt-10 w-full">
    <form method="post" class="bg-bleu flex flex-col w-1/3 mx-auto rounded-2xl text-white p-4 justify-between h-80">
        <h2 class="titre text-3xl font-semibold mx-auto text-white">Login</h2>
        <input class="bg-transparent border border-gris rounded-sm px-2 py-2" type="email" name="email" placeholder="E-Mail"
			<?php if($_GET && isset($_GET['email'])){
				echo ' value="'.htmlspecialchars($_GET['email']).'"';
			}?>>
        <input class="bg-transparent border border-gris rounded-sm px-2 py-2" type="password" name="password" placeholder="password">
        <input class="text-bleu bg-white text-2xl uppercase font-bold rounded-lg mb-4 -mt-2 mx-auto px-4 py-1 cursor-pointer" type="submit" name="login" value="Login">
          <?php if($_GET && isset($_GET["error"])) {
              
              if($_GET['error'] === 'email'){
                ?> <p class="text-white"> <?php
                  echo 'Erreur : l\'email est inconnu. <a href="./register.php">Créer un compte ?</a>';
                ?> </p> <?php

              }elseif($_GET['error'] === 'password'){
                ?> <p class="text-white"> <?php
                  echo 'Erreur : Mot de passe erroné';
                  ?> </p> <?php
              }elseif($_GET['error'] === 'empty'){
				  ?> <p class="text-white"> <?php
					  echo 'Erreur : Veuillez remplir tous les champs';
					  ?> </p> <?php
			  }
            
        }?>
    </form>
    <div class="mx-auto w-2/12 flex mt-8 border-2 border-bleu rounded-lg py-2 px-1">
      <a class="mx-auto text-bleu  flex" href="./register.php">Pas de compte ? S'inscrire</a>
    </div>
</main>
</body>
</html>