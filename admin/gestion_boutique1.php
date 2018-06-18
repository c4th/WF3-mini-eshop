<?php
//-------- configuration ------------- //
require_once('../inc/init.inc.php');

if(!userAdmin()) {
  header('location: '.RACINE_SITE.'/profil.php');
  exit();
}
$controle = true;

if ( !empty($_POST) && !empty($_FILES)) {
  extract($_POST);

  if (!empty($reference) && !empty($categorie) && !empty($titre) && !empty($description) && !empty($couleur) && !empty($taille) && !empty($prix) && !empty($stock)) {
    
    $checkRef = $pdo->prepare('SELECT reference FROM article WHERE reference = :reference');
    $reference = substr($reference, 0, 10);
    $checkRef->bindValue('reference', $reference, PDO::PARAM_INT);
    $checkRef->execute();

    if ($checkRef->rowCount() > 0) {
       $msg .= '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> L\'article existe déjà dans la base de données !</p>';

       $controle = false;
    }

    if(checkExtensionPhoto()) {
      $photo = '/photos/'.$reference.'_'.$_FILES['photo']['name'];
      copy($_FILES['photo']['tmp_name'], dirname(dirname(__FILE__)).$photo);

    } else {
       $msg .= '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Fichier photo non valide.</p>';

       $controle = false;
    }


  } else {
    $msg .= '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Veuillez remplir tous les champs !</p>';

    $controle = false;
  }
} else {
  $controle = false; 
}

if ($controle) {
  $insertion = $pdo->prepare("INSERT INTO article(reference, categorie, titre, description, couleur, taille, photo, prix, stock)
      VALUES(:reference, :categorie, :titre, :description, :couleur, :taille, :photo, :prix, :stock)");

      $insertion->bindValue('reference', $reference, PDO::PARAM_INT);
      $insertion->bindValue('categorie', $categorie, PDO::PARAM_STR);
      $insertion->bindValue('titre', $titre, PDO::PARAM_STR);
      $insertion->bindValue('description', $description, PDO::PARAM_STR);
      $insertion->bindValue('couleur', $couleur, PDO::PARAM_STR);
      $insertion->bindValue('taille', $taille, PDO::PARAM_STR);
      $insertion->bindValue('photo', $photo, PDO::PARAM_STR);
      $insertion->bindValue('prix', $prix, PDO::PARAM_INT);
      $insertion->bindValue('stock', $stock, PDO::PARAM_INT);
      $insertion->execute();

      $msg .= '<p class="alert bg-success" role="alert"><span class="glyphicon glyphicon-ok"></span> Votre article a bien été ajouté !</p>';
}

//------ requête de supression ------ //

if ( !empty($_GET['action']) && $_GET['action'] == "supprimer" && !empty($_GET['id_article']) && is_numeric($_GET['id_article']) ) {

    $id_article = intval($_GET['id']);

    $suppression = $pdo->prepare('DELETE FROM article WHERE id_article = :id_article');
    $suppression->bindValue('id_article', $id_article, PDO::PARAM_INT);
    $suppression->execute();
    
    if ($suppression->rowCount() == 1) {
      $msg .= '<p class="alert bg-success" role="alert"><span class="glyphicon glyphicon-ok"></span> Article supprimé.</p>';
    }
}

//------ requête de modification ------ //

if (!empty($_GET['action'])) {
  if ($_GET['action'] == "modifier") {

    $id_article = $_GET['id'];

    $modification = $pdo->prepare('UPDATE article SET id_article, reference, categorie, titre, description, couleur, taille, photo, prix, stock WHERE id_article = :id_article');
    $modification->bindValue('id_article', $id_article, PDO::PARAM_INT);
    $modification->execute();

    $msg .= '<p class="alert bg-success" role="alert"><span class="glyphicon glyphicon-ok"></span> Les informations de l\'article ont été modifiées.</p>';
  }
}


//------ affichage du tableau des articles ------ //
$recuperation = $pdo->query("SELECT id_article, reference, categorie, titre, description, couleur, taille, photo, prix, stock FROM article");
$articles = $recuperation->fetchAll(PDO::FETCH_ASSOC);


//------ affichage ------ //
$pageCourante = 'Gestion Boutique';
include_once('../inc/header.inc.php');

?>

<div class="content container-fluid">
  <?php if(!empty($msg)) echo $msg; ?>
  <a href="?action=afficher" class="btn btn-default">Afficher les articles</a>
  <a href="?action=ajouter" class="btn btn-default">Ajouter un article</a>
  <div class="row centered-form">
    <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Ajouter un produit</h3>
        </div>
        <div class="panel-body">
          <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
              <label for="reference">Référence</label>
              <input type="text" class="form-control" id="reference" name="reference" placeholder="Référence" maxlength="10" value="<?= $article['reference']?>">
            </div>
            <div class="form-group">
              <label for="categorie">Catégorie</label>
              <input type="text" class="form-control" id="categorie" name="categorie" placeholder="Catégorie" value="<?= $article['categorie']?>">
            </div>
            <div class="form-group">
              <label for="titre">Titre</label>
              <input type="text" class="form-control" id="titre" name="titre" placeholder="Titre" value="<?= $article['titre']?>">
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea type="text" class="form-control" id="description" name="description" placeholder="Description"><?= $article['description']?></textarea>
            </div>
            <div class="form-group">
              <label for="couleur">Couleur</label>
              <input type="text" class="form-control" id="couleur" name="couleur" placeholder="Couleur" value="<?= $article['couleur']?>">
            </div>
            <div class="form-group">
              <label for="taille">Taille</label>
                <select name="taille" id="taille">
                    <option value="xl">XL</option>
                    <option value="l">L</option>
                    <option value="m">M</option>
                </select>
            </div>
            <div class="form-group">
              <label for="prix">Prix</label>
              <input type="text" class="form-control" id="prix" name="prix" placeholder="Prix" value="<?= $article['prix']?>">
            </div>
            <div class="form-group">
              <label for="stock">Stock</label>
              <input type="text" class="form-control" id="stock" name="stock" placeholder="Stock" value="<?= $article['stock']?>">
            </div>
            <div class="form-group">
              <label for="photo">Photo</label>
              <input type="file" id="photo" name="photo" value="<?= $article['photo']?>">
            </div>
            <input type="submit" class="btn btn-info" value="Valider">
          </form>
        </div>
      </div>
    </div>

  <div class="col-md-8 col-md-offset-2">
    <table class="table table-hover table-bordered">
      <thead>
        <tr>
          <th>id_article</th>
          <th>référence</th>
          <th>catégorie</th>
          <th>titre</th>
          <th>description</th>
          <th>couleur</th>
          <th>taille</th>
          <th>photo</th>
          <th>prix</th>
          <th>stock</th>
          <th>MODIF</th>
          <th>SUPPR</th>
        </tr>
      </thead>

      <tbody>      
       <?php foreach ($articles as $article) { ?> 
        <tr>
          <td><?= $article['id_article'] ?></td>
          <td><?= $article['reference'] ?></td>
          <td><?= $article['categorie'] ?></td>
          <td><?= $article['titre'] ?></td>
          <td><?= $article['description'] ?></td>
          <td><?= $article['couleur'] ?></td>
          <td><?= $article['taille'] ?></td>
          <td><img src="..<?= $article['photo'] ?>"></td>
          <td><?= $article['prix'] ?></td>
          <td><?= $article['stock'] ?></td>
          <td><a href="?action=modifier&id=<?= $article['id_article'] ?>">...</a></td>
          <td><a href="?action=supprimer&id=<?= $article['id_article'] ?>">X</a></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
  </div>
</div>  




<?php
include_once('../inc/footer.inc.php');