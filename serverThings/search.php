<?php
// Autocomplete endpoint for Guess the Car: GET ?query=<text> -> [{"answer": "..."}]
require __DIR__ . '/../inc/bootstrap.php';
require APP_ROOT . '/inc/db.php';

header('Content-Type: application/json; charset=utf-8');

$query = trim((string) ($_GET['query'] ?? ''));
if ($query === '' || strlen($query) > 60) {
    echo '[]';
    exit;
}

try {
    $pattern = '%' . addcslashes($query, '%_\\') . '%';
    $stmt = db()->prepare('SELECT DISTINCT answer FROM search WHERE answer LIKE ? ORDER BY answer LIMIT 10');
    $stmt->bind_param('s', $pattern);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = ['answer' => $row['answer']];
    }
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('search: ' . $e->getMessage());
    http_response_code(503);
    echo '[]';
}
