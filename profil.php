<?php
// config
require_once('inc/init.inc.php');

if(empty($_SESSION['utilisateur'])) { // si la session utilisateur est vide, cela signifie que la personne n'est pas connectée DONC redirection... vers connexion.php
	header('location: connexion.php');
	exit();
}

// affichage
$pageCourant = 'Profil';
require_once('inc/header.inc.php');

?>


<div class="content container-fluid">
	<h1>Bonjour <?= $_SESSION['utilisateur']['prenom'] ?> <?= $_SESSION['utilisateur']['nom'] ?></h1>

	<h2>Voici vos informations :</h2>

	<p><strong>Email</strong></p>
	<p><?= $_SESSION['utilisateur']['email'] ?></p>

	<p><strong>Pseudo</strong></p>
	<p><?= $_SESSION['utilisateur']['pseudo'] ?></p>

	<address>
  	<strong>Adresse</strong><br>
  	<?= $_SESSION['utilisateur']['adresse'] ?><br>
  	<?= $_SESSION['utilisateur']['cp'] ?> <?= $_SESSION['utilisateur']['ville'] ?><br>
	</address>

</div>	


<?php
require_once('inc/footer.inc.php');