<?php
session_start();
include "dbcon.php"; // Make sure this file uses pg_connect for PostgreSQL

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $reportId = $input['id'];

    if (!isset($_SESSION['role']) || trim($_SESSION['role']) == '') {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    // PostgreSQL query with parameterized input
    $sql = "DELETE FROM reports WHERE id = $1";
    $result = pg_query_params($conn, $sql, array($reportId));

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove report']);
    }

    // No need to close the statement or connection explicitly in PostgreSQL with pg_query
    // Since pg_query doesn't require a separate statement object, we don't need $stmt->close() and $conn->close()
}
?>
