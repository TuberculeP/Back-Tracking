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

<main class=" bg-white w-full lg:h-[88vh] h-full pb-20 pt-8 profiles">
    <form class="bg-bleu flex flex-col lg:w-1/3 w-11/12 mx-auto rounded-2xl text-white p-4 justify-between lg:h-[55vh] h-full" method="post">
        <h2 class="titre text-3xl font-semibold mx-auto text-white mb-4">Register</h2>
        <div class="flex lg:flex-row flex-col justify-between w-11/12 mx-auto">
            <div class="flex flex-col">
                <input class=" my-2 bg-transparent border border-gris rounded-sm px-2 py-2" type="text" name="pseudo" placeholder="pseudo">
                <input class=" my-2 bg-transparent border border-gris rounded-sm px-2 py-2" type="email" name="email" placeholder="email">
                <input class=" my-2 bg-transparent border border-gris rounded-sm px-2 py-2" type="password" name="password" placeholder="password">
                <input class=" my-2 bg-transparent border border-gris rounded-sm px-2 py-2" type="password" name="password-confirm" placeholder="confirm password">
            </div>
            <div class="flex flex-col">
                <input class=" my-2 bg-transparent border border-gris rounded-sm px-2 py-2" type="text" name="first_name" placeholder="first name">
                <input class=" my-2 bg-transparent border border-gris rounded-sm px-2 py-2" type="text" name="last_name" placeholder="last name">
                <input class=" my-2 bg-transparent border border-gris rounded-sm px-2 py-2" type="number" name="age" min="0" placeholder="age">
            </div>
        </div>
        <div class="w-11/12 mx-auto mt-2 mb-6">
            <?php if($_GET && isset($_GET['error'])){
                ?> <p class="text-white"> <?php
                    echo '<p class="error">Erreur : ';
                    ?> </p> <?php

                echo match (htmlspecialchars($_GET['error'])) {
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
        </div>
        <input class="text-bleu bg-white text-2xl uppercase font-bold rounded-lg mb-4 -mt-2 mx-auto px-4 py-1 cursor-pointer" type="submit" name="register" value="Register">
    </form>
</main>

</body>
</html>