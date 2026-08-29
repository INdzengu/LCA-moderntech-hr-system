<?php
// helpers/Response.php

class Response {
    /**
     * Sends a standardized JSON response with an HTTP status code
     */
    public static function json($statusCode, $data) {
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }
}
?>