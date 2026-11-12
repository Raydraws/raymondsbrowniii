<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyChecker\Services;

use AcyChecker\Classes\BlockUserClass;
use AcyChecker\Classes\TestClass;
use AcyChecker\Libraries\AcycObject;
use AcyCheckerCmsServices\Database;
use AcyCheckerCmsServices\Language;
use AcyCheckerCmsServices\Message;
use AcyCheckerCmsServices\Url;

class ApiService extends AcycObject
{
    public array $errors = [];

    private int $blacklistedNameLength;
    private array $blacklistedNameRules = [];
    private array $blacklistedRules = [];
    private array $whitelistedRules = [];
    private string $apiBaseUrl;
    private string $apiKey;

    const API_TEST_KEY_EMAIL = 'email';
    const API_TEST_KEY_VALID_ADDRESS = 'valid';
    const API_TEST_KEY_DISPOSABLE = 'disposable';
    const API_TEST_KEY_FREE_MAILBOX = 'free';
    const API_TEST_KEY_ROLE_BASED = 'role';
    const API_TEST_KEY_DOMAIN_EXISTS = 'd_exists';
    const API_TEST_KEY_DOMAIN_SUGGESTIONS = 'suggestions';
    const API_TEST_KEY_EMAIL_EXISTS = 'exists';
    const API_TEST_KEY_ACCEPT_ALL = 'accept_all';

    const TRUST_RESULT_VALID = 'valid';
    const TRUST_RESULT_POSSIBLE = 'possible';
    const TRUST_RESULT_RISKY = 'risky';
    const TRUST_RESULT_NOT_EXISTING = 'not_existing';

    public function __construct()
    {
        parent::__construct();

        $this->apiBaseUrl = ACYC_API_URL.'api/v1/';
        $this->config->load();
        $this->apiKey = $this->config->get('license_key');
        $blacklisted = $this->config->get('blacklist');
        if (!empty($blacklisted)) {
            $this->blacklistedRules = explode(',', $blacklisted);
            $this->blacklistedRules = array_map('trim', $this->blacklistedRules);
        }

        $whitelisted = $this->config->get('whitelist');
        if (!empty($whitelisted)) {
            $this->whitelistedRules = explode(',', $whitelisted);
            $this->whitelistedRules = array_map('trim', $this->whitelistedRules);
        }

        $blacklistedName = $this->config->get('blacklist_name');
        if (!empty($blacklistedName)) {
            $this->blacklistedNameRules = explode(',', $blacklistedName);
            $this->blacklistedNameRules = array_map('trim', $this->blacklistedNameRules);

            foreach ($this->blacklistedNameRules as $key => $rule) {
                $startingRegexCharPos = strpos($rule, '^');
                $endingRegexCharPos = strpos($rule, '$');
                $stringLength = strlen($rule);

                if ($startingRegexCharPos !== 0 && $endingRegexCharPos !== $stringLength - 1) {
                    $rule = preg_quote($rule);
                }

                if ($startingRegexCharPos !== 0) {
                    $rule = '^.*'.$rule;
                }

                if ($endingRegexCharPos !== $stringLength - 1) {
                    $rule = $rule.'.*$';
                }

                $this->blacklistedNameRules[$key] = $rule;
            }
        }

        $this->blacklistedNameLength = (int)$this->config->get('blacklist_name_length', 0);
    }

    private function processRequest(string $url, array $data = [], string $method = 'GET', array $headers = []): array
    {
        $headers = $headers + $this->getDefaultHeaders();

        $options = [
            'verifySsl' => false,
            'headers' => $headers,
            'method' => $method,
            'data' => $data,
        ];
        $request = RequestService::curlCall($url, $options);

        if (!empty($request['error'])) {
            $this->errors[] = $request['error'];

            return [];
        }

        $return = $request;
        $return['success'] = true;

        if ($request['status_code'] < 200 || $request['status_code'] > 299) {
            $return['success'] = false;
        }

        return $return;
    }

    private function getDefaultHeaders(): array
    {
        return [
            'Accept' => 'application/ld+json',
            'Content-Type' => 'application/ld+json',
            'API-KEY' => $this->apiKey,
            'Source' => 'plugin',
            'Source-Version' => '1.8.0',
        ];
    }

    public function getCredits()
    {
        $url = $this->apiBaseUrl.'licenses/credits';

        $request = $this->processRequest($url);
        if (!empty($this->errors)) {
            Message::enqueueMessage(implode(', ', $this->errors), 'error');
        }

        if (empty($request['success'])) {
            return $this->returnResponse(Language::translation('ACYC_COULD_NOT_CALL_API'), [], false);
        }

        if ($request['status_code'] !== 200) {
            if (isset($request['message'])) {
                return $this->returnResponse($request['message'], [], false);
            } else {
                return $this->returnResponse(
                    Language::translationSprintf('ACYC_ERROR_CALLING_API_CODE_X', $request['status_code']),
                    [],
                    false
                );
            }
        }

        return $this->returnResponse('', $request);
    }

