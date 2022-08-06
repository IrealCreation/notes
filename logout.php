<?php 

session_start();
session_destroy();
session_start();

$title = "Déconnexion";

require("inc/conf.php");

?>
<?php include(ROOT . "/inc/meta.php"); ?>
</head>
<body>
	<?php include(ROOT . "/inc/nav.php"); ?>

	<main class="container-fluid">

		<section class="card">
			<header class="card-header">
				<h2>Déconnexion</h2>
			</header>
			<div class="card-body">

				<p>Vous êtes désormais déconnecté(e).</p>

				<a href="<?= ROOTHTML ?>/login"><button class="btn btn-lg btn-success" type="button"><span class="bi-door-open"></span> Se connecter</button></a>

			</div>
		</section>

	</main>

</body>
</html>