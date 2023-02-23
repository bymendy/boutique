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

// ************ CONTRAINTE ************
// 1ére contrainte
if (isset($_GET['action'])) {
// tous ce qui va concernée l'envoie en base de donnée
    if ($_POST) {
// Les contraintes pour chaque champs
        if (!isset($_POST['reference']) || !preg_match('#^[a-zA-Z0-9-_.]{4,20}$#', $_POST['reference'])) {
            $erreur .= '<div class="alert alert-danger" role="alert"> La référence ne peut pas être vide !</div>';
        }
        if (!isset($_POST['categorie']) || iconv_strlen($_POST['categorie']) < 3 || iconv_strlen($_POST['categorie']) > 20) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format nom !</div>';
        }
        if (!isset($_POST['titre']) || iconv_strlen($_POST['titre']) < 3 || iconv_strlen($_POST['titre']) > 20) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format nom !</div>';
        }
        if (!isset($_POST['description']) || iconv_strlen($_POST['description']) < 3 || iconv_strlen($_POST['description']) > 50) {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format nom !</div>';
        }
        // Syntaxe pour la contrainte radio, selecteurs et checkbox
        if (!isset($_POST['couleur']) || $_POST['couleur'] != 'bleu' && $_POST['couleur'] != 'rouge' && $_POST['couleur'] != 'vert' && $_POST['couleur'] != 'jaune' && $_POST['couleur'] != 'blanc' && $_POST['couleur'] != 'noir' && $_POST['couleur'] != 'marron') {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format couleur !</div>';
        }        
        
        if (!isset($_POST['taille']) || $_POST['taille'] != 'small' && $_POST['taille'] != 'medium' && $_POST['taille'] != 'large' && $_POST['taille'] != 'xlarge') {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format Taille !</div>';
        }
        if (!isset($_POST['public']) || $_POST['public'] != 'enfant' && $_POST['public'] != 'femme' && $_POST['public'] != 'homme' && $_POST['public'] != 'mixte') {
            $erreur .= '<div class="alert alert-danger" role="alert">Erreur format Public !</div>';
        }        
        if (!isset($_POST['prix']) || !preg_match('#^[a-zA-Z0-9-_.]{1,5}$#', $_POST['prix'])) {
            $erreur .= '<div class="alert alert-danger" role="alert"> Le prix ne peut être vide !</div>';
        }
        if (!isset($_POST['stock']) || !preg_match('#^[a-zA-Z0-9-_.]{1,5}$#', $_POST['stock'])) {
            $erreur .= '<div class="alert alert-danger" role="alert"> Le stock ne peut être vide !</div>';
        }   
        // ***  Traitement pour la photo
        // Initialisation de la photo
        $photo_bdd ="";
        // condition pour modifier une photo 
        if($_GET['action']== 'update'){
            $photo_bdd= $_POST['photoActuelle'];
        }
        if(!empty($_FILES['photo']['name'])){
            // je donne un nom à la photoque je vais ajouter en concaténant le nom de la référence du produit, avec le nom du fichier photo d'origine (les deux étant séparés d'un underscore (_))
            $photo_nom = $_POST['reference'] . '_' . $_FILES['photo']['name'];
            // utilisation de la variable photo_bdd pour lui affecter la valeur de photo_nom, sous forme de chaine de caractéres (pour les bindValue)
            $photo_bdd= "$photo_nom";
            // declaration de la variable qui va enregistrer le chemin ou uploader notre fichier (les photos vont aller dans le dossier img de notre projet, en localcomme en ligne lorsque le site sera héberger)
            $photo_dossier = RACINE_SITE . "img/$photo_nom";
            copy($_FILES['photo']['tmp_name'], $photo_dossier);
        }
        // *** Fin traitement photo
        // Condition si la personne à bien renseigner les champs et ne s'est pas tromper
        if (empty($erreur)) {
            // si dans l'URL action == update, on entame une procédure de modification
            if ($_GET['action'] == 'update') {
                $modifProduit = $pdo->prepare(" UPDATE produit SET id_produit = :id_produit , reference = :reference, categorie = :categorie, titre = :titre, description = :description, couleur = :couleur, taille = :taille, public = :public, photo = :photo, prix = :prix, stock = :stock WHERE id_produit = :id_produit ");
                $modifProduit->bindValue(':id_produit', $_POST['id_produit'], PDO::PARAM_INT);
                $modifProduit->bindValue(':reference', $_POST['reference'], PDO::PARAM_STR);
                $modifProduit->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_STR);
                $modifProduit->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
                $modifProduit->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
                $modifProduit->bindValue(':couleur', $_POST['couleur'], PDO::PARAM_STR);
                $modifProduit->bindValue(':taille', $_POST['taille'], PDO::PARAM_STR);
                $modifProduit->bindValue(':public', $_POST['public'], PDO::PARAM_STR);
                // $modifProduit->bindValue(':photo', $_POST['photo'], PDO::PARAM_STR);
                $modifProduit->bindValue(':prix', $_POST['prix'], PDO::PARAM_INT);
                $modifProduit->bindValue(':stock', $_POST['stock'], PDO::PARAM_INT);
                $modifProduit->execute();
                // Requete pour afficher un message personnaliser lorsque la modification à bien été réussie
                $queryProduit = $pdo->query(" SELECT titre FROM produit WHERE id_produit = '$_GET[id_produit]' ");
                // le query permet de cibler un élément tandis que le fetch permet de récupérer la cible
                $produit = $queryProduit->fetch(PDO::FETCH_ASSOC);

                $content .= '<div class="alert alert-success alert-dismissible fade show mt-5" role="alert">
                        <strong>Félicitations !</strong> Modification du produit '. $produit['titre'] .' réussie !
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else {
                // si on récupère autre chose que update (et donc add) on entame une procédure d'insertion en BDD
                $inscrireProduit = $pdo->prepare(" INSERT INTO produit (reference, categorie, titre, description, couleur, taille, public, photo, prix, stock) VALUES (:reference, :categorie, :titre, :description, :couleur, :taille, :public, :photo, :prix, :stock) ");
                $inscrireProduit->bindValue(':reference', $_POST['reference'], PDO::PARAM_STR);
                $inscrireProduit->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_STR);
                $inscrireProduit->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
                $inscrireProduit->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
                $inscrireProduit->bindValue(':couleur', $_POST['couleur'], PDO::PARAM_STR);
                $inscrireProduit->bindValue(':taille', $_POST['taille'], PDO::PARAM_STR);
                $inscrireProduit->bindValue(':public', $_POST['public'], PDO::PARAM_STR);
                $inscrireProduit->bindValue(':photo', $photo_bdd, PDO::PARAM_STR);
                $inscrireProduit->bindValue(':prix', $_POST['prix'], PDO::PARAM_INT);
                $inscrireProduit->bindValue(':stock', $_POST['stock'], PDO::PARAM_INT);
                $inscrireProduit->execute();
            }
        }
    }

    // procédure de récupération des infos en BDD pour les afficher dans le formulaire lorsque on fait un update (plus pratique et plus sur)
    if ($_GET['action'] == 'update') {
        $tousProduit = $pdo->query("SELECT * FROM produit WHERE id_produit = '$_GET[id_produit]' ");
        $produitActuel = $tousProduit->fetch(PDO::FETCH_ASSOC);
    }

    $id_produit = (isset($produitActuel['id_produit'])) ? $produitActuel['id_produit'] : "";
    $reference = (isset($produitActuel['reference'])) ? $produitActuel['reference'] : "";
    $categorie = (isset($produitActuel['categorie'])) ? $produitActuel['categorie'] : "";
    $titre = (isset($produitActuel['titre'])) ? $produitActuel['titre'] : "";
    $description = (isset($produitActuel['description'])) ? $produitActuel['description'] : "";
    $couleur = (isset($produitActuel['couleur'])) ? $produitActuel['couleur'] : "";
    $taille = (isset($produitActuel['taille'])) ? $produitActuel['taille'] : "";
    $public = (isset($produitActuel['public'])) ? $produitActuel['public'] : "";
    $photo = (isset($produitActuel['photo'])) ? $produitActuel['photo'] : "";
    $prix = (isset($produitActuel['prix'])) ? $produitActuel['prix'] : "";
    $stock = (isset($produitActuel['stock'])) ? $produitActuel['stock'] : "";

    // Requete pour effectuer une Supression
    if($_GET['action'] == 'delete'){
        $pdo->query(" DELETE FROM produit WHERE id_produit = '$_GET[id_produit]' ");
    }
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

