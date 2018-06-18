<?php
// config
require_once('inc/init.inc.php');

// affichage
$pageCourante = 'Inscription';
require_once('inc/header.inc.php');


if ( !empty($_POST)) {
	extract($_POST); // transforme le nom des champs en variable

		if (!empty($pseudo) && !empty($email) && !empty($sexe) && !empty($ville) && !empty($adresse) && !empty($prenom) && !empty($nom) && !empty($mdp) && !empty($cp) ) {
			
			$recuperation = $pdo->prepare("SELECT email FROM membre WHERE email = :email");
			$recuperation->bindValue('email', $email, PDO::PARAM_STR);
			$recuperation->execute();

				if(strlen($prenom) < 3) {
					$msg .= '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Erreur : </span>Le PRÉNOM doit contenir au moins 3 caractères !</p>';
				} elseif (strlen($nom) < 3) {
					$msg .= '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Erreur : </span>Le NOM doit contenir au moins 3 caractères !</p>';
				} elseif (strlen($mdp) < 8 || !preg_match('/[0-9]/',$mdp)) {
					$msg .= '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Erreur : </span>Le MDP doit contenir au moins 8 caractères dont 1 chiffre !</p>';
				} elseif (strlen($cp) < 5 && intval($cp) == 0) {
					$msg .= '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Erreur : </span>Le CP doit contenir au moins 5 caractères et que des chiffres !</p>';
				} elseif(preg_match('/[0-9]/', $prenom)) {
					$msg .= '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Erreur : </span>Le PRÉNOM ne doit contenir aucun chiffre !</p>';
				} elseif(preg_match('/[0-9]/', $nom)) {
					$msg .= '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Erreur : </span>Le NOM ne doit contenir aucun chiffre !</p>';
				} elseif ($recuperation->rowCount() === 1) {
					$msg .= '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Erreur : </span>Adresse mail non valide !</p>';
				} else {



				$insertion = $pdo->prepare("INSERT INTO membre(pseudo, mdp, nom, prenom, email, sexe, ville, cp, adresse, date_inscription)
											VALUES(:pseudo, :mdp, :nom, :prenom, :email, :sexe, :ville, :cp, :adresse, NOW())");

				$insertion->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
				$insertion->bindValue(':mdp', $mdp, PDO::PARAM_STR);
				$insertion->bindValue(':nom', $nom, PDO::PARAM_STR);
				$insertion->bindValue(':prenom', $prenom, PDO::PARAM_STR);
				$insertion->bindValue(':email', $email, PDO::PARAM_STR);
				$insertion->bindValue(':sexe', $sexe, PDO::PARAM_STR);
				$insertion->bindValue(':ville', $ville, PDO::PARAM_STR);
				$insertion->bindValue(':cp', $cp, PDO::PARAM_INT);
				$insertion->bindValue(':adresse', $adresse, PDO::PARAM_STR);
				$insertion->execute();

				$msg .= '<p class="alert alert-success" role="alert"><span class="glyphicon glyphicon-ok"></span> Inscription réussie !</p>';

				/*
				$profil = $insertion->fetch(PDO::FETCH_ASSOC);

      			foreach($profil as $key => $value) {
        			$_SESSION['utilisateur'][$key] = $value; // je rempli une session avec les infos du profil 
      			}
      
			    header('location: profil.php'); // si la connexion est OK, je le redirige vers sa page profil
			    exit();*/

				}

		} else {
		$msg .= '<p class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Erreur : </span>Veuillez remplir tous les champs !</p>';
		}
}

?>
<div class="content container-fluid">
	<?php if(!empty($msg)) echo $msg; ?>
	<div class="row centered-form">
		<div class="col-xs-12 col-sm-8 col-md-4 col-sm-offset-2 col-md-offset-4">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Inscription</h3>
				</div>
				<div class="panel-body">
					<form method="post" role="form">
						<div class="row">
							<div class="col-xs-6 col-sm-6 col-md-6">
								<div class="form-group">
									<input type="text" name="prenom" id="prenom" class="form-control input-sm" placeholder="Prénom"  value="<?php echo (!empty($_POST['prenom'])) ? $_POST['prenom'] : '';  ?>">
								</div>
							</div>
							<div class="col-xs-6 col-sm-6 col-md-6">
								<div class="form-group">
									<input type="text" name="nom" id="nom" class="form-control input-sm" placeholder="Nom"  value="<?php echo (!empty($_POST['nom'])) ? $_POST['nom'] : '';  ?>">
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-6 col-sm-6 col-md-6">
								<div class="form-group">
									<input type="text" name="ville" id="ville" class="form-control input-sm" placeholder="Ville"  value="<?php echo (!empty($_POST['ville'])) ? $_POST['ville'] : '';  ?>">
								</div>
							</div>
							<div class="col-xs-6 col-sm-6 col-md-6">
								<div class="form-group">
									<input type="text" name="cp" id="cp" class="form-control input-sm" placeholder="CP"  value="<?php echo (!empty($_POST['cp'])) ? $_POST['cp'] : '';  ?>">
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<textarea name="adresse" id="adresse" class="form-control input-sm" placeholder="Adresse"><?php echo (!empty($_POST['adresse'])) ? $_POST['adresse'] : '';  ?></textarea>
						</div>

						<div class="form-group">
							<input type="email" name="email" id="email" class="form-control input-sm" placeholder="Email"  value="<?php echo (!empty($_POST['email'])) ? $_POST['email'] : '';  ?>">
						</div>

						<div class="row">
							<div class="col-xs-6 col-sm-6 col-md-6">
								<div class="form-group">
									<input type="text" name="pseudo" id="pseudo" class="form-control input-sm" placeholder="Pseudo"  value="<?php echo (!empty($_POST['pseudo'])) ? $_POST['pseudo'] : '';  ?>">
								</div>
							</div>
							<div class="col-xs-6 col-sm-6 col-md-6">
								<div class="form-group">
									<input type="password" name="mdp" id="mdp" class="form-control input-sm" placeholder="Mot de passe"  value="<?php echo (!empty($_POST['mdp'])) ? $_POST['mdp'] : '';  ?>">
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-xs-6 col-sm-6 col-md-6">
								<div class="input-group">
									<span class="input-group-addon">
										<input id="femme" type="radio" name="sexe" value="f" <?php echo (!empty($sexe) && $sexe === 'f') ? 'checked="checked"' : '';  ?>>
									</span>
									<label for="femme" class="form-control">Femme</label>
								</div><!-- /input-group -->
							</div>

							<div class="col-xs-6 col-sm-6 col-md-6">
								<div class="input-group">
									<span class="input-group-addon">
										<input id="homme" type="radio" name="sexe" value="m" <?php echo (!empty($sexe) && $sexe === 'm') ? 'checked="checked"' : '';  ?>>
									</span>
									<label class="form-control" for="homme" >Homme</label>
								</div>
							</div>
						</div>

						<button style="margin-top: 15px;" type="submit" class="btn btn-info btn-block">Inscription</button>

					</form>
				</div>
			</div>
		</div>
	</div>
</div>


<?php
require_once('inc/footer.inc.php');