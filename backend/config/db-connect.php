<?php

$db_server = "127.0.0.1";
$db_user = "root";
$db_password = ""; 
$db_name = "moderntech_hr";
$db_port = 3307; 

try {
    $conn = mysqli_connect($db_server, $db_user, $db_password, $db_name, $db_port);
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo "Database NOT connected: " . $e->getMessage();
}
?>