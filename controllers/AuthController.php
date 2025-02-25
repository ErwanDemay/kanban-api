<?php

class AuthController
{
  private $db;
  private $user;
  private $jwt;

  public function __construct($db)
  {
    $this->db = $db;
    $this->user = new User($db);
    $this->jwt = new JwtHandler();
  }

  public function register($data)
  {
    $this->user->username = $data['username'];
    $this->user->email = $data['email'];
    $this->user->password = $data['password'];

    if ($this->user->create()) {
      return json_encode([
        "status" => "success",
        "message" => "User registered successfully."
      ]);
    } else {
      return json_encode([
        "status" => "error",
        "message" => "Unable to register user."
      ]);
    }
  }
  public function login($data)
  {
    $this->user->email = $data['email'];

    if ($this->user->emailExists() && password_verify($data['password'], $this->user->password_hash)) {
      $token = $this->jwt->generateToken($this->user->id);

      return json_encode([
        "status" => "success",
        "message" => "Login successful.",
        "token" => $token,
        "user" => [
          "id" => $this->user->id,
          "username" => $this->user->username
        ]
      ]);
    } else {
      return json_encode([
        "status" => "error",
        "message" => "Invalid email or password."
      ]);
    }
  }
}
