<?php
// affichage des catégories dans la navigation latérale
$afficheMenuCategories = $pdo->query(" SELECT DISTINCT categorie FROM produit ORDER BY categorie ASC ");
// fin de navigation laterale catégories

// tout l'affichage par categorie
if(isset($_GET['categorie'])){
    // pagination pour les categories
    
    // fin pagination pour les categories

    // affichage de tous les produits concernés par une categorie
    $afficheProduits = $pdo->query(" SELECT * FROM produit WHERE categorie = '$_GET[categorie]' ORDER BY prix ASC ");
    // fin affichage des produits par categorie

    // affichage de la categorie dans le <h2>
    $afficheTitreCategorie = $pdo->query(" SELECT categorie FROM produit WHERE categorie = '$_GET[categorie]' ");
    $titreCategorie = $afficheTitreCategorie->fetch(PDO::FETCH_ASSOC);
    // fin du h2 categorie

    // pour les onglets categories
    $pageTitle = "Nos modèles de " . $_GET['categorie'];
    // fin onglets categories
}
// fin affichage par categorie

// -----------------------------------------------------------------------------------

// tout l'affichage par public
if(isset($_GET['public'])){
    // pagination produits par public
    
    // fin pagination produits par public

    // affichage des produits par public
    // requete qui va cibler tous les produits qui ont en commun le public récupéré dans l'URL
    $afficheProduits = $pdo->query(" SELECT * FROM produit WHERE public = '$_GET[public]' ORDER BY prix ASC ");
    // fin affichage des produits par public

    // affichage du public dans le <h2>
    $afficheTitrePublic = $pdo->query(" SELECT public FROM produit WHERE public = '$_GET[public]' ");
    $titrePublic = $afficheTitrePublic->fetch(PDO::FETCH_ASSOC);
    // fin du </h2> pour le public

    // pour les onglets publics
    $pageTitle = "Nos vetements " . ucfirst($_GET['public']) . 's'; 
    // fin onglets publics
}
// fin affichage par public

// ---------------------------------------------------------------------------------------
// Tout ce qui concerne la fiche produit

// affichage d'un produit
if(isset($_GET['id_produit'])){
    $detailProduit =$pdo->query("SELECT * FROM produit WHERE id_produit= '$_GET[id_produit]'");
    // pour se protéger de qlq'un qui tenterait de modifier l'id-produit dans l'URL...si la valeur n'existe pas en BDD, on le redirige vers notre index (URL). Le <= 0 est fait dans le cas ou il injecte une valeur négative
    if($detailProduit->rowCount() <=0){
        header('location:' . URL);
        exit;
    }
    // si on n'est pas rentré dans la condition, si le produit existe, on fait le fetch, et le résultat de la requete sera affecté dans la variable/tableau $detail
    $detail= $detailProduit->fetch(PDO::FETCH_ASSOC);
}
// fin affichage d'un seul produit


//  fin fiche produit

// --------------------------------------------------------------------------------------------