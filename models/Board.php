<?php

class Board
{
  private $conn;
  private $table_name = "boards";

  public $id;
  public $user_id;
  public $title;
  public $created_at;
  public $updated_at;

  public function __construct($db)
  {
    $this->conn = $db;
  }

  public function create()
  {
    $query = "INSERT INTO" . $this->table_name . " (user_id, title) 
   VALUES (:user_id , :title)";

    $stmt = $this->conn->prepare($query);

    $this->title = htmlspecialchars(strip_tags($this->title));

    $stmt->bindParam(":user_id", $this->user_id);
    $stmt->bindParam(":title", $this->title);

    if ($stmt->execute()) {
      $this->id = $this->conn->lastInsertId();
      return true;
    }
    return false;
  }

  public function readAll()
  {
    $query = "SELECT * FROM" . $this->table_name . " WHERE user_id = :user_id 
    ORDER BY created_at DESC";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(":user_id", $this->user_id);

    $stmt->execute();

    return $stmt;
  }

  public function readSingle()
  {
    $query = "SELECT * FROM " . $this->table_name . " 
              WHERE id = :id AND user_id = :user_id 
              LIMIT 0,1";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(":id", $this->id);
    $stmt->bindParam(":user_id", $this->user_id);

    $stmt->execute();

    return $stmt;
  }



  public function update()
  {
    $query = "UPDATE " . $this->table_name . " 
              SET title = :title, updated_at = NOW() 
              WHERE id = :id AND user_id = :user_id";

    $stmt = $this->conn->prepare($query);

    $this->title = htmlspecialchars(strip_tags($this->title));

    $stmt->bindParam(":title", $this->title);
    $stmt->bindParam(":id", $this->id);
    $stmt->bindParam(":user_id", $this->user_id);

    if ($stmt->execute()) {
      return true;
    }
    return false;
  }

  public function delete()
  {
    $query = "DELETE FROM " . $this->table_name . " 
              WHERE id = :id AND user_id = :user_id";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(":id", $this->id);
    $stmt->bindParam(":user_id", $this->user_id);

    if ($stmt->execute()) {
      return true;
    }
    return false;
  }
}