    public function dispatchBatch($emails)
    {
        if (empty($emails)) {
            return $this->returnResponse(Language::translation('ACYC_INVALID_EMAIL_ADDRESS'), [], false);
        }

        $url = $this->apiBaseUrl.'bulk_verify/';

        $data = [
            'emails' => $emails,
            'callbackUrl' => Url::frontendLink('callbackController'),
            'fileType' => 'CSV',
        ];

        $request = $this->processRequest($url, $data, 'POST');

        if (empty($request['success'])) {
            return $this->returnResponse(Language::translation('ACYC_COULD_NOT_CALL_API'), [], false);
        }

        //Not enough credits
        if ($request['status_code'] === 403 && strpos($request['message'], 'Not enough credits') !== false) {
            return $this->returnResponse(Language::translation('ACYC_NOT_ENOUGH_CREDITS'), [], false);
        } elseif ($request['status_code'] !== 201) {
            $message = $request['message'] ?? '';
            if (empty($message)) {
                $message = Language::translationSprintf('ACYC_ERROR_CALLING_API_CODE_X', $request['status_code']);
            }

            return $this->returnResponse($message, [], false);
        }

        return $this->returnResponse('', $request);
    }

    private function returnResponse($message = '', $data = [], $success = true)
    {
        return [
            'message' => $message,
            'data' => $data,
            'success' => $success,
        ];
    }

    public function testEmail($email, $conditions)
    {
        $user = new \stdClass();
        $user->email = $email;

        return $this->testUser($user, $conditions);
    }

    public function testUser(object $user, string $conditions, bool $testOne = false)
    {
        $result = $this->getEmailResult($user->email);
        if (!empty($result) && $testOne) {
            DebugService::logMessage('single_test.log', $user->email.' - '.json_encode($result['data'], true));

            return $result['data'];
        }

        if ($result['success'] === false) {
            // An error occurred while testing the email address, no credits left for example, don't block the subscription
            return true;
        }

        $conditions = explode(',', $conditions);
        $emailOk = $this->isUserAccepted($result['data'], $conditions, $user);

        if ($emailOk !== true) {
            $testService = new TestService();
            $banReasons = $testService->getBanReasons();

            $message = Language::translationSprintf('ACYC_ADDRESS_NOT_OK', $banReasons[$emailOk] ?? $emailOk);
            DebugService::logMessage('individual_tests.log', $user->email.' - '.$message);
        }

        $result['data']['name'] = $user->name ?? '';
        $this->saveTest($result['data'], $emailOk);

        return $emailOk;
    }

    public function isUserAccepted(array $emailResult, array $conditions, object $user)
    {
        if ($this->isMatchingRules($emailResult[self::API_TEST_KEY_EMAIL], $this->whitelistedRules)) {
            return true;
        }

        if (in_array(self::TRUST_RESULT_RISKY, $conditions) && $emailResult[self::API_TEST_KEY_EMAIL_EXISTS] === self::TRUST_RESULT_RISKY) {
            return self::TRUST_RESULT_RISKY;
        }

        if (in_array('domain_not_exists', $conditions) && intval($emailResult[self::API_TEST_KEY_DOMAIN_EXISTS]) !== 1) {
            return 'domain_not_exists';
        }

        if (count(array_intersect(['invalid_smtp', self::TRUST_RESULT_NOT_EXISTING], $conditions)
            ) > 0 && $emailResult[self::API_TEST_KEY_EMAIL_EXISTS] === self::TRUST_RESULT_NOT_EXISTING) {
            return 'invalid_smtp';
        }

        foreach (
            [
                'disposable' => self::API_TEST_KEY_DISPOSABLE,
                'accept_all' => self::API_TEST_KEY_ACCEPT_ALL,
                'free_domain' => self::API_TEST_KEY_FREE_MAILBOX,
                'role_based' => self::API_TEST_KEY_ROLE_BASED,
            ] as $acycName => $apiName
        ) {
            if (in_array($acycName, $conditions) && intval($emailResult[$apiName]) === 1) {
                return $acycName;
            }
        }

        if ($this->isMatchingRules($emailResult[self::API_TEST_KEY_EMAIL], $this->blacklistedRules)) {
            return 'blacklisted';
        }

        if (!empty($user->name)) {
            if (!empty($this->blacklistedNameLength) && strlen($user->name) >= $this->blacklistedNameLength) {
                return 'blacklisted_name';
            }

            if ($this->isMatchingRules($user->name, $this->blacklistedNameRules)) {
                return 'blacklisted_name';
            }
        }

        if (!empty($user->username)) {
            if (!empty($this->blacklistedNameLength) && strlen($user->username) >= $this->blacklistedNameLength) {
                return 'blacklisted_name';
            }

            if ($this->isMatchingRules($user->username, $this->blacklistedNameRules)) {
                return 'blacklisted_name';
            }
        }

        return true;
    }

    private function getEmailResult($email)
    {
        $url = $this->apiBaseUrl.'email_verify/'.$email;

        $request = $this->processRequest($url);

        if (empty($request['success'])) {
            $message = Language::translation('ACYC_COULD_NOT_CALL_API');
            if (!empty($this->errors)) {
                $message = implode(', ', $this->errors);
            }
            DebugService::logMessage('individual_tests.log', $email.' - '.$message);

            return [
                'success' => false,
                'message' => Language::translation('ACYC_COULD_NOT_CALL_API'),
            ];
        }

        if ($request['status_code'] !== 200) {
            $message = $request['message'] ?? Language::translationSprintf('ACYC_ERROR_CALLING_API_CODE_X', $request['status_code']);
            DebugService::logMessage('individual_tests.log', $email.' - '.$message);

            return [
                'success' => false,
                'message' => $message,
            ];
        }

        return [
            'success' => true,
            'data' => $request['data'],
        ];
    }

    public function prepareTest($result, $escape)
    {
        $dateTimeNow = new \DateTime('NOW');
        $dateTimeNow->setTimezone(new \DateTimeZone('UTC'));
        $testClass = new TestClass();

        $test = new \stdClass();
        $test->email = $result[self::API_TEST_KEY_EMAIL];
        if (isset($result['name'])) {
            $test->name = $result['name'];
        }
        $test->date = $dateTimeNow->format('Y-m-d H:i:s');
        $test->raw_result = json_encode($result);
        $test->test_result = $result[self::API_TEST_KEY_EMAIL_EXISTS];

        if ($escape) {
            $test->email = Database::escapeDB($test->email);
            if (isset($result['name'])) {
                $test->name = Database::escapeDB($test->name);
            }
            $test->date = Database::escapeDB($test->date);
            $test->raw_result = Database::escapeDB($test->raw_result);
            $test->test_result = Database::escapeDB($test->test_result);
        }

        $test->disposable = $result[self::API_TEST_KEY_DISPOSABLE] ? 1 : 0;
        $test->free = $result[self::API_TEST_KEY_FREE_MAILBOX] ? 1 : 0;
        $test->accept_all = $result[self::API_TEST_KEY_ACCEPT_ALL] ? 1 : 0;
        $test->role_email = $result[self::API_TEST_KEY_ROLE_BASED] ? 1 : 0;
        $test->current_step = $testClass::STEP['finished'];
        $test->domain_exists = $result[self::API_TEST_KEY_DOMAIN_EXISTS] ? 1 : 0;

        return $test;
    }

    private function saveTest($result, $emailOk)
    {
        $test = $this->prepareTest($result, false);
        $test->block_reason = $emailOk === true ? null : $emailOk;
        $test->batch_id = null;

        $testClass = new TestClass();
        $testClass->save($test);

        if ($emailOk !== true) {
            $blockedEmail = new \stdClass();
            $blockedEmail->email = $test->email;
            $blockedEmail->block_reason = $emailOk;

            $blockUserClass = new BlockUserClass();
            $blockUserClass->block_action = BlockUserClass::BLOCK_ACTION_REGISTRATION;
            $blockUserClass->save($blockedEmail);
        }
    }

    public function refreshCreditsUsed()
    {
        $result = $this->getCredits();

        if ($result['success']) {
            $newConfig = [
                'credits_used_batch' => $result['data']['credits_used_batch'],
                'remaining_credits_batch' => $result['data']['remaining_credits_batch'],
                'credits_used_simple' => $result['data']['credits_used_simple'],
                'remaining_credits_simple' => $result['data']['remaining_credits_simple'],
                'license_last_check' => time(),
                'license_end_date' => $result['data']['end_date'],
            ];
            $this->config->save($newConfig);
        } else {
            Message::enqueueMessage($result['message'], 'error');
        }
    }

    public function getBatchResult($batchId)
    {
        if (empty($batchId)) {
            return false;
        }

        $url = $this->apiBaseUrl.'bulk_result/'.$batchId;

        $request = $this->processRequest($url);

        if (empty($request['success'])) {
            DebugService::logMessage('batch_tests.log', 'Error getting '.$batchId.' results');

            return false;
        }

        if ($request['status_code'] !== 200) {
            $codes = [
                403 => 'The API key is incorrect',
                404 => 'The results are unavailable',
                429 => 'Too many requests',
            ];
            if (in_array($request['status_code'], array_keys($codes))) {
                DebugService::logMessage('batch_tests.log', 'Error getting '.$batchId.' results: '.$codes[$request['status_code']]);
            } else {
                DebugService::logMessage('batch_tests.log', 'Error getting '.$batchId.' results: Unknown return code '.$request['status_code']);
            }

            return false;
        }

        if (empty($request['data'])) {
            DebugService::logMessage('batch_tests.log', 'Error getting '.$batchId.' results: No data returned');

            return false;
        }

        DebugService::logMessage('batch_tests.log', 'Getting batch '.$batchId.' results was successful.');

        return $request['data'];
    }

    private function isMatchingRules($testedString, $rules): bool
    {
        if (empty($rules)) {
            return false;
        }

        $reversedString = strrev($testedString);
        foreach ($rules as $oneRule) {
            if (strpos($reversedString, strrev($oneRule)) === 0) {
                return true;
            }

            if (strpos($oneRule, '$') !== false && preg_match('/'.$oneRule.'/', $testedString) === 1) {
                return true;
            }
        }

        return false;
    }
}
