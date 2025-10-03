<?php
$response = file_get_contents('http://localhost/pesopal/src/api/dashboard/summary.php', false, stream_context_create([
    'http' => [
        'header' => "Origin: http://localhost:5173\r\n"
    ]
]));

echo "Full API Response:\n";
echo $response;

$data = json_decode($response, true);
echo "\n\nSavings Goals Data:\n";
print_r($data['data']['savings_goals'] ?? 'No savings goals found');
?>