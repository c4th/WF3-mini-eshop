<?php

function debug($argument, $mode = 1) {
	echo '<div style="display: inline-block; padding: 10px; background: salmon; z-index: 1000; top: 5px">';

	if ($mode == 1) {
		echo '<pre>';
		print_r($argument);
		echo '</pre>';
	} else {
		echo '<pre>';
		var_dump($argument);
		echo '</pre>';
	}
	echo '</div>';
}

//-------- fonction membre ------------- //
function userConnected() {
	if(!empty($_SESSION['utilisateur'])) {
		return true;
	} else {
		return false;
	}
}

//-------- fonction admin ------------- //
function userAdmin() {
	if(userConnected() && $_SESSION['utilisateur']['statut'] == 1) {
		return true;
	} else {
		return false;
	}
}



//-------- fonction divers ------------- //
function checkExtensionPhoto() {
	$extension = strrchr($_FILES['photo']['name'], '.'); // permet de récupérer une partir du string après un caractère indiqué

	$extension = strtolower($extension);
	$extension = substr($extension, 1); // on enlève le point
	$tabExtensionsValides = array('jpg', 'jpeg', 'png', 'gif');

	$verifExtension = in_array($extension, $tabExtensionsValides); // verifie que l'extension est présente dans la tableau
	return $verifExtension; // retourne TRUE ou FALSE
}


function recupInfosArticle($id_article) {
	global $pdo;
	$infosArticle = $pdo->prepare("SELECT id_article, reference, titre, categorie, description, couleur, taille, photo, prix, stock FROM article WHERE id_article = :id_article");
	$id_article = intval($id_article);
	$infosArticle->bindValue(':id_article', $id_article, PDO::PARAM_INT);
	$infosArticle->execute();
	if($infosArticle->rowCount() == 1) {
    	$resultat = $infosArticle->fetch(PDO::FETCH_ASSOC);
    } else {
    	$resultat = false;
    }
    return $resultat;
}