<?php
//-------- configuration ------------- //
require_once('inc/init.inc.php');
if(!empty($_GET['action']) && $_GET['action'] === 'logout') {
  unset($_SESSION['utilisateur']);
}
if(!empty($_SESSION['utilisateur'])) { // si la session utilisateur n'est pas vide, cela signifie que la personne est connectée DONC redirection... vers profil.php
  header('location: profil.php');
  exit();
}



if(!empty($_POST)) {
  extract($_POST);
  if(!empty($email) && !empty($mdp)) {
    $recuperation = $pdo->prepare("SELECT pseudo, email, prenom, nom, adresse, cp, ville, statut, sexe FROM membre WHERE email = :email && mdp = :mdp");
    $recuperation->bindValue(':email', $email, PDO::PARAM_STR);
    $recuperation->bindValue(':mdp', $mdp, PDO::PARAM_STR);
    $recuperation->execute();
    if($recuperation->rowCount() === 0) { // si la requete renvoi 0 cela signifie que le mot de passe et l'email ne correspondent pas donc message erreur
      $msg .= '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Erreur : </span>identifiants incorrects !</p>';
    } else {
      $profil = $recuperation->fetch(PDO::FETCH_ASSOC);

      foreach($profil as $key => $value) {
        $_SESSION['utilisateur'][$key] = $value; // je rempli une session avec les infos du profil 
      }
      
      header('location: profil.php'); // si la connexion est OK, je le redirige vers sa page profil
      exit();
    }

  } else {
    $msg .= '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Erreur : </span>Tous les champs doivent être remplis</p>';
  }
}

//------ affichage ------------------ //
$pageCourante = 'Connexion';
include_once('inc/header.inc.php');


?>


<div class="content container-fluid">
	<?php if(!empty($msg)) echo $msg; ?>
  <div class="row centered-form">
    <div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Connexion</h3>
        </div>
        <div class="panel-body">
        	<form class="form-horizontal" method="post" action="">
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">Email</label>
            <div class="col-xs-6 col-sm-6 col-md-6">
              <input name="email" type="email" class="form-control" id="inputEmail3" placeholder="Email">
            </div>
          </div>
          <div class="form-group">
            <label for="inputPassword3" class="col-sm-4 control-label">Mot de passe</label>
            <div class="col-xs-6 col-sm-6 col-md-6">
              <input name="mdp" type="password" class="form-control" id="inputPassword3" placeholder="Mot de passe">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
              <div class="checkbox">
                <label>
                  <input type="checkbox"> Se souvenir
                </label>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
              <button type="submit" class="btn btn-info">Connexion</button>
            </div>
          </div>
        </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
require_once('inc/footer.inc.php');