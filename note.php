<?php 

session_start();

$title = "Ajouter";

require("inc/conf.php");
require("class/note.php");
require("class/user.php");

if(!isset($_SESSION["user"]) || $_SESSION["user"] == null) {
	header("Location: " . ROOTHTML . "/login");
	exit;
}
$user = unserialize($_SESSION["user"]);

if(isset($_GET["id"])) {
	$note = Note::load($_GET["id"]);
	$title = "Éditer";
}

?>
<?php include(ROOT . "/inc/meta.php"); ?>
</head>
<body>
	<?php include(ROOT . "/inc/nav.php"); ?>

	<main class="container-fluid">

		<section class="card">
			<header class="card-header">
				<?php if(isset($note)) { ?>
				<h2>Modifier une note</h2>
				<?php } else { ?>
				<h2>Ajouter une note</h2>
				<?php } ?>
			</header>
			<form class="card-body">

				<div class="input-group-vertical">
				  <label class="input-group-top" for="auteur">Auteur</label>
				  <input type="text" class="form-control" id="auteur" name="auteur">
				</div>

				<div class="input-group-vertical">
				  <label class="input-group-top" for="source">Source</label>
				  <input type="text" class="form-control" id="source" name="source">
				</div>

				<div class="input-group-vertical" id="categories-liste">
				  <label class="input-group-top" for="categories">Catégories</label>
				  <input type="text" class="form-control categories" id="categories">
				</div>
				<button class="btn btn-lg btn-warning" type="button" onclick="addCategoryField()">+</button>

				<div class="input-group-vertical" id="keywords-liste">
				  <label class="input-group-top" for="keywords">Mots-clés</label>
				  <input type="text" class="form-control keywords" id="keywords">
				</div>
				<button class="btn btn-lg btn-warning" type="button" onclick="addKeywordField()">+</button>

				<div class="input-group-vertical">
					<label class="input-group-top" for="note-texte">Texte</label>
					<textarea class="form-control" id="note-texte"></textarea>
				</div>

				<button class="btn btn-lg btn-success" type="button" onclick="saveNote()"><span class="bi-archive"></span> Enregistrer la note</button>

			</form>
		</section>

	</main>

	<script type="text/javascript">

	<?php if(isset($note)) { ?>
		var note = <?php echo json_encode($note); ?>;
		note.categories = <?php echo json_encode($note->getCategories()); ?>;
		note.keywords = <?php echo json_encode($note->getKeywords()); ?>;
		var id = note.id;
	<?php }
	else { ?>
		var note = null;
		var id = 0;
	<?php } ?>

	console.log(note);

	var authors = <?php echo json_encode(array_keys($user->loadAllAuthors())); ?>;
    console.log(authors);

	var sources = <?php echo json_encode(array_keys($user->loadAllSources())); ?>;
    console.log(sources);

    var categories = <?php echo json_encode(array_keys($user->loadAllCategories())); ?>;
    console.log(categories);

    var keywords = <?php echo json_encode(array_keys($user->loadAllKeywords())); ?>;
    console.log(keywords);

	$(document).ready(function() {

		$("#auteur").autocomplete({
			source: authors
    	});

		$("#source").autocomplete({
			source: sources
    	});

		$(".categories").autocomplete({
			source: categories
    	});

		$(".keywords").autocomplete({
			source: keywords
    	});

		$('#note-texte').summernote({
			toolbar: [
			    ['style', ['bold', 'italic', 'underline', 'clear']],
			    ['color', ['color']],
			    ['para', ['ul', 'ol']],
	  			['view', ['undo', 'redo', 'codeview']],
			]
		});

		if(note != null) {
			$("#auteur").val(note.author);
			$("#source").val(note.source);
			$("#note-texte").summernote('code', note.content);

			i = 0;
			note.categories.forEach(async function(categorie) {
				if(i > 0)
					addCategoryField();
				$(".categories").last().val(categorie);
				i ++;
			});

			i = 0;
			note.keywords.forEach(async function(categorie) {
				if(i > 0)
					addKeywordField();
				$(".keywords").last().val(categorie);
				i ++;
			});
		}
	});

	function addCategoryField() {
		$("#categories-liste").append('<input type="text" class="form-control categories">');

		$(".categories").autocomplete({
			source: categories
    	});
	}

	function addKeywordField() {
		$("#keywords-liste").append('<input type="text" class="form-control keywords">');

		$(".keywords").autocomplete({
			source: keywords
    	});
	}

	function saveNote() {
		var author = $("#auteur").val();
		var source = $("#source").val();
		var content = $("#note-texte").summernote('code');
		var categories = [];
		$(".categories").each(function() {
			categories.push($(this).val()); 
		});
		var keywords = [];
		$(".keywords").each(function() {
			keywords.push($(this).val()); 
		});

		$.ajax({
			url: "<?php echo ROOTHTML; ?>/api/note_save.php",
			type: "POST",
			data: {
				id: id,
				author: author,
				source: source,
				content: content,
				categories: categories,
				keywords: keywords
			},

			success: function(data, status, jqXHR) {
				console.log(data);
				data = $.parseJSON(data);
				if (data.success) {
					toastr.success(data.message);
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

	</script>

</body>
</html>