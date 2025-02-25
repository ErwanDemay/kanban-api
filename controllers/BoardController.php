<?php

class BoardController
{
  private $db;
  private $board;
  private $user_id;

  public function __construct($db, $user_id)
  {
    $this->db = $db;
    $this->board = new Board($db);
    $this->user_id = $user_id;
  }

  public function createBoard($data)
  {
    $this->board->user_id = $this->user_id;
    $this->board->title = $data['title'];

    if ($this->board->create()) {
      return json_encode([
        "status" => "success",
        "message" => "Board created successfully.",
        "id" => $this->board->id
      ]);
    } else {
      return json_encode([
        "status" => "error",
        "message" => "Unable to create board."
      ]);
    }
  }

  public function getBoards()
  {
    $this->board->user_id = $this->user_id;

    $stmt = $this->board->readAll();
    $num = $stmt->rowCount();

    if ($num > 0) {
      $boards_array = [];

      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $board_item = [
          "id" => $id,
          "title" => $title,
          "created_at" => $created_at,
          "updated_at" => $updated_at
        ];
        array_push($boards_array, $board_item);
      }
      return json_encode([
        "status" => "success",
        "boards" => $boards_array
      ]);
    } else {
      return json_encode([
        "status" => "success",
        "message" => "No boards found.",
        "boards" => []
      ]);
    }
  }

  public function getSingleBoard($id)
  {
    $this->board->id = $id;
    $this->board->user_id = $this->user_id;

    $stmt = $this->board->readSingle();

    if ($stmt->rowCount() > 0) {
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      $board_item = [
        "id" => $row['id'],
        "title" => $row['title'],
        "created_at" => $row['created_at'],
        "updated_at" => $row['updated_at']
      ];

      return json_encode([
        "status" => "success",
        "board" => $board_item
      ]);
    } else {
      return json_encode([
        "status" => "error",
        "message" => "Board not found."
      ]);
    }
  }



  public function updateBoard($id, $data)
  {
    $this->board->id = $id;
    $this->board->user_id = $this->user_id;
    $this->board->title = $data['title'];

    if ($this->board->update()) {
      return json_encode([
        "status" => "success",
        "message" => "Board updated successfully."
      ]);
    } else {
      return json_encode([
        "status" => "error",
        "message" => "Unable to update board."
      ]);
    }
  }
  public function deleteBoard($id)
  {
    $this->board->id = $id;
    $this->board->user_id = $this->user_id;

    if ($this->board->delete()) {
      return json_encode([
        "status" => "success",
        "message" => "Board deleted successfully."
      ]);
    } else {
      return json_encode([
        "status" => "error",
        "message" => "Unable to delete board."
      ]);
    }
  }
}