<?= $erreur ?>
<?= $content ?>
<!-- Utilisation de la fonction personnalisée debug pour savoir ce qui a été récupéré avec $_POST, pour comprendre en cas de probléme, ou est que cela se situe -->
<!-- <?= debug($_POST) ?> -->

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
    <!-- Important d'incorporer l'id_produit et de le cacher avec hidden  -->
    <input type="hidden" name="id_produit" value="<?= $id_produit ?>">

    <div class="row mt-5">
        <div class="col-md-4">
            <label class="form-label" for="reference">
                <div class="badge badge-dark text-wrap">Référence</div>
            </label>
            <input class="form-control" type="text" name="reference" id="reference" placeholder="Référence" value="<?= $reference ?>" >
        </div>

        <div class="col-md-4">
            <label class="form-label" for="categorie">
                <div class="badge badge-dark text-wrap">Catégorie</div>
            </label>
            <input class="form-control" type="text" name="categorie" id="categorie" placeholder="Catégorie" value="<?= $categorie ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label" for="titre">
                <div class="badge badge-dark text-wrap">Titre</div>
            </label>
            <input class="form-control" type="text" name="titre" id="titre" placeholder="Titre" value="<?= $titre ?>">
        </div>
    </div>

    <div class="row justify-content-around mt-5">
        <div class="col-md-6">
            <label class="form-label" for="description">
                <div class="badge badge-dark text-wrap">Description</div>
            </label>
            <!-- Cas particulier pour le textarea mettre la value entre l'ouvrante et la fermante  -->
            <textarea class="form-control" name="description" id="description" placeholder="Description" rows="5" ><?= $description ?>"</textarea>
        </div>
    </div>

    <div class="row mt-5">


        <div class="col-md-4 mt-3">
            <label class="badge badge-dark text-wrap" for="couleur">Couleur</label>
            <select class="form-control" name="couleur" id="couleur">
                <option value="">Choisissez</option>
                <option class="bg-primary text-light" value="bleu" <?= ($couleur == 'bleu') ? 'selected' : '' ?>>Bleu</option>
                <option class="bg-danger text-light" value="rouge" <?= ($couleur == 'rouge') ? 'selected' : '' ?>>Rouge</option>
                <option class="bg-success text-light" value="vert" <?= ($couleur == 'vert') ? 'selected' : '' ?>>Vert</option>
                <option class="bg-warning text-light" value="jaune" <?= ($couleur == 'jaune') ? 'selected' : '' ?>>Jaune</option>
                <option class="bg-light text-dark" value="blanc" <?= ($couleur == 'blanc') ? 'selected' : '' ?>>Blanc</option>
                <option class="bg-dark text-light" value="noir" <?= ($couleur == 'noir') ? 'selected' : '' ?>>Noir</option>
                <option class="text-light" style="background:brown;" value="marron" <?= ($couleur == 'noir') ? 'selected' : '' ?>>Marron</option>
            </select>
        </div>
        <!--  -->
        <div class="col-md-4">
            <p>
            <div class="badge badge-dark text-wrap">Taille</div>
            </p>

            <input type="radio" name="taille" id="taille1" value="small" <?= ($taille == 'small') ? 'checked' : '' ?>>
            <label class="mx-1" for="taille1">Small</label>

            <input type="radio" name="taille" id="taille2" value="medium" <?= ($taille == 'medium') ? 'checked' : '' ?>>
            <label class="mx-1" for="public2">Medium</label>

            <input type="radio" name="taille" id="taille3" value="large" <?= ($taille == 'large') ? 'checked' : '' ?>>
            <label class="mx-1" for="taille3">Large</label>

            <input type="radio" name="taille" id="taille4" value="xlarge" <?= ($taille == 'xlarge') ? 'checked' : '' ?>>
            <label class="mx-1" for="taille4">XLarge</label>
        </div>

        <div class="col-md-4">
            <p>
            <div class="badge badge-dark text-wrap">Public</div>
            </p>

            <input type="radio" name="public" id="public1" value="enfant" <?= ($public == 'enfant') ? 'checked' : '' ?>>
            <label class="mx-1" for="public1">Enfant</label>

            <input type="radio" name="public" id="public2" value="femme" <?= ($public == 'femme') ? 'checked' : '' ?>>
            <label class="mx-1" for="public2">Femme</label>

            <input type="radio" name="public" id="public3" value="homme" <?= ($public == 'homme') ? 'checked' : '' ?>>
            <label class="mx-1" for="public3">Homme</label>

            <input type="radio" name="public" id="public4" value="mixte" <?= ($public == 'mixte') ? 'checked' : '' ?>>
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
            <input class="form-control" type="text" name="prix" id="prix" placeholder="Prix" value="<?= $prix ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label" for="stock">
                <div class="badge badge-dark text-wrap">Stock</div>
            </label>
            <input class="form-control" type="text" name="stock" id="stock" placeholder="Stock" value="<?= $stock ?>">
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

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
</div>

<!-- modal -->

<?php require_once('includeAdmin/footer.php'); ?>