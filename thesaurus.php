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
			<nav class="navbar navbar-inner">
				<ul class="navbar-nav">
			        <li class="nav-item">
			        	<a class="nav-link " href="#categories">Catégories</a>
			        </li>
			        <li class="nav-item">
			        	<a class="nav-link " href="#motscles">Mots-clés</a>
			        </li>
			        <li class="nav-item">
			        	<a class="nav-link " href="#auteurs">Auteurs</a>
			        </li>
			        <li class="nav-item">
			        	<a class="nav-link " href="#sources">Sources</a>
			        </li>
			    </ul>
			</nav>
		</section>

		<section class="card" id="categories">
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
					<?php foreach($categories as $category => $count) { ?>
						<tr>
								<td><a href="<?= ROOTHTML ?>/categorie/<?= $category ?>"><?= $category ?></a></td>
								<td><?= $count ?></td>
								<td>
									<form onsubmit="return editThesaurus(this, 'category', '<?= $category ?>');">
										<input type="text" placeholder="Nouvelle valeur..." name="newValue" class="form-control"><button class="btn btn-warning" type="submit"><span class="bi-pencil"></span></button>
									</form>
								</td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</section>

		<section class="card" id="motscles">
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
								<td><a href="<?= ROOTHTML ?>/motcle/<?= $keyword ?>"><?= $keyword ?></a></td>
								<td><?= $count ?></td>
								<td>
									<form onsubmit="return editThesaurus(this, 'keyword', '<?= $keyword ?>');">
										<input type="text" placeholder="Nouvelle valeur..." name="newValue" class="form-control"><button class="btn btn-warning" type="submit"><span class="bi-pencil"></span></button>
									</form>
								</td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</section>

		<section class="card" id="auteurs">
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
								<td><a href="<?= ROOTHTML ?>/auteur/<?= $author ?>"><?= $author ?></a></td>
								<td><?= $count ?></td>
								<td>
									<form onsubmit="return editThesaurus(this, 'author', '<?= $author ?>');">
										<input type="text" placeholder="Nouvelle valeur..." name="newValue" class="form-control"><button class="btn btn-warning" type="submit"><span class="bi-pencil"></span></button>
									</form>
								</td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</section>

		<section class="card" id="sources">
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
								<td>
									<form onsubmit="return editThesaurus(this, 'source', '<?= $source ?>');">
										<input type="text" placeholder="Nouvelle valeur..." name="newValue" class="form-control"><button class="btn btn-warning" type="submit"><span class="bi-pencil"></span></button>
									</form>
								</td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</section>

	</main>

	<script type="text/javascript">

		function editThesaurus(form, field, oldValue) {
			const UIText = {
				"category": "la catégorie",
				"keyword": "le mot-clé",
				"author": "l'auteur",
				"source": "la source"
			}

			var newValue = form.elements["newValue"].value;
			console.log(newValue);

			if(newValue != null && newValue != "")
			{
				if(confirm('Confirmer le remplacement de ' + UIText[field] + ' "' + oldValue +'"' + ' par "' + newValue +'" ?')) {
					$.ajax({
						url: "<?php echo ROOTHTML; ?>/api/thesaurus_edit.php",
						type: "POST",
						data: {
							field: field,
							oldValue: oldValue,
							newValue: newValue,
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
							console.log(data);
							alert("Impossible d'établir la connexion");
						},
					});
				}
			}
			return false;
		}

	</script>
</body>
</html>