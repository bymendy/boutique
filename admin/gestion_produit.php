<!-- todo list
- exclure toute personne qui n'est pas admin

-Les trois taches qui suivent ne concernent pas le champs photo (on le fera ensemble)
- Faire les contraintes pour chaque champs
- Faire la requete de modification et d'insertion
- Récupérer toutes les infos en BDD pour les afficher dans le formulaire en cas d'update

 -->

 <?php
require_once('../include/init.php');

if (!internauteConnecteAdmin()) {
    header('location:' . URL . 'connexion.php');
    exit();
}


if (isset($_GET['action'])) {

    if ($_POST) {

        if (!isset($_POST['reference']) || !preg_match('#^[a-zA-Z0-9-_.]{3,20}$#', $_POST['reference'])) {
            $erreur .= '<div class="alert alert-danger" role="alert"> La référence ne peut pas être vide !</div>';
        }

        if (!isset($_POST['categorie']) || iconv_strlen($_POST['categorie']) < 3 || iconv_strlen($_POST['categorie']) > 20) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format nom !</div>';
        }


        if (empty($erreur)) {
            // si dans l'URL action == update, on entame une procédure de modification
            if ($_GET['action'] == 'update') {
                $modifIuser = $pdo->prepare(" UPDATE membre SET id_membre = :id_membre , pseudo = :pseudo, nom = :nom, prenom = :prenom, email = :email, civilite = :civilite, ville = :ville, code_postal = :code_postal, adresse = :adresse WHERE id_membre = :id_membre ");
                $modifIuser->bindValue(':id_membre', $_POST['id_membre'], PDO::PARAM_INT);
                $modifIuser->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
                $modifIuser->bindValue(':nom', $_POST['nom'], PDO::PARAM_STR);
                $modifIuser->bindValue(':prenom', $_POST['prenom'], PDO::PARAM_STR);
                $modifIuser->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
                $modifIuser->bindValue(':civilite', $_POST['civilite'], PDO::PARAM_STR);
                $modifIuser->bindValue(':ville', $_POST['ville'], PDO::PARAM_STR);
                $modifIuser->bindValue(':code_postal', $_POST['code_postal'], PDO::PARAM_INT);
                $modifIuser->bindValue(':adresse', $_POST['adresse'], PDO::PARAM_STR);
                $modifIuser->execute();
            } else {
                // si on récupère autre chose que update (et donc add) on entame une procédure d'insertion en BDD
                $inscrireUser = $pdo->prepare(" INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, ville, code_postal, adresse) VALUES (:pseudo, :mdp, :nom, :prenom, :email, :civilite, :ville, :code_postal, :adresse) ");
                $inscrireUser->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
                $inscrireUser->bindValue(':mdp', $_POST['mdp'], PDO::PARAM_STR);
                $inscrireUser->bindValue(':nom', $_POST['nom'], PDO::PARAM_STR);
                $inscrireUser->bindValue(':prenom', $_POST['prenom'], PDO::PARAM_STR);
                $inscrireUser->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
                $inscrireUser->bindValue(':civilite', $_POST['civilite'], PDO::PARAM_STR);
                $inscrireUser->bindValue(':ville', $_POST['ville'], PDO::PARAM_STR);
                $inscrireUser->bindValue(':code_postal', $_POST['code_postal'], PDO::PARAM_INT);
                $inscrireUser->bindValue(':adresse', $_POST['adresse'], PDO::PARAM_STR);
                $inscrireUser->execute();
            }
        }
    }

    // procédure de récupération des infos en BDD pour les afficher dans le formulaire lorsque on fait un update (plus pratique et plus sur)
    if ($_GET['action'] == 'update') {
        $tousUsers = $pdo->query("SELECT * FROM membre WHERE id_membre = '$_GET[id_membre]' ");
        $userActuel = $tousUsers->fetch(PDO::FETCH_ASSOC);
    }

    $id_membre = (isset($userActuel['id_membre'])) ? $userActuel['id_membre'] : "";
    $pseudo = (isset($userActuel['pseudo'])) ? $userActuel['pseudo'] : "";
    $email = (isset($userActuel['email'])) ? $userActuel['email'] : "";
    $nom = (isset($userActuel['nom'])) ? $userActuel['nom'] : "";
    $prenom = (isset($userActuel['prenom'])) ? $userActuel['prenom'] : "";
    $civilite = (isset($userActuel['civilite'])) ? $userActuel['civilite'] : "";
    $ville = (isset($userActuel['ville'])) ? $userActuel['ville'] : "";
    $code_postal = (isset($userActuel['code_postal'])) ? $userActuel['code_postal'] : "";
    $adresse = (isset($userActuel['adresse'])) ? $userActuel['adresse'] : "";
    // syntaxe de condition classique équivalente à la ternaire juste au dessus
    /*if(isset($userActuel['pseudo'])){
            $pseudo = $userActuel['pseudo'];
        }else{
            $pseudo = "";
        }*/
}
require_once('includeAdmin/header.php');
?>

