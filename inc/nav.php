	<nav class="navbar sticky-top navbar-dark bg-dark">
		<div class="container-fluid">
			<h1 class="navbar-brand">Notes</h1>

		    <?php if(isset($user)) { ?>
			<ul class="navbar-nav">
		        <li class="nav-item">
		        	<a class="nav-link <?php if($title == 'Ajouter' || $title == 'Éditer') echo 'active'; ?>" href="<?= ROOTHTML ?>/note">Ajouter</a>
		        </li>
		        <li class="nav-item">
		        	<a class="nav-link <?php if($title == 'Liste') echo 'active'; ?>" href="<?= ROOTHTML ?>/liste">Consulter</a>
		        </li>
		        <li class="nav-item">
		        	<a class="nav-link <?php if($title == 'Thésaurus') echo 'active'; ?>" href="<?= ROOTHTML ?>/thesaurus">Thésaurus</a>
		        </li>
		    </ul>
		    <ul class="navbar-nav">
		    	<li>
		    		<a class="nav-link" href="#"><?= $user->login; ?></a>
		    	</li>
		    	<li>
		    		<a class="nav-link" href="<?= ROOTHTML ?>/logout">Déconnexion</a>
		    	</li>
		    </ul>
		    <?php }
		    else { ?>
		    <ul class="navbar-nav">
		        <li class="nav-item">
		        	<a class="nav-link <?php if($title == 'Connexion') echo 'active'; ?>" href="<?= ROOTHTML ?>/login">Connexion</a>
		        </li>
		    </ul>
		    <?php } ?>
		</div>
	</nav>