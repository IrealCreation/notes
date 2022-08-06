<?php

// Rename this file "conf.php" and modify the following constants according to your own server configuration

define("DB_NAME", "");

define("DB_USER", "");

define("DB_PASSWORD", "");

define("ROOT", $_SERVER["DOCUMENT_ROOT"]);

define("ROOTHTML", "");

define("SERVER", "https://" . $_SERVER['HTTP_HOST']);

$pdo = new PDO('mysql:dbname=' . DB_NAME . ';host=localhost', DB_USER, DB_PASSWORD);