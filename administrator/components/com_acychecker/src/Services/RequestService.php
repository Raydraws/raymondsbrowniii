<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyChecker\Services;

class RequestService
{
    /**
     * @param string $url
     * @param array  $options
     *                - verifySsl: bool, default true
     *                - headers: array for example ['Content-Type' => 'application/json']
     *                - data: array, if Content-Type is application/json, the data will be json_encoded otherwise it will be urlencoded
     *                - dns: string
     *                - proxy: array ['host' => '127.0.0.1:8888', 'auth' => 'user:password'] the auth is optional
     *                - method: string GET, POST, etc
     *
     * @return array
     */
    public static function curlCall(string $url, array $options = []): array
    {
        $options['verifySsl'] = $options['verifySsl'] ?? true;

        $headers = [];
        if (!empty($options['headers']) && is_array($options['headers'])) {
            $headers = $options['headers'];
        }

        $data = [];
        if (!empty($options['data']) && is_array($options['data'])) {
            $data = $options['data'];
        }

        $allowedMethods = ['GET', 'POST'];
        $method = 'GET';
        if (!empty($options['method']) && in_array($options['method'], $allowedMethods)) {
            $method = $options['method'];
        }

        $dataFormatted = '';
        if (!empty($data)) {
            $isHeaderContentTypeJson = !empty($headers['Content-Type']) && $headers['Content-Type'] === 'application/json';
            if ($method === 'POST' && $isHeaderContentTypeJson) {
                $dataFormatted = json_encode($data);
            } else {
                $dataFormatted = http_build_query($data);
            }
        }

        if ($method === 'GET' && !empty($dataFormatted)) {
            $url .= strpos($url, '?') === false ? '?'.$dataFormatted : '&'.$dataFormatted;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $dataFormatted);
            }
        }
        if (!$options['verifySsl']) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        if (!empty($headers)) {
            // We have headers like this ['Content-Type' => 'application/json']
            // We need headers like this ['Content-Type: application/json']
            $headersFormatted = array_map(
                function ($key, $value) {
                    return $key.': '.$value;
                },
                array_keys($headers),
                $headers
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headersFormatted);
        }

        if (!empty($options['dns']) && is_string($options['dns'])) {
            curl_setopt($ch, CURLOPT_DNS_SERVERS, $options['dns']);
        }

        if (!empty($options['proxy']) && is_array($options['proxy'])) {
            curl_setopt($ch, CURLOPT_PROXY, $options['proxy']['host']);
            if (!empty($options['proxy']['auth'])) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $options['proxy']['auth']);
            }
        }

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            return [
                'error' => curl_error($ch),
                'status_code' => $httpCode,
            ];
        }

        $result = json_decode($result, true);
        $result['status_code'] = $httpCode;

        return $result;
    }
}
