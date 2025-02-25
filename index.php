<?php
//headers
header("Access-Control-Allow-Origin: *");
header("Content-Type : application/json; charset_UTF-8");
header("Access-Control-Allow-Methods: POST, GET , PUT ,DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-Width");


//Option request 
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit();
}

//database and classes
require_once "config/database.php";
require_once "models/User.php";
require_once "models/Board.php";
require_once "models/Column.php";
require_once "models/Task.php";
require_once "controllers/AuthController.php";
require_once "controllers/BoardController.php";
require_once "controllers/ColumnController.php";
require_once "controllers/TaskController.php";
require_once "middleware/AuthMiddleware.php";
require_once "utils/JwtHandler.php";

//Instancier la bdd
$database = new Database();
$db = $database->getConnection();

//Request URl and methods
$request_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_segments = explode('/', trim($request_url, '/'));
$api_endpoint = $uri_segments[1] ?? '';
$ressource_id = $uri_segments[2] ?? null;
$request_method = $_SERVER['REQUEST_METHOD'];

//get request data
$data = json_decode(file_get_contents("php://input"), true);

//API REQUESTS
switch ($api_endpoint) {
  case 'register':
    if ($request_method === 'POST') {
      $auth_controller = new AuthController($db);
      echo $auth_controller->register($data);
    } else {
      header("HTTP/1.1 405 Method Not Allowed");
      echo json_encode(["message" => "Method not allowed"]);
    }
    break;

  case 'login':
    if ($request_method === 'POST') {
      $auth_controller = new AuthController($db);
      echo $auth_controller->login($data);
    } else {
      header("HTTP/1.1 405 Method Not Allowed");
      echo json_encode(["Message" => "Method Not Allowed"]);
    }
    break;

  case 'boards':
    // Validate JWT token
    $auth_middleware = new AuthMiddleware();
    $user_id = $auth_middleware->validateToken();

    $board_controller = new BoardController($db, $user_id);

    if ($request_method === 'GET') {
      if ($resource_id) {
        // Get specific board
        echo $board_controller->getSingleBoard($resource_id);
      } else {
        // Get all boards
        echo $board_controller->getBoards();
      }
    } elseif ($request_method === 'POST') {
      // Create board
      echo $board_controller->createBoard($data);
    } elseif ($request_method === 'PUT' && $resource_id) {
      // Update board
      echo $board_controller->updateBoard($resource_id, $data);
    } elseif ($request_method === 'DELETE' && $resource_id) {
      // Delete board
      echo $board_controller->deleteBoard($resource_id);
    } else {
      header("HTTP/1.1 405 Method Not Allowed");
      echo json_encode(["message" => "Method not allowed"]);
    }
    break;

  default:
    header("HTTP/1.1 404 Not Found");
    echo json_encode(["message" => "Endpoint not found"]);
    break;
}
