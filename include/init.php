<?php
// dans ce fichier on va coder tout ce qui va nous servir sur l'intégralite des fichiers de notre boutique 
$pdo = new PDO('mysql:host=localhost;dbname=boutique', 'root', '', array(PDO::ATTR_ERRMODE =>PDO::ERRMODE_WARNING,PDO::MYSQL_ATTR_INIT_COMMAND =>'SET NAMES UTF8' ));

// Le session_start obligatoire  en haut de chaque fichier
session_start();

define('RACINE_SITE',  $_SERVER['DOCUMENT_ROOT'] . '/boutique/');

