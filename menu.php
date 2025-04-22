<?php
$host = "localhost";
$user = "root";
$password = "admin123";
$dbname = "test1";
// Create connection
$conn = new mysqli($host, $user, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header('Content-Type: application/json');
session_start();
$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

switch ($action) {
    case "get":
        // user
        $_SESSION['id'] = 1;
        if (!isset($_SESSION['id'])) {
            echo json_encode(['success' => false, 'message' => 'Please log in']);
            exit;
        }
        $stmt = $conn->prepare("SELECT * FROM search_history WHERE user_id = ?");
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($mysqli->error));
            }
        $stmt->bind_param("i", $_SESSION['id']);
        $stmt->execute();
        $history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        if (!$history){
            echo json_encode(['success' => true, 'message' => 'ok', "data"=>[
                ]]);
            exit();
        }
        echo json_encode(['success' => true, 'message' => 'ok',  "data"=>$history
        ]);
        break;
    case "search":
        $_SESSION['id'] = 1;
        $userId  = 1;
        if (!isset($_SESSION['id'])) {
            echo json_encode(['success' => false, 'message' => 'Please log in']);
            exit;

        }
        $arr = [
            "Home" => "index.html",
            "Forum" => "Forum/forum.html",
            "Booking" => "index_booking.html",
            "Paris" => "paris.html",
            "London" => "London.html",
            "Rome" => "Rome.html",
            "Beijing" => "Beijing.html",
            "Tokyo" => "Tokyo.html",
            "KualaLumpur" => "KualaLumpur.html",
        ];
        $search = $_GET['search'] ?? '';

        if (!$search){
            echo json_encode(['success' => false, 'message' => 'failed']);
            exit;
        }
        $url = $arr[$search] ?? "";
        $stmt = $conn->prepare("INSERT INTO search_history (user_id, content, url)
        VALUES (?, ?,  ?)");
        $stmt->bind_param("sss", $userId , $search, $url );
        $stmt->execute();
        if (!isset($arr[$search])){
            echo json_encode(['success' => false, 'message' => 'failed']);
            exit();
        }
        echo json_encode(['success' => true, 'message' => 'ok', 'data' => [
            "content"=> $search,
            "url"=> $arr[$search],
        ]]);
        break;
    case "delete":
        $_SESSION['id'] = 1;
        if (!isset($_SESSION['id'])) {
            echo json_encode(['success' => false, 'message' => 'Please log in', 'code' => 401]);
            exit;
        }
        $stmt = $conn->prepare("delete FROM search_history WHERE id = ?");
        $stmt->bind_param("i", $input['id']);
        $stmt->execute();
        if ($conn->affected_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'failed']);
            exit;
        }
        echo json_encode(['success' => true]);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$conn->close();
