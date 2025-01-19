<?php
require 'vendor/autoload.php'; 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;  
include 'db_connection.php'; 
include 'auth_middleware.php';
header('Content-Type: application/json');

$secretKey = "9%fG8@h7!wQ4\$zR2*vX3&bJ1#nL6!mP5"; 

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? null;

    if (!$token) {
        echo json_encode(["error" => "Token is required."]);
        http_response_code(401);
        exit();
    }

    try {
        $token = str_replace("Bearer ", "", $token);
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
        $company_id = $decoded->company_id; // الحصول على company_id من التوكن

        if (!$company_id) {
            echo json_encode(["error" => "Company ID is required."]);
            exit();
        }

        // الحصول على الـ user_id من الـ query parameter
        $user_id = $_GET['user_id'] ?? null;

        if (!$user_id) {
            echo json_encode(["error" => "User ID is required."]);
            exit();
        }

        // استرجاع السيرة الذاتية للمستخدم المحدد
        $sql = "
            SELECT cv.*, u.User_name, u.Phone 
            FROM curriculum_vitae AS cv
            JOIN users AS u ON cv.user_id = u.User_id
            WHERE cv.user_id = ? 
        ";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $cv = $result->fetch_assoc();
            echo json_encode(['success' => true, 'cv' => $cv]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No CV found for this user.']);
        }
    } catch (Exception $e) {
        echo json_encode(["error" => "Invalid token: " . $e->getMessage()]);
        exit();
    }
} else {
    echo json_encode(["error" => "Method not allowed"]);
}
?>
