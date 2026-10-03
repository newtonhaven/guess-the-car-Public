<?php
// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
// Lazy database connection. Throws mysqli_sql_exception when the database is unreachable,
// so pages can show a friendly message instead of dying.
if (basename($_SERVER['SCRIPT_NAME']) === basename(__FILE__)) {
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied");
}

// DB_SERVER, DB_USERNAME, ... come from inc/bootstrap.php (config.php or environment variables).
function db()
{
    static $conn = null;
    if ($conn === null) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}
