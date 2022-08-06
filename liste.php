<?php 

session_start();

$title = "Liste";

require("inc/conf.php");
require("inc/util.php");
require("class/note.php");
require("class/user.php");

if(!isset($_SESSION["user"]) || $_SESSION["user"] == null) {
	header("Location: " . ROOTHTML . "/login");
	exit;
}
$user = unserialize($_SESSION["user"]);

$notes = array();
$filters = array();
$current_filter = "";

if(isset($_GET["auteur"]) || (isset($_GET["dans"]) && $_GET["dans"] == "auteur")) {
	//$notes = array_merge($notes, $user->getNotesByAuthor($_GET["auteur"]));
	$get = $_GET["auteur"] ?? $_GET["contenu"];

	$filters["author"] = $get;
	$current_filter .= "Auteur = " . $get;
}
if(isset($_GET["texte"]) || (isset($_GET["dans"]) && $_GET["dans"] == "texte")) {
	// $notes = array_merge($notes, $user->getNotesBySource($_GET["source"]));
	$get = $_GET["texte"] ?? $_GET["contenu"];
	if($current_filter != "")
		$current_filter .= " ; ";

	$filters["content"] = $get;
	$current_filter .= "Texte = " . $get;
}
if(isset($_GET["source"]) || (isset($_GET["dans"]) && $_GET["dans"] == "source")) {
	// $notes = array_merge($notes, $user->getNotesBySource($_GET["source"]));
	$get = $_GET["source"] ?? $_GET["contenu"];
	if($current_filter != "")
		$current_filter .= " ; ";

	$filters["source"] = $get;
	$current_filter .= "Source = " . $get;
}
if((isset($_GET["categorie"]) && $_GET["categorie"] != "") || (isset($_GET["dans"]) && $_GET["dans"] == "categorie")) {
	// $notes = array_merge($notes, $user->getNotesByCategory($_GET["categorie"]));
	$get = $_GET["categorie"] ?? $_GET["contenu"];
	if($current_filter != "")
		$current_filter .= " ; ";

	$filters["category"] = $get;
	$current_filter .= "Catégorie = " . $get;
}
if(isset($_GET["motcle"]) || (isset($_GET["dans"]) && $_GET["dans"] == "motcle")) {
	// $notes = array_merge($notes, $user->getNotesByKeyword($_GET["motcle"]));
	$get = $_GET["motcle"] ?? $_GET["contenu"];
	if($current_filter != "")
		$current_filter .= " ; ";

	$filters["keyword"] = $get;
	$current_filter .= "Mot-clé = " . $get;
}

if(count($filters) == 0) {
	$notes = $user->getNotes();
}
else {
	$notes = $user->searchNotes($filters);
}

$categories = $user->loadAllCategories();

?><?php include(ROOT . "/inc/meta.php"); ?>
</head>
<body>
	<?php include(ROOT . "/inc/nav.php"); ?>

	<main class="container-fluid">

		<section class="card">
			<header class="card-header">
				<h2>Filtrer les notes</h2>
			</header>
			<form class="card-body" method="GET" action="<?= ROOTHTML ?>/recherche">
				<?php if($current_filter != "") { ?>
				<p>Filtre actuel : <?= $current_filter ?></p>
				<?php } ?>
				<div class="input-group">
					<label class="input-group-text" for="categories">Catégorie</label>
					<select id="categories" name="categorie" class="form-control">
						<option value="">Toutes</option>
						<?php foreach($categories as $categorie => $count) { ?>
							<option value="<?= toSafeValue($categorie) ?>"><?= $categorie ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="input-group-vertical">
							<label class="input-group-top" for="recherche-contenu">Rechercher...</label>
							<input type="text" class="form-control" id="recherche-contenu" name="contenu" value="<?php echo $_GET["contenu"] ?? '' ?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="input-group-vertical">
							<label class="input-group-top" for="recherche-dans">... dans...</label>
							<select id="recherche-dans" name="dans" class="form-control">
								<option value="">---</option>
								<option value="texte" <?php if(isset($_GET["dans"]) && $_GET["dans"] == "texte") echo "selected"; ?>>Texte</option>
								<option value="auteur" <?php if(isset($_GET["dans"]) && $_GET["dans"] == "auteur") echo "selected"; ?>>Auteur</option>
								<option value="source" <?php if(isset($_GET["dans"]) && $_GET["dans"] == "source") echo "selected"; ?>>Source</option>
								<option value="motcle" <?php if(isset($_GET["dans"]) && $_GET["dans"] == "motcle") echo "selected"; ?>>Mots-clés</option>
							</select>
						</div>
					</div>
				</div>

				<button class="btn btn-lg btn-success" type="submit" onclick=""><span class="bi-search"></span> Rechercher</button>
			</form>
		</section>

		<?php foreach($notes as $note) { 
			echo $note->showCard();
		} ?>

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