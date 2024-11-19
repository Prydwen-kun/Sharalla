<?php
//response struct to send

function response($response = 'ok', $message = 'No message', ...$addParam)
{
    $responseBody = [
        'response' => $response,
        'message' => $message
    ];
    if (!empty($addParam)) {
        foreach ($addParam as $key => $value) {
            $responseBody['param' . $key] = $value;
        }
    }
    echo json_encode($responseBody);
}
