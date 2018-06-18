<nav class="collapse navbar-collapse col-md-11">
				<ul class="nav navbar-nav">
					<li><a href="<?= RACINE_SITE ?>/index.php">Accueil</a></li>
					<?php if(userConnected()) : ?>
					    <li><a href="<?= RACINE_SITE ?>/profil.php">Profil</a></li>
					    <li><a href="<?= RACINE_SITE ?>/boutique.php">Boutique</a></li>
					    <li><a href="<?= RACINE_SITE ?>/panier.php">Panier</a></li>
					    <li><a href="<?= RACINE_SITE ?>/connexion.php?action=logout">Se déconnecter</a></li>
					<?php else : ?>
						<li><a href="<?= RACINE_SITE ?>/boutique.php">Boutique</a></li>
					    <li><a href="<?= RACINE_SITE ?>/panier.php">Panier</a></li>
					    <li><a href="<?= RACINE_SITE ?>/inscription.php">Inscription</a></li>
					    <li><a href="<?= RACINE_SITE ?>/connexion.php">Connexion</a></li>
					<?php endif; ?>
					<?php if (userAdmin()) : ?>
					    <li class="dropdown">
					    	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Admin <span class="caret"></span></a>
						    <ul class="dropdown-menu" role="menu">
							    <li><a href="<?= RACINE_SITE ?>/admin/gestion_membres.php">Gestion des membres</a></li>
							    <li><a href="<?= RACINE_SITE ?>/admin/gestion_commandes.php">Gestion des commandes</a></li>
							    <li><a href="<?= RACINE_SITE ?>/admin/gestion_boutique.php">Gestion de la boutique</a></li>
							</ul>
						</li>
					<?php endif; ?>
				</ul>
			</nav>	