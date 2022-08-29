<?php 

session_start();

require("../inc/conf.php");
require("../class/note.php");
require("../class/user.php");

if(!isset($_SESSION["user"]) || $_SESSION["user"] == null) {
	echo json_encode(["success" => false, "message" => "Vous êtes déconnecté. Veuillez ouvrir un nouvel onglet pour vous reconnecter, puis enregistrer la note."]);
	exit;
}
$user = unserialize($_SESSION["user"]);

$success = true;
$message = "Enregistrement réussi";

try {
	$note = new Note($_POST["id"], $_POST["author"], $_POST["source"], $_POST["content"], $_POST["categories"], $_POST["keywords"]);
	$note->store();
}
catch (Exception $e) {
	$message = $e->getMessage();
	$success = false;
}

echo json_encode(["success" => $success, "message" => $message]);