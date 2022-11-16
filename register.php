<?php
$page_title = "S'inscrire";
require_once 'template/header.php';

if($_POST && isset($_POST['register'])){
    
    //verification du formulaire -----------------------------------//
    $formCorrect = true;                                            //
    foreach ($_POST as $attribute => $value){                       //
        if( $value === null || $value === ''){                      //
            $formCorrect = 'empty';                                 //
            break;                                                  //
        }                                                           //
    }                                                               //
    if($_POST['password'] !== $_POST['password-confirm']){          //
        $formCorrect = 'password';                                  //
    }                                                               //
    //--------------------------------------------------------------//
	if($formCorrect === true){
		$user = new User($_POST);
        //est-il déjà dans la base ?
        if($user->alreadyEmail()){
            header('location:./register.php?error=emailTaken');
        }else if($user->alreadyPseudo()){
            header('location:./register.php?error=pseudoTaken');
        }else{
			var_dump((array)$user);
			$try = $user->register();
			if($try){
				header('location:./login.php?email='.$user->email);
			}else{
				header('location:register.php?error=SQL');
			}
        }
	}else{
        header('location:./register.php?error='.$formCorrect);
    }
    
}
?>

<main>
    <form method="post">
        <h2>Register</h2>
        <div>
            <input type="text" name="pseudo" placeholder="pseudo"><br>
            <input type="email" name="email" placeholder="email"><br>
            <input type="password" name="password" placeholder="password"><br>
            <input type="password" name="password-confirm" placeholder="confirm password">
        </div>
        <div>
            <input type="text" name="first_name" placeholder="first name"><br>
            <input type="text" name="last_name" placeholder="last name"><br>
            <input type="number" name="age" min="0" placeholder="age"><br>
        </div>
		<?php if($_GET && isset($_GET['error'])){
			echo '<p class="error">Erreur : ';
			echo match ($_GET['error']) {
				'empty' => 'Le formulaire comporte des sections non-remplies.',
				'password' => 'Les mots de passe ne correspondent pas.',
				'emailTaken' => "L'email est déjà utilisé. <a href='./login.php'>Déjà inscrit ?</a>",
				'pseudoTaken' => "Le pseudo est déjà utilisé.",
				'SQL' => "Une erreur est survenue au niveau de la Base de Données.",
				default => 'Une erreur inattendue est survenue',
			};
			echo '</p>';
			
		}
		
		?>
        <input type="submit" name="register" value="Register">
    </form>
</main>

</body>
</html>
