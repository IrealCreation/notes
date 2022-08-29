<?php 

session_start();
$_SESSION["user"] = null;

$title = "Connexion";
$error = "";

require("inc/conf.php");
require("class/user.php");

if(isset($_POST["identifiant"]) && isset($_POST["password"])) {
	if(User::connexion($_POST["identifiant"], $_POST["password"])) {
		header("Location: " . ROOTHTML . "/liste");
	}
	else {
		$error = "Nom d'utilisateur ou mot de passe incorrect";
	}
}

?>
<?php include(ROOT . "/inc/meta.php"); ?>
</head>
<body>
	<?php include(ROOT . "/inc/nav.php"); ?>

	<main class="container-fluid">

		<section class="card">
			<header class="card-header">
				<h2>Connexion</h2>
			</header>

			<form class="card-body" method="POST" action="<?= ROOTHTML ?>/login">

				<?php if($error != "") { ?>
					<div class="alert alert-danger"><?= $error ?></div>
				<?php } ?>

				<div class="input-group-vertical">
				  <label class="input-group-top" for="identifiant">Identifiant</label>
				  <input type="text" class="form-control" id="identifiant" name="identifiant">
				</div>

				<div class="input-group-vertical">
				  <label class="input-group-top" for="password">Mot de passe</label>
				  <input type="password" class="form-control" id="password" name="password">
				</div>

				<button class="btn btn-lg btn-success" type="submit"><span class="bi-check-circle"></span> Se connecter</button>

			</form>
		</section>

	</main>

</body>
</html>