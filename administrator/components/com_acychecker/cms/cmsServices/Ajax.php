<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyCheckerCmsServices;


class Ajax
{
    public static function sendAjaxResponse($message = '', $data = [], $success = true)
    {
        $response = [
            'message' => $message,
            'data' => $data,
            'status' => $success ? 'success' : 'error',
        ];

        // Get the document object.
        $document = Miscellaneous::getGlobal('doc');

        // Set the MIME type for JSON output.
        $document->setMimeEncoding('application/json');

        // Output the JSON data.
        echo json_encode($response);
        exit;
    }
}
