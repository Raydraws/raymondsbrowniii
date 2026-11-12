<?php

function acym_sendAjaxResponse(string $message = '', array $data = [], bool $success = true): void
{
    $response = [
        'message' => $message,
        'data' => $data,
        'error' => !$success,
    ];

    $document = acym_getGlobal('doc');

    $document->setMimeEncoding('application/json');

    echo json_encode($response);
    exit;
}