<!-- $erreur .= '<div class="alert alert-danger" role="alert">Erreur format mot de passe !</div>'; -->

<!-- $content .= '<div class="alert alert-success alert-dismissible fade show mt-5" role="alert">
                        <strong>Félicitations !</strong> Insertion du produit réussie !
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>'; -->

<h1 class="text-center my-5">
    <div class="badge badge-warning text-wrap p-3">Gestion des produits</div>
</h1>

<?php if (isset($_GET['action']) || isset($_GET['page'])) : ?>
<div class="blockquote alert alert-dismissible fade show mt-5 shadow border border-warning rounded" role="alert">
    <p>Gérez ici votre base de données des produits</p>
    <p>Vous pouvez modifier leurs données, ajouter ou supprimer un produit</p>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<?php if(isset($_GET['action'])): ?>
<h2 class="pt-5">Formulaire <?= ($_GET['action'] == 'add') ? "d'ajout" : "de modification" ?> des produits</h2>

<!-- l'attribut enctype de la balise form permet l'envoi d'un fichier en upload, il est obligatoire, sinon on ne pourra envoyer le fichier image correspondant au produit -->
<form id="monForm" class="my-5" method="POST" action="" enctype="multipart/form-data">



    <div class="row mt-5">
        <div class="col-md-4">
            <label class="form-label" for="reference">
                <div class="badge badge-dark text-wrap">Référence</div>
            </label>
            <input class="form-control" type="text" name="reference" id="reference" placeholder="Référence">
        </div>

        <div class="col-md-4">
            <label class="form-label" for="categorie">
                <div class="badge badge-dark text-wrap">Catégorie</div>
            </label>
            <input class="form-control" type="text" name="categorie" id="categorie" placeholder="Catégorie">
        </div>

        <div class="col-md-4">
            <label class="form-label" for="titre">
                <div class="badge badge-dark text-wrap">Titre</div>
            </label>
            <input class="form-control" type="text" name="titre" id="titre" placeholder="Titre">
        </div>
    </div>

    <div class="row justify-content-around mt-5">
        <div class="col-md-6">
            <label class="form-label" for="description">
                <div class="badge badge-dark text-wrap">Description</div>
            </label>
            <textarea class="form-control" name="description" id="description" placeholder="Description" rows="5"></textarea>
        </div>
    </div>

    <div class="row mt-5">

        <div class="col-md-4 mt-3">
            <label class="badge badge-dark text-wrap" for="couleur">Couleur</label>
            <select class="form-control" name="couleur" id="couleur">
                <option value="">Choisissez</option>
                <option class="bg-primary text-light" value="bleu">Bleu</option>
                <option class="bg-danger text-light" value="rouge">Rouge</option>
                <option class="bg-success text-light" value="vert">Vert</option>
                <option class="bg-warning text-light" value="jaune">Jaune</option>
                <option class="bg-light text-dark" value="blanc">Blanc</option>
                <option class="bg-dark text-light" value="noir">Noir</option>
                <option class="text-light" style="background:brown;" value="marron">Marron</option>
            </select>
        </div>

        <div class="col-md-4">
            <p>
            <div class="badge badge-dark text-wrap">Taille</div>
            </p>

            <input type="radio" name="taille" id="taille1" value="small">
            <label class="mx-1" for="taille1">Small</label>

            <input type="radio" name="taille" id="taille2" value="medium">
            <label class="mx-1" for="public2">Medium</label>

            <input type="radio" name="taille" id="taille3" value="large">
            <label class="mx-1" for="taille3">Large</label>

            <input type="radio" name="taille" id="taille4" value="xlarge">
            <label class="mx-1" for="taille4">XLarge</label>
        </div>

        <div class="col-md-4">
            <p>
            <div class="badge badge-dark text-wrap">Public</div>
            </p>

            <input type="radio" name="public" id="public1" value="enfant">
            <label class="mx-1" for="public1">Enfant</label>

            <input type="radio" name="public" id="public2" value="femme">
            <label class="mx-1" for="public2">Femme</label>

            <input type="radio" name="public" id="public3" value="homme">
            <label class="mx-1" for="public3">Homme</label>

            <input type="radio" name="public" id="public4" value="mixte">
            <label class="mx-1" for="public4">Mixte</label>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-4">
            <label class="form-label" for="photo">
                <div class="badge badge-dark text-wrap">Photo</div>
            </label>
            <input class="form-control" type="file" name="photo" id="photo" placeholder="Photo">
        </div>
        <!-- ----------------- -->
        <div class="mt-4">
            <p>Vous pouvez changer d'image
                <img src="" width="50px">
            </p>
        </div>
        <!-- -------------------- -->
        <div class="col-md-4">
            <label class="form-label" for="prix">
                <div class="badge badge-dark text-wrap">Prix</div>
            </label>
            <input class="form-control" type="text" name="prix" id="prix" placeholder="Prix">
        </div>

        <div class="col-md-4">
            <label class="form-label" for="stock">
                <div class="badge badge-dark text-wrap">Stock</div>
            </label>
            <input class="form-control" type="text" name="stock" id="stock" placeholder="Stock">
        </div>
    </div>

    <div class="col-md-1 mt-5">
        <button type="submit" class="btn btn-outline-dark btn-warning">Valider</button>
    </div>

