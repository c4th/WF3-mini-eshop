
<?php
//-------- configuration ------------- //
require_once('inc/init.inc.php');

//------ affichage ------------------ //
$pageCourante = 'Boutique';
include_once('inc/header.inc.php');

if( !empty($_GET['id_article']) && is_numeric($_GET['id_article']) ) {
	$_GET['id_article'] = intval($_GET['id_article']);
	$article = recupInfosArticle($_GET['id_article']);
	if (!$article) {
		$contenu = '<div class="alert alert-danger" role="alert">Article introuvable !</div>';
	} else {
		$contenu = ' <div class="thumbnail">
	      <img src="'. RACINE_SITE . $article['photo']. '" alt="'.$article['titre'].'">
	      <div class="caption">
	        <h1>'.$article['titre'].'</h1>
	        <p>'.$article['description'].'</p>
	        <h3 class="text-right">Prix : ' .$article['prix']. '<span class="glyphicon glyphicon-euro"></span></h3>
	        <p><strong>Couleur</strong> : '.$article['couleur']. '</p>

	        	<form method="post" action="panier.php">
		        <p><strong>Taille</strong> : '.$article['taille'].' </p>
	       		<p><strong>Référence de l\'article</strong> : '.$article['reference'].'</p>	
		    	<p class="form-inline text-right">Quantité : ';
		    	
		    	if ($article['stock'] > 0) {
		    		$contenu .='<select name="quantité" id="quantité">';

	        		for ($i=1; $i<=$article['stock']; $i++) { 
		        		$contenu .=  '<option value="'.$i.'">'.$i.'</option>';
		        	}
		        	$contenu .= '</select>
		        	</p>
		        	<p class="text-right">	        	
		        	<button class="btn btn-info" type="submit"><span class="glyphicon glyphicon-shopping-cart"></span> Ajouter au panier</button>';
		    	} else {
		    		$contenu.= '<p class="text-right"><div class="alert alert-danger col-md-4" role="alert">Article indisponible !</div></p>';
		    	}		    	
		    	$contenu .= '</p>
	        </form>        
	      </div>
	    </div>';
	}
} else {
	header('location: boutique.php');
}

//------ affichage ------------------ //
$pageCourante = $article['categorie']. '-' .$article['titre'];
include_once('inc/header.inc.php');

$back_link = 'boutique.php';
if (!empty($_SERVER['HTTP_REFERER'])) {
	$back_link = $_SERVER['HTTP_REFERER'];
}

?>
<div class="content container-fluid">
	<a href="<?= $back_link ?>">&laquo; Retour</a>
	<div class="row">
	  <div class="col-sm-6 col-md-4 col-md-offset-4">
	  	<?= $contenu; ?>
	  </div>
	</div>
</div>
<?php
include_once('inc/footer.inc.php');