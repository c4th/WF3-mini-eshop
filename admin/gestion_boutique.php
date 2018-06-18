<?php
//-------- configuration ------------- //
require_once('../inc/init.inc.php');
if(!userAdmin()) {
  header('location: '.RACINE_SITE.'/profil.php');
  exit();
}
$controle = true;

if(!empty($_POST)) {
  echo '<hr>$_POST : ' . debug($_POST). '<hr>';
  echo '<hr>$_FILES : ' . debug($_FILES). '<hr>';

  extract($_POST);

  if( !empty($reference) && !empty($categorie) && !empty($titre) && !empty($couleur) && !empty($description) && !empty($taille) && !empty($prix) && !empty($stock) && !empty($_FILES) ) {
    $checkRef = $pdo->prepare('SELECT reference FROM article WHERE reference = :reference');
    $reference= substr($reference, 0, 10); // je decoupe la ref à 10 caractères (car les types INT de SQL sont limités à 10 caractères max)
    $checkRef->bindValue(':reference', $reference, PDO::PARAM_INT);
    $checkRef->execute();
    
    if($checkRef->rowCount() > 0 && $_GET['action'] != 'modifier') { // je check la réference que si je ne suis PAS dans le cas de la modification (autrement dit lorsque je suis dans le cas de l'ajout)
      $msg .= '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> L\'article est déjà enregistré en BDD</p>';
      $controle = false;
    } elseif(checkExtensionPhoto()) {
      $cheminPhoto = '/photos/' . $reference . '_' . $_FILES['photo']['name'];
      copy($_FILES['photo']['tmp_name'], RACINE_SERVER . RACINE_SITE . $cheminPhoto);
    } elseif(!empty($_GET['action']) && $_GET['action'] == 'modifier'){
      $cheminPhoto = $photo; // extract du $_POST['photo']
    } else {
      $msg .= '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> L\'extension de votre image n\'est pas valide</p>';
      $controle = false;
    }

  } else { // else du if (!empty($reference) etc ..) 
    $msg .=  '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Merci de remplir tous les champs !</p>';
    $controle = false;
  } // FIN else du if (!empty($reference) etc ..) 

} else {
  $controle = false;
}

