<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $url = $_GET['url'];
    $response = fetchData($url);

    if ($response !== false) {
        echo $response;
    } else {
        http_response_code(503);
        echo json_encode(['error' => 'Failed to fetch the URL.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}

function fetchData($url) {
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        return false;
    }

    curl_close($ch);

    return $response;
}
?>
