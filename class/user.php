<?php

class User {

	public int $id;

	public string $login; // Varchar 50

	public string $mail; // Varchar 50

	private string $_password; // Varchar 255

	public string $signin; // Date et heure d'inscription. Datetime


	function __construct() {
		// Default id is 0. An object with id 0 means it cannot be loaded from the database.
		$this->id = 0;
	}

	private function setPassword($password) {
		$this->_password = $password;
	}

	function hashPassword($password) {
		$this->_password = password_hash($password, PASSWORD_DEFAULT);
	}

	function store() {
		// Ne peut être utilisé que pour la première création, utiliser sinon les fonctions d'Update
		global $pdo;

		if($this->id === 0) {
			$sql = "INSERT INTO user (login, mail, password, signin)
				VALUES (:login, :mail, :password, :signin)";

			$statement = $pdo->prepare($sql);
			
			$statement->bindParam(':login', $this->login);
			$statement->bindParam(':mail', $this->mail);
			$statement->bindParam(':password', $this->_password);
			$date = date("Y-m-d H:i:s");
			$statement->bindParam(':signin', $date);

			$statement->execute();

			if($statement->errorInfo()[0] != '00000') {
				throw new Exception($statement->errorInfo());
			}
		}
		else {
			throw new Exception('User->store(): storing an already existing user');
		}
	}

	function updateLogin($new) {

	}

	function updatePassword($new) {

	}

	function getNotes() : array {
		global $pdo;

		$notes = array();

		// $sql = "SELECT note.id as id, author, source, content, add_datetime, delete_datetime, GROUP_CONCAT(DISTINCT category SEPARATOR ';') as categories, GROUP_CONCAT(DISTINCT keyword SEPARATOR ';') as keywords 
		// 	FROM note, category, keyword 
		// 	WHERE user_id = :user AND category.note_id = note.id AND keyword.note_id = note.id AND delete_datetime IS NULL GROUP BY note.id";

		$sql = "SELECT note.id as id, author, source, content, add_datetime, delete_datetime, GROUP_CONCAT(DISTINCT category SEPARATOR ';') as categories, GROUP_CONCAT(DISTINCT keyword SEPARATOR ';') as keywords 
			FROM note
			LEFT JOIN category ON category.note_id = note.id
			LEFT JOIN keyword ON keyword.note_id = note.id 
			WHERE user_id = :user AND delete_datetime IS NULL GROUP BY note.id ORDER BY id DESC";

		$statement = $pdo->prepare($sql);
		
		$statement->bindParam(':user', $this->id);

		$statement->execute();

		if($statement->errorInfo()[0] != '00000') {
			throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
		}

		return Note::NotesFromPDO($statement);
	}

	function searchNotes(array $filters) : array {
		global $pdo;

		$notes = array();

		$where = "";

		if(isset($filters["author"])) {
			$where.= "author LIKE CONCAT( '%', :author, '%')";
		}
		if(isset($filters["source"])) {
			if($where != "")
				$where .= " AND ";
			$where.= "source LIKE CONCAT( '%', :source, '%')";
		}
		if(isset($filters["content"])) {
			if($where != "")
				$where .= " AND ";
			$where.= "content LIKE CONCAT( '%', :content, '%')";
		}
		if(isset($filters["keyword"])) {
			if($where != "")
				$where .= " AND ";
			$where.= "keyword LIKE CONCAT( '%', :keyword, '%')";
		}
		if(isset($filters["category"]) && $filters["category"] != "") {
			if($where != "")
				$where .= " AND ";
			$where.= "category = :category";
		}

		$sql = "SELECT note.id as id, author, source, content, add_datetime, delete_datetime, GROUP_CONCAT(DISTINCT category SEPARATOR ';') as categories, GROUP_CONCAT(DISTINCT keyword SEPARATOR ';') as keywords 
			FROM note
			LEFT JOIN category ON category.note_id = note.id
			LEFT JOIN keyword ON keyword.note_id = note.id 
			WHERE ";
		$sql .= $where;
		$sql .= " AND user_id = :user AND delete_datetime IS NULL GROUP BY note.id ORDER BY id DESC";

		$statement = $pdo->prepare($sql);
		$statement->bindParam(':user', $this->id);

		if(isset($filters["author"])) {
			$statement->bindParam(':author', $filters["author"], PDO::PARAM_STR);
		}
		if(isset($filters["source"])) {
			$statement->bindParam(':source', $filters["source"], PDO::PARAM_STR);
		}
		if(isset($filters["content"])) {
			$statement->bindParam(':content', $filters["content"], PDO::PARAM_STR);
		}
		if(isset($filters["keyword"])) {
			$statement->bindParam(':keyword', $filters["keyword"], PDO::PARAM_STR);
		}
		if(isset($filters["category"]) && $filters["category"] != "") {
			$statement->bindParam(':category', $filters["category"], PDO::PARAM_STR);
		}

		$statement->execute();

		//$statement->debugDumpParams();

		if($statement->errorInfo()[0] != '00000') {
			throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
		}

		return Note::NotesFromPDO($statement);
	}

	function loadAllAuthors() : array {
		global $pdo;
		$authors = array();

		$sql = "SELECT author, COUNT(author) as count FROM note WHERE note.user_id = :user AND delete_datetime IS NULL GROUP BY author ORDER BY count DESC";
		$statement = $pdo->prepare($sql);
		$statement->bindParam(':user', $this->id);
		$statement->execute();

		if($statement->errorInfo()[0] != '00000') {
			throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
		}

		while($result = $statement->fetch(PDO::FETCH_ASSOC)) {
			$authors[$result["author"]] = $result["count"];
		}
		return $authors;
	}

	function loadAllSources() : array {
		global $pdo;
		$sources = array();

		$sql = "SELECT source, COUNT(source) as count FROM note WHERE note.user_id = :user AND delete_datetime IS NULL GROUP BY source ORDER BY count DESC";
		$statement = $pdo->prepare($sql);
		$statement->bindParam(':user', $this->id);
		$statement->execute();

		if($statement->errorInfo()[0] != '00000') {
			throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
		}

		while($result = $statement->fetch(PDO::FETCH_ASSOC)) {
			$sources[$result["source"]] = $result["count"];
		}
		return $sources;
	}

	function loadAllCategories() : array {
		global $pdo;
		$categories = array();

		// $sql = "SELECT DISTINCT category FROM category INNER JOIN note ON category.note_id = note.id AND note.user_id = :user";
		$sql = "SELECT category, COUNT(category) as count FROM category INNER JOIN note ON category.note_id = note.id AND note.user_id = :user AND delete_datetime IS NULL GROUP BY category ORDER BY count DESC";
		$statement = $pdo->prepare($sql);
		$statement->bindParam(':user', $this->id);
		$statement->execute();

		if($statement->errorInfo()[0] != '00000') {
			throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
		}

		while($result = $statement->fetch(PDO::FETCH_ASSOC)) {
			$categories[$result["category"]] = $result["count"];
		}
		return $categories;
	}

	function loadAllKeywords() : array {
		global $pdo;
		$keywords = array();

		$sql = "SELECT keyword, COUNT(keyword) as count FROM keyword INNER JOIN note ON keyword.note_id = note.id AND note.user_id = :user AND delete_datetime IS NULL GROUP BY keyword ORDER BY count DESC";
		$statement = $pdo->prepare($sql);
		$statement->bindParam(':user', $this->id);
		$statement->execute();

		if($statement->errorInfo()[0] != '00000') {
			throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
		}

		while($result = $statement->fetch(PDO::FETCH_ASSOC)) {
			$keywords[$result["keyword"]] = $result["count"];
		}
		return $keywords;
	}

	function editAuthor($oldValue, $newValue) {
		global $pdo;
		$sql = "UPDATE note SET author = :newValue WHERE author = :oldValue AND note.user_id = :user";
		$statement = $pdo->prepare($sql);
		$statement->bindParam(':oldValue', $oldValue);
		$statement->bindParam(':newValue', $newValue);
		$statement->bindParam(':user', $this->id);
		$statement->execute();

		if($statement->errorInfo()[0] != '00000') {
			throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
		}
	}

	function editSource($oldValue, $newValue) {
		global $pdo;
		$sql = "UPDATE note SET source = :newValue WHERE source = :oldValue AND note.user_id = :user";
		$statement = $pdo->prepare($sql);
		$statement->bindParam(':oldValue', $oldValue);
		$statement->bindParam(':newValue', $newValue);
		$statement->bindParam(':user', $this->id);
		$statement->execute();

		if($statement->errorInfo()[0] != '00000') {
			throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
		}
	}

	function editCategory($oldValue, $newValue) {
		global $pdo;
		$sql = "UPDATE category INNER JOIN note ON category.note_id = note.id SET category = :newValue WHERE category = :oldValue AND note.user_id = :user";
		$statement = $pdo->prepare($sql);
		$statement->bindParam(':oldValue', $oldValue);
		$statement->bindParam(':newValue', $newValue);
		$statement->bindParam(':user', $this->id);
		$statement->execute();

		if($statement->errorInfo()[0] != '00000') {
			throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
		}
	}

	function editKeyword($oldValue, $newValue) {
		global $pdo;
		$sql = "UPDATE keyword INNER JOIN note ON keyword.note_id = note.id SET keyword = :newValue WHERE keyword = :oldValue AND note.user_id = :user";
		$statement = $pdo->prepare($sql);
		$statement->bindParam(':oldValue', $oldValue);
		$statement->bindParam(':newValue', $newValue);
		$statement->bindParam(':user', $this->id);
		$statement->execute();

		if($statement->errorInfo()[0] != '00000') {
			throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
		}
	}

	static function load($id) : User {
		throw new Exception('Not implemented');
	}

	static function connexion($login, $password) : bool {
		global $pdo;

		$sql = "SELECT id, login, mail, password, signin FROM user WHERE login = :login";

		$statement = $pdo->prepare($sql);
		
		$statement->bindParam(':login', $login);

		$statement->execute();

		while($result = $statement->fetch(PDO::FETCH_ASSOC)) {
			if(password_verify($password, $result['password'])) {
				$user = new User();
				$user->id = $result['id'];
				$user->login = $result['login'];
				$user->mail = $result['mail'];
				$user->setPassword($result['password']);
				$user->signin = $result['signin'];

				$_SESSION["user"] = serialize($user);
				return true;
			}
		}
		return false;
		// Connexion failed: redirect to the login page
		header('Location: ' . ROOTHTML . "/login");
		exit;

	}

}