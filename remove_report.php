<?php
session_start();
include "dbcon.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $reportId = $input['id'];

    if (!isset($_SESSION['role']) || trim($_SESSION['role']) == '') {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    $sql = "DELETE FROM reports WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $reportId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove report']);
    }

    $stmt->close();
    $conn->close();
}
?>