</form>
<?php endif; ?>

<?php $queryProduits = $pdo->query(" SELECT id_produit FROM produit "); ?>
<h2 class="py-5">Nombre de produits en base de données: <?= $queryProduits->rowCount() ?></h2>

<div class="row justify-content-center py-5">
    <a href='?action=add'>
        <button type="button" class="btn btn-sm btn-outline-dark shadow rounded">
            <i class="bi bi-plus-circle-fill"></i> Ajouter un produit
        </button>
    </a>
</div>

<table class="table table-dark text-center">
    <?php $afficheProduits = $pdo->query("SELECT * FROM produit ORDER BY prix ASC ") ?>
    <thead>
        <tr>
            <?php for ($i = 0; $i < $afficheProduits->columnCount(); $i++) :
                $colonne = $afficheProduits->getColumnMeta($i) ?>
                <th><?= $colonne['name'] ?></th>
            <?php endfor; ?>
            <th colspan=2>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($produit = $afficheProduits->fetch(PDO::FETCH_ASSOC)) : ?>
            <tr>
                <?php foreach ($produit as $key => $value) : ?>
                    <?php if ($key == 'prix') : ?>
                        <td><?= $value ?> €</td>
                    <?php elseif ($key == 'photo') : ?>
                        <td><img class="img-fluid" src="<?= URL . 'img/' . $value ?>" width="50" loading="lazy"></td>
                    <?php else : ?>
                        <td><?= $value ?></td>
                    <?php endif; ?>
                <?php endforeach; ?>
                <td><a href='?action=update&id_produit=<?= $produit['id_produit'] ?>'><i class="bi bi-pen-fill text-warning"></i></a></td>
                <td><a data-href="?action=delete&id_produit=<?= $produit['id_produit'] ?>" data-toggle="modal" data-target="#confirm-delete"><i class="bi bi-trash-fill text-danger" style="font-size: 1.5rem;"></i></a></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<nav>
    <ul class="pagination justify-content-end">
        <li class="page-item ">
            <a class="page-link text-dark" href="" aria-label="Previous">
                <span aria-hidden="true">précédente</span>
                <span class="sr-only">Previous</span>
            </a>
        </li>

        <li class="mx-1 page-item">
            <a class="btn btn-outline-dark " href=""></a>
        </li>

        <li class="page-item ">
            <a class="page-link text-dark" href="" aria-label="Next">
                <span aria-hidden="true">suivante</span>
                <span class="sr-only">Next</span>
            </a>
        </li>
    </ul>
</nav>

<!-- <img class="img-fluid" src="" width="50"> -->

<!-- <td><a href=''><i class="bi bi-pen-fill text-warning"></i></a></td>-->
<!-- <td><a data-href="" data-toggle="modal" data-target="#confirm-delete"><i class="bi bi-trash-fill text-danger" style="font-size: 1.5rem;"></i></a></td> -->

<!-- modal suppression codepen https://codepen.io/lowpez/pen/rvXbJq -->

<!-- <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Supprimer article
            </div>
            <div class="modal-body">
                Etes-vous sur de vouloir retirer cet article de votre panier ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
                <a class="btn btn-danger btn-ok">Supprimer</a>
            </div>
        </div>
    </div>
</div> -->

<!-- modal -->

<?php require_once('includeAdmin/footer.php'); ?>