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
$message = "Suppression réussie";

try {
	$note = Note::load($_POST["id"]);
	$note->delete();
}
catch (Exception $e) {
	$message = $e->getMessage();
	$success = false;
}

echo json_encode(["success" => $success, "message" => $message]);