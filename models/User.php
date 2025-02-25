<?php
class User
{
  private $conn;
  private $table_name = "users";

  public $id;
  public $username;
  public $email;
  public $password;

  public function __construct($db)
  {
    $this->conn = $db;
  }

  public function create()
  {
    $query = "INSERT INTO " . $this->table_name . " 
                  (username, email, password_hash) 
                  VALUES (:username, :email, :password)";

    $stmt = $this->conn->prepare($query);

    // Sanitize and hash password
    $this->username = htmlspecialchars(strip_tags($this->username));
    $this->email = htmlspecialchars(strip_tags($this->email));
    $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

    // Bind values
    $stmt->bindParam(":username", $this->username);
    $stmt->bindParam(":email", $this->email);
    $stmt->bindParam(":password", $password_hash);

    if ($stmt->execute()) {
      return true;
    }

    return false;
  }

  public function emailExists()
  {
    $query = "SELECT id, username, password_hash 
                FROM " . $this->table_name . " 
                WHERE email = ?
                LIMIT 0,1";

    $stmt = $this->conn->prepare($query);

    $this->email = htmlspecialchars(strip_tags($this->email));

    $stmt->bindParam(1, $this->email);

    $stmt->execute();

    $num = $stmt->rowCount();

    if ($num > 0) {
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      $this->id = $row['id'];
      $this->username = $row['username'];
      $this->password_hash = $row['password_hash'];

      return true;
    }

    return false;
  }
}
