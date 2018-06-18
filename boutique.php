<?php
//-------- configuration ------------- //
require_once('inc/init.inc.php');

$affichageCat = $pdo->query("SELECT DISTINCT categorie FROM article");
$categories = $affichageCat->fetchAll(PDO::FETCH_ASSOC);

if(!empty($_GET['categorie'])) {
	$resultats = $pdo->prepare("SELECT id_article, photo, prix, titre FROM article WHERE categorie = :categorie");
	$resultats->bindValue(':categorie', $_GET['categorie'], PDO::PARAM_STR);
	$resultats->execute();
	$produits = $resultats->fetchAll(PDO::FETCH_ASSOC);

    $nbreProduits = sizeof($produits);
    for($i=0;$i<$nbreProduits;$i++) {
    	$contenu .= '<div class="col-xs-3 col-sm-3 col-md-4 col-lg-3">
		<h3 class="text-center">'.$produits[$i]['titre'].'</h3>
    	<p class="text-center">
			<a href="fiche_article.php?id_article='.$produits[$i]['id_article'].'"><img width="100" src="'.RACINE_SITE . $produits[$i]['photo'].'"></a>
			<p class="lead text-center">Prix : '.$produits[$i]['prix'].'€</p>
    	</p>
		</div>';
    }
}

//------ affichage ------------------ //
$pageCourante = 'Boutique';
include_once('inc/header.inc.php');

?>
<div class="content container-fluid ">
	<h1>Boutique</h1>
	<div class="dropdown clearfix">
		<button id="drop" class="btn btn-info dropdown-toggle" data-toggle="dropdown">Catégories <span class="caret"></span>
		</button>
		<ul class="dropdown-menu" role="menu" aria-labelledby="drop">
			<?php
				$nbreCat = sizeof($categories);
				for($i=0; $i<$nbreCat; $i++) : ?>
				<li role="presentation"><a role="menuitem" href="?categorie=<?php echo $categories[$i]['categorie'] ?>"><?php echo $categories[$i]['categorie'] ?></a></li>
			<?php endfor; ?>
	    </ul>
    </div>
    <div class="row">
    	<?php echo $contenu; ?>
    </div>
</div>
<?php
include_once('inc/footer.inc.php');
