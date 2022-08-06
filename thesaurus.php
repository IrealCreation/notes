<?php 

session_start();

$title = "Thésaurus";

require("inc/conf.php");
require("inc/util.php");
require("class/note.php");
require("class/user.php");

if(!isset($_SESSION["user"]) || $_SESSION["user"] == null) {
	header("Location: " . ROOTHTML . "/login");
	exit;
}
$user = unserialize($_SESSION["user"]);

$categories = $user->loadAllCategories();
$keywords = $user->loadAllKeywords();
$authors = $user->loadAllAuthors();
$sources = $user->loadAllSources();

?><?php include(ROOT . "/inc/meta.php"); ?>
</head>
<body>
	<?php include(ROOT . "/inc/nav.php"); ?>

	<main class="container-fluid">

		<section class="card">
			<header class="card-header">
				<h2>Catégories</h2>
			</header>
			<div class="card-body">
				<table class="thesaurus-table">
					<tr>
						<th>Intitulé</th>
						<th>Nombre de notes</th>
						<th>Éditer</th>
					</tr>
					<?php foreach($categories as $categorie => $count) { ?>
						<tr>
								<td><a href="<?= ROOTHTML ?>/categorie/<?= $categorie ?>"><?= $categorie ?></a></td>
								<td><?= $count ?></td>
								<td><button class="btn btn-warning"><span class="bi-pencil"></span></button></td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</section>

		<section class="card">
			<header class="card-header">
				<h2>Mots-clés</h2>
			</header>
			<div class="card-body">
				<table class="thesaurus-table">
					<tr>
						<th>Intitulé</th>
						<th>Nombre de notes</th>
						<th>Éditer</th>
					</tr>
					<?php foreach($keywords as $keyword => $count) { ?>
						<tr>
								<td><a href="<?= ROOTHTML ?>/keyword/<?= $keyword ?>"><?= $keyword ?></a></td>
								<td><?= $count ?></td>
								<td><button class="btn btn-warning"><span class="bi-pencil"></span></button></td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</section>

		<section class="card">
			<header class="card-header">
				<h2>Auteurs</h2>
			</header>
			<div class="card-body">
				<table class="thesaurus-table">
					<tr>
						<th>Intitulé</th>
						<th>Nombre de notes</th>
						<th>Éditer</th>
					</tr>
					<?php foreach($authors as $author => $count) { ?>
						<tr>
								<td><a href="<?= ROOTHTML ?>/author/<?= $author ?>"><?= $author ?></a></td>
								<td><?= $count ?></td>
								<td><button class="btn btn-warning"><span class="bi-pencil"></span></button></td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</section>

		<section class="card">
			<header class="card-header">
				<h2>Sources</h2>
			</header>
			<div class="card-body">
				<table class="thesaurus-table">
					<tr>
						<th>Intitulé</th>
						<th>Nombre de notes</th>
						<th>Éditer</th>
					</tr>
					<?php foreach($sources as $source => $count) { ?>
						<tr>
								<td><a href="<?= ROOTHTML ?>/source/<?= $source ?>"><?= $source ?></a></td>
								<td><?= $count ?></td>
								<td><button class="btn btn-warning"><span class="bi-pencil"></span></button></td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</section>

	</main>

	<script type="text/javascript">

		function deleteNote(id, auteur) {
			if(confirm("Confirmer la mise à la corbeille de la note n°" + id + " (auteur : " + auteur + ")")) {
				$.ajax({
					url: "<?php echo ROOTHTML; ?>/api/note_delete.php",
					type: "POST",
					data: {
						id: id,
					},

					success: function(data, status, jqXHR) {
						console.log(data);
						data = $.parseJSON(data);
						if (data.success) {
							toastr.success(data.message);
							location.reload();
						}
						else {
							toastr.error(data.message);
						}
					},
					error: function(data, status, jqXHR) {
						alert("Impossible d'établir la connexion");
					},
				});
			}
		}

	</script>
</body>
</html>