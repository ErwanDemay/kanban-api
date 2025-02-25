<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

class JwtHandler
{
  private $key;
  private $iss = "kanban_api";
  private $aud = "kanban_app";
  private $iat;
  private $exp;

  public function construct()
  {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
    $this->key = $_ENV['JWT_SECRET'] ?? 'default_secret_key';
    $this->iat = time();
    $this->exp = $this->iat + 60 * 60 * 24; //Expiration de 24h
  }

  public function generateToken($user_id)
  {
    $payload = [
      'iss' => $this->iss,
      'aud' => $this->aud,
      'iat' => $this->iat,
      'exp' => $this->exp,
      'user_id' => $user_id
    ];

    return JWT::encode($payload, $this->key, 'HS256');
  }
  public function validateToken($token)
  {
    try {
      $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
      return $decoded;
    } catch (Exception $e) {
      return false;
    }
  }
}