if($controle) { // si controle est toujours à true 
  $ajoutArticle = $pdo->prepare('REPLACE INTO article (id_article, reference, categorie, couleur, prix, description, titre, taille, stock, photo) 
    VALUES (:id_article, :reference, :categorie, :couleur, :prix, :description, :titre, :taille, :stock, :photo)');
  $ajoutArticle->bindValue(':id_article', $id_article, PDO::PARAM_INT);
  $ajoutArticle->bindValue(':reference', $reference, PDO::PARAM_INT);
  $ajoutArticle->bindValue(':categorie', $categorie, PDO::PARAM_STR);
  $ajoutArticle->bindValue(':couleur', $couleur, PDO::PARAM_STR);
  $ajoutArticle->bindValue(':prix', $prix, PDO::PARAM_INT);
  $ajoutArticle->bindValue(':description', $description, PDO::PARAM_STR);
  $ajoutArticle->bindValue(':titre', $titre, PDO::PARAM_STR);
  $ajoutArticle->bindValue(':taille', $taille, PDO::PARAM_STR);
  $ajoutArticle->bindValue(':stock', $stock, PDO::PARAM_INT);
  $ajoutArticle->bindValue(':photo', $cheminPhoto, PDO::PARAM_STR);
  $ajoutArticle->execute();
  $msgAction = (!empty($_GET['action']) && $_GET['action'] == 'modifier') ? 'modifié' : 'enregistré';
  $msg .=  '<p class="alert alert-success" role="alert"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Produit '.$msgAction.' avec succès !</p>'; 
}

// ----------------- Suppression d'une ligne ------------------------ //
if( (!empty($_GET['action']) && $_GET['action'] == 'supprimer')
  && (!empty($_GET['id_article']) && is_numeric($_GET['id_article'])) ) {

  $id_article = intval($_GET['id_article']); // transformation en type integer
  
  $recupPhoto = recupInfosArticle($id_article);
 
  $suppr = $pdo->prepare("DELETE FROM article WHERE id_article = :id_article");
  $suppr->bindValue(':id_article', $id_article, PDO::PARAM_INT);
  $suppr->execute();  

  if($suppr->rowCount() == 1) {
    $msg .= '<p class="alert alert-success" role="alert"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Le produit à l\'id n°'.$id_article.' a été supprimé avec succès !</p>';

    if (!empty($recupPhoto['photo'])) {
      $suppr_photo = RACINE_SERVER . RACINE_SITE . $recupPhoto['photo'];
    }

    if( isset($suppr_photo) && file_exists($suppr_photo)) {
      unlink($suppr_photo);
    }

  }
}

// ---------------- Affichage du tableau des articles --------------- //
$produits = $pdo->query("SELECT id_article, reference, titre, categorie, description, couleur, taille, photo, prix, stock
  FROM article");
$affichageProduits = $produits->fetchAll(PDO::FETCH_ASSOC);
// debug($affichageProduits); // verifier si on récupère bien nos produits 
//-- titres de mon tableau :
$titres = '<tr>';
foreach($affichageProduits[0] as $key => $value) {
  $titres .= '<th>' . $key . '</th>';
}
$titres .= '<th>mod</th>';
$titres .= '<th>suppr</th>';
$titres .= '</tr>';

$donnees = '';
$nbreLignes = $produits->rowCount();
for($i=0; $i<$nbreLignes;$i++) {
  $donnees .= '<tr>';
  foreach($affichageProduits[$i] as $key => $value) {
    switch($key) {
      case 'photo' : $donnees .= '<td><img width="100" src="' . RACINE_SITE . $value . '"></td>';
        break;
      case 'description' : $donnees .= '<td>' . substr($value, 0, 100) . '...</td>';
        break;
      default : $donnees .= '<td>' . $value . '</td>';
        break;
    }
  }
  $donnees .= '<td><a href="?action=modifier&id_article=' . $affichageProduits[$i]['id_article'] . '">...</a></td>';
  $donnees .='<td><a href="?action=supprimer&id_article=' . $affichageProduits[$i]['id_article'] . '">X</a></td>';
  $donnees .= '</tr>';
}

//--- Ajouter ou modifier, on affiche le formulaire
if( !empty($_GET['action']) && ($_GET['action'] == 'ajouter' || ($_GET['action'] == 'modifier')) ) {

  if( !empty($_GET['id_article']) && is_numeric($_GET['id_article']) ) {
    $resultat = recupInfosArticle($_GET['id_article']);

    if($resultat) {
        extract($resultat);
    }
  }
  
  /*
  if(!empty($reference)) {
    $reference = $reference;
  } else {
    $reference = '';
  }
  */ // version ternaire juste en dessous
  // Si je suis dans le cas d'un ajout, $reference n'existe pas car la fonction extract (qui lui donne son existence), n'est utilisée que dans le cas d'un $_POST (resaisie) et dans le cas d'un $resultat de la BDD (modification)
  $id_article = !empty($id_article) ? $id_article : '';
  $reference = !empty($reference) ? $reference : '';
  $titre = !empty($titre) ? $titre : '';
  $categorie = !empty($categorie) ? $categorie : '';
  $description = !empty($description) ? $description : '';
  $couleur = !empty($couleur) ? $couleur : '';
  $tailleXL = !empty($taille) && strtolower($taille) == 'xl' ? 'selected' : '';
  $tailleL = !empty($taille) && strtolower($taille) == 'l' ? 'selected' : '';
  $tailleM = !empty($taille) && strtolower($taille) == 'm' ? 'selected' : '';
  $tailleS = !empty($taille) && strtolower($taille) == 's' ? 'selected' : '';
  $prix = !empty($prix) ? $prix : '';
  $stock = !empty($stock) ? $stock : '';
  $cheminPhoto = !empty($photo) ? RACINE_SITE . $photo : '';
  $affichagePhoto = !empty($cheminPhoto) ? '<input type="text" name="photo" value="'.$photo.'"><img width="50"src="'.$cheminPhoto.'">' : '';

  $bouton = ucfirst($_GET['action']);

  $contenu = '<div class="row centered-form">
      <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">'.$bouton.' un produit</h3>
          </div>
          <div class="panel-body">
            <form method="post" enctype="multipart/form-data" role="form">
              <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6">
                  <div class="form-group">
                    <input type="hidden" name="id_article" value="'.$id_article.'">
                    <input type="text" name="reference" id="reference" class="form-control input-sm" maxlength="10" placeholder="Réference" 
                    value="'.$reference. '">
                  </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6">
                  <div class="form-group">
                    <input type="text" name="categorie" id="categorie" class="form-control input-sm" placeholder="Catégorie" value="'.$categorie.'">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6">
                  <div class="form-group">
                    <input type="text" name="titre" id="titre" class="form-control input-sm" placeholder="Titre" value="'.$titre.'">
                  </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6">
                  <div class="form-group">
                    <input type="text" name="couleur" id="couleur" class="form-control input-sm" placeholder="Couleur" value="'.$couleur.'">
                  </div>
                </div>
              </div>

              <div class="form-group">
                <textarea name="description" id="description" class="form-control input-sm" placeholder="Description">'.$description.'</textarea>
              </div>
              
              <div class="form-group">
              '.$affichagePhoto.'
                <input type="file" name="photo" id="photo" class="form-control" >
              </div>

               <div class="row">
                <div class="col-xs-3 col-sm-3 col-md-3">
                  <div class="form-group">
                    <select name="taille" id="taille" class="form-control input-sm" >
                      <option value="xl" '.$tailleXL.' >XL</option>
                      <option value="l" '.$tailleL.' >L</option>
                      <option value="m" '.$tailleM.' >M</option>
                      <option value="s" '.$tailleS.' >S</option>
                    </select>
                  </div>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                  <div class="form-group">
                    <input type="text" name="prix" id="prix" class="form-control input-sm" placeholder="Prix" value="'.$prix.'">
                  </div>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3">
                  <div class="form-group">
                    <input type="text" name="stock" id="stock" class="form-control input-sm" placeholder="Stock" value="'.$stock.'">
                  </div>
                </div>
              </div>
              
              <button style="margin-top: 15px;" type="submit" class="btn btn-info btn-block">'.$bouton.'</button>

            </form>
          </div>
        </div>
      </div>
    </div>'; }
   else {
    $contenu = '<div class="row">
    <div class="col-md-offset-2 col-md-8 col-md-offset-2">
    <table class="table table-border table-hover">
    '. $titres . $donnees . '
    </table>
    </div>
  </div>';
  }

$affichageActif = (empty($_GET['action']) || $_GET['action'] != 'ajouter') ? 'active' : '';
$ajoutActif = (!empty($_GET['action']) && $_GET['action'] == 'ajouter') ? 'active' : '';
//------ affichage ------------------ //
$pageCourante = 'Gestion Boutique';
include_once('../inc/header.inc.php');
?>

<div class="content container-fluid">
  <?php if(!empty($msg)) echo $msg; ?>
  <ul style="margin: 10px 0;" class="nav nav-tabs">
  <li role="presentation" class="<?php echo $affichageActif; ?>"><a href="?action=affichage">Afficher les produits</a></li>
  <li role="presentation" class="<?php echo $ajoutActif; ?>"><a href="?action=ajouter">Ajouter produits</a></li>
</ul>  
  
  <?php echo $contenu; ?>

</div>

<?php
include_once('../inc/footer.inc.php');