<?php 

session_start();

require("../inc/conf.php");
require("../class/note.php");
require("../class/user.php");

if(!isset($_SESSION["user"]) || $_SESSION["user"] == null) {
	echo json_encode(["success" => false, "message" => "Vous êtes déconnecté. Veuillez rafraîchir la page afin de vous reconnecter."]);
	exit;
}
$user = unserialize($_SESSION["user"]);

$success = true;
$message = "Modification réussie";

try {
	switch ($_POST["field"]) {
		case "author":
			$user->editAuthor($_POST["oldValue"], $_POST["newValue"]);
			break;
		case "source":
			$user->editSource($_POST["oldValue"], $_POST["newValue"]);
			break;
		case "category":
			$user->editCategory($_POST["oldValue"], $_POST["newValue"]);
			break;
		case "keyword":
			$user->editKeyword($_POST["oldValue"], $_POST["newValue"]);
			break;
		default: 
			throw new Exception("Champ de thesaurus inconnu : " . $_POST["field"]);
			break;
	}
}
catch (Exception $e) {
	$message = $e->getMessage();
	$success = false;
}

echo json_encode(["success" => $success, "message" => $message]);