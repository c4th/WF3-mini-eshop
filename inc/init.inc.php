<?php
session_start();
require_once('connexion_bdd.inc.php');
require_once('fonctions.inc.php');

define('RACINE_SITE', '/wf3_cupcake_shop'); // constante pour le site
define('RACINE_SERVER', $_SERVER['DOCUMENT_ROOT']); // constante pour le serveur
define('URL', 'http://'.$_SERVER['HTTP_HOST']);

if (!empty($_GET['action'])) {
	if ($_GET['action'] == 'logout') {
		session_destroy(); // supprime le fichier de session et ce sera toujours la dernière instruction exécutée, peut importe l'endroit où il est écrit dans le code
		header('location: connexion.php');
		exit();
	}
}

$msg = '';

$contenu = '';