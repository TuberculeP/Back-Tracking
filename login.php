<?php
$page_title = "Se connecter";
require_once 'template/header.php';

if($_POST && isset($_POST['login'])){
    if($_POST['email'] === '' || $_POST['email'] === null || $_POST['password'] === '' || $_POST['password'] === null){
        var_dump($_POST);
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
<main>
    <form method="post">
        <h2>Login</h2>
        <input type="email" name="email" placeholder="E-Mail"
			<?php if($_GET && isset($_GET['email'])){
				echo ' value="'.htmlspecialchars($_GET['email']).'"';
			}?>>
        <input type="password" name="password" placeholder="password">
        <input type="submit" name="login" value="Login">
        <?php if($_GET && isset($_GET["error"])) {
            
            if($_GET['error'] === 'email'){
                
                echo '<p>Erreur : l\'email est inconnu. <a href="./register.php">Créer un compte ?</a></p>';
                
            }elseif($_GET['error'] === 'password'){
                echo '<p>Erreur : Mot de passe erroné</p>';
            }
            
        }?>
    </form>
    <a href="./register.php">Pas de compte ? S'inscrire</a>
</main>
</body>
</html>