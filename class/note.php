<?php

class Note {

	public int $id;

	public string $author; // Varchar 255. Nullable

	public string $source; // Varchar 255. Nullable

	public string $content;

	public string $add_datetime; // Datetime. Default current

	public string $delete_datetime; // Datetime. Nullable

	private array $_categories; // Varchar 255.

	private array $_keywords; // Varchar 255.


	function __construct(int $id, string $author, string $source, string $content, array $categories, array $keywords, string $add_datetime = null, string $delete_datetime = null) {
		$this->id = $id;
		$this->author = ($author !== null ? trim($author) : "");
		$this->source = ($source !== null ? trim($source) : "");
		$this->content = ($content !== null ? $content : "");
		$this->_categories = array();
		if($categories !== null) {
			foreach($categories as $category) {
				$this->_categories[] = trim($category);
			}
		}
		$this->_keywords = array();
		if($categories !== null) {
			foreach($keywords as $keyword) {
				$this->_keywords[] = trim($keyword);
			}
		}
	}

	function store() {
		global $pdo;
		global $user;

		if($this->id == 0) {
			// Premier enregistrement (INSERT) de cette note

			$sql = "INSERT INTO note (author, source, content, user_id)
				VALUES (:author, :source, :content, :user)";

			$statement = $pdo->prepare($sql);
			
			$statement->bindParam(':author', $this->author);
			$statement->bindParam(':source', $this->source);
			$statement->bindParam(':content', $this->content);
			$statement->bindParam(':user', $user->id);

			$statement->execute();

			if($statement->errorInfo()[0] != '00000') {
				throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
			}

			$this->id = $pdo->lastInsertId();

			$this->insertCategories();
			$this->insertKeywords();
		}
		else {
			// Ré-enregistrement (UPDATE) de cette note

			$sql = "UPDATE note 
				SET author = :author, source = :source, content = :content 
				WHERE id = :id AND user_id = :user";

			$statement = $pdo->prepare($sql);
			
			$statement->bindParam(':author', $this->author);
			$statement->bindParam(':source', $this->source);
			$statement->bindParam(':content', $this->content);
			$statement->bindParam(':id', $this->id);
			$statement->bindParam(':user', $user->id);

			$statement->execute();

			if($statement->errorInfo()[0] != '00000') {
				throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
			}

			//Update categories and keywords.

			$sql = "DELETE FROM category 
				WHERE note_id = :note";

			$statement = $pdo->prepare($sql);
			$statement->bindParam(':note', $this->id);
			$statement->execute();

			if($statement->errorInfo()[0] != '00000') {
				throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
			}

			$this->insertCategories();

			$sql = "DELETE FROM keyword 
				WHERE note_id = :note";

			$statement = $pdo->prepare($sql);
			$statement->bindParam(':note', $this->id);
			$statement->execute();

			if($statement->errorInfo()[0] != '00000') {
				throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
			}

			$this->insertKeywords();

		}
	}

	public function getCategories() : array {
		return $this->_categories;
	}

	public function setCategories(array $categories) {
		if($categories != $this->_categories) {
			$this->_categories = $categories;
			$_categoriesHaveChanged = true;
		}
	}

	private function insertCategories() {
		global $pdo;

		if(count($this->_categories) > 0) {
			foreach($this->_categories as $category) {
				$category = trim($category);
				if($category == "")
					continue;

				$sql = "INSERT INTO category (note_id, category)
					VALUES (:note, :category)";

				$statement = $pdo->prepare($sql);
				
				$statement->bindParam(':note', $this->id);
				$statement->bindParam(':category', $category);

				$statement->execute();

				if($statement->errorInfo()[0] != '00000') {
					throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
				}
			}
		}
	}

	public function getKeywords() : array {
		return $this->_keywords;
	}

	public function setKeywords(array $keywords) {
		if($keywords != $this->_keywords) {
			$this->_keywords = $keywords;
			$_keywordsHaveChanged = true;
		}
	}

	private function insertKeywords() {
		global $pdo;
		
		if(count($this->_keywords) > 0) {
			foreach($this->_keywords as $keyword) {
				$keyword = trim($keyword);
				if($keyword == "")
					continue;

				$sql = "INSERT INTO keyword (note_id, keyword)
					VALUES (:note, :keyword)";

				$statement = $pdo->prepare($sql);
				
				$statement->bindParam(':note', $this->id);
				$statement->bindParam(':keyword', $keyword);

				$statement->execute();

				if($statement->errorInfo()[0] != '00000') {
					throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
				}
			}
		}
	}

	function showCard() : string {
		$card = '<div class="card note-card">';
		$card .= '<div class="card-header">';
		$card .= '<h3><a class="auteur" href="' . ROOTHTML . '/auteur/' . toSafeValue($this->author) .'">' . $this->author . '</a></span>, <a class="source" href="' . ROOTHTML . '/source/' . toSafeValue($this->source) .'">' . $this->source . '</a></h3>';
		$card .= '<div class="card-buttons"><a class="edit" href="' . ROOTHTML . '/note/' . $this->id .'"><span class="bi-pencil-fill" title="Modifier"></span></a>';
		$card .= '<a class="delete" href="#" onclick="deleteNote(' . $this->id . ', \'' . $this->author . '\')"><span class="bi-trash" title="Supprimer"></span></a></div></div>';
		$card .= '<div class="card-body">';
		$content = $this->content;
		if(strlen($this->content) > 500) {
			$content = substr($this->content, 0, 500) . "...";
		}
		$card .= '<p class="content">' . $content . "</p>";
		$card .= '<div class="card-bottom">';
		if(count($this->_categories) > 0) {
			$card .= '<ul class="categories">';
			$count = 0;
			foreach($this->_categories as $category) {
				if($count > 0)
					$card .= ",&nbsp;</li>";
				$card .= '<li><a href="' . ROOTHTML . '/categorie/' . toSafeValue($category) .'"><span class="bi-bookmark" title="Catégorie"></span>' . $category . "</a>";
				$count ++;
			}
			$card .= "</li></ul>";
		}
		if(count($this->_keywords) > 0) {
			$card .= '<ul class="categories">';
			$count = 0;
			foreach($this->_keywords as $keyword) {
				if($count > 0)
					$card .= ",&nbsp;</li>";
				$card .= '<li><a href="' . ROOTHTML . '/motcle/' . toSafeValue($keyword) .'"><span class="bi-tag" title="Mot-clé"></span>' . $keyword . "</a>";
				$count ++;
			}
			$card .= "</li></ul>";
		}
		$card .= "</div>";
		$card .= "</div>";
		$card .= "</div>";

		return $card;
	}

	function delete() {
		global $pdo;
		global $user;

		$sql = "UPDATE note 
			SET delete_datetime = CURRENT_TIMESTAMP() 
			WHERE id = :id AND user_id = :user";

		$statement = $pdo->prepare($sql);

		$statement->bindParam(':id', $this->id);
		$statement->bindParam(':user', $user->id);

		$statement->execute();

		if($statement->errorInfo()[0] != '00000') {
			throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
		}
	}

	static function load($id) : Note {
		global $pdo;
		global $user;

		$sql = "SELECT note.id as id, author, source, content, add_datetime, delete_datetime, GROUP_CONCAT(DISTINCT category SEPARATOR ';') as categories, GROUP_CONCAT(DISTINCT keyword SEPARATOR ';') as keywords 
			FROM note
			LEFT JOIN category ON category.note_id = note.id
			LEFT JOIN keyword ON keyword.note_id = note.id 
			WHERE note.id = :id AND user_id = :user AND delete_datetime IS NULL GROUP BY note.id ORDER BY id DESC";

		$statement = $pdo->prepare($sql);
		
		$statement->bindParam(':id', $id);
		$statement->bindParam(':user', $user->id);

		$statement->execute();

		if($statement->errorInfo()[0] != '00000') {
			throw new Exception(__FILE__ . " line " . __LINE__ . " - " . $statement->errorInfo());
		}

		return Note::NotesFromPDO($statement)[0];
	}

	static function loadAll() : array {
		$sql = "SELECT * FROM note";
	}

	static function notesFromPDO($statement) : array {
		$notes = array();
		while($result = $statement->fetch(PDO::FETCH_ASSOC)) {
			// Explode categories and keywords into arrays
			$categories = explode(";", $result['categories']);
			$keywords = explode(";", $result['keywords']);

			// Create the note
			$notes[] = new Note($result['id'], $result['author'], $result['source'], $result['content'], $categories, $keywords, $result['add_datetime'], $result['delete_datetime']);
		}
		return $notes;
	}

}