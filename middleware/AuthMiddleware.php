<?php

class AuthMiddleware
{
  private $jwt;

  public function __construct()
  {
    $this->jwt = new JwtHandler();
  }

  public function validateToken()
  {
    $headers = apache_request_headers();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : "";

    if ($authHeader) {
      $token = str_replace('Bearer ', "", $authHeader);

      $decoded = $this->jwt->validateToken($token);

      if ($decoded) {
        return $decoded->user_id;
      }
    }



    header("HTTP/1.1 401 Unauthorized");
    echo json_encode([
      "status" => "error",
      "message" => "Unauthorized access."
    ]);
    exit;
  }
}
