<?php
require_once('include/init.php');

if(internauteConnecte()){
    header('location' .URL. 'profil.php');
    exit();

}
if(isset($_GET['action']) && $_GET['action'] == 'validate'){
$validate .= '<div class="alert alert-success alert-dismissible fade show mt-5" role="alert">
                    <strong>Félicitations !</strong> Votre inscription est réussie 😉, vous pouvez vous connecter !
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
}
if($_POST){
    $verifPseudo = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo ");
    $verifPseudo->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
    $verifPseudo->execute();

    if($verifPseudo->rowCount() == 1){
        $user = $verifPseudo->fetch(PDO::FETCH_ASSOC);
        if(password_verify($_POST['mdp'], $user['mdp'])){
            foreach($user as $key => $value){
                if($key != 'mdp'){
                    $_SESSION['membre'][$key] = $value;
                    $_SESSION['membre']['id_membre'] = $user['id_membre'];
                    $_SESSION['membre']['pseudo'] = $user['pseudo'];
                    $_SESSION['membre']['nom'] = $user['nom'];
                    $_SESSION['membre']['prenom'] = $user['prenom'];
                    $_SESSION['membre']['email'] = $user['email'];
                    $_SESSION['membre']['civilite'] = $user['civilite'];
                    $_SESSION['membre']['ville'] = $user['ville'];
                    $_SESSION['membre']['code_postal'] = $user['code_postal'];
                    $_SESSION['membre']['adresse'] = $user['adresse'];
                    $_SESSION['membre']['statut'] = $user['statut'];

                }
            }
        }else{
            $errreur .='<div class="alert alert-danger" role="alert">Ce mot de passe ne correspond pas !</div>';
        }
    }else{
        $erreur .= '<div class="alert alert-danger" role="alert">Erreur ce pseudo n\'existe pas, vérifiez ! Etes vous inscrit ? !</div>';
    }
}
require_once('include/header.php');
?>

<h2 class="text-center py-5"><div class="badge badge-dark text-wrap p-3">Connexion</div></h2>

<?= $validate ?>

<!-- $erreur .= '<div class="alert alert-danger" role="alert">Erreur format adresse !</div>'; -->

<!-- $validate .= '<div class="alert alert-success alert-dismissible fade show mt-5" role="alert">
                    <strong>Félicitations !</strong> Votre inscription est réussie 😉, vous pouvez vous connecter !
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>'; -->

<form class="my-5" method="POST" action="">

    <div class="col-md-4 offset-md-4 my-4">

    <label class="form-label" for="pseudo"><div class="badge badge-dark text-wrap">Pseudo</div></label>
    <input class="form-control btn btn-outline-success mb-4" type="text" name="pseudo" id="pseudo" placeholder="Votre pseudo">

    <label class="form-label" for="mdp"><div class="badge badge-dark text-wrap">Mot de passe</div></label>
    <input class="form-control btn btn-outline-success mb-4" type="password" name="mdp" id="mdp" placeholder="Votre mot de passe">

    <button type="submit" class="btn btn-lg btn-outline-success offset-md-4 my-2">Connexion</button>

    </div>
   
</form>

<?php require_once('include/footer.php');?>
