<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyChecker\Controllers;

use AcyChecker\Libraries\AcycController;
use AcyChecker\Services\ApiService;
use AcyChecker\Services\ViewService;
use AcyCheckerCmsServices\Ajax;
use AcyCheckerCmsServices\Language;
use AcyCheckerCmsServices\Security;
use AcyCheckerCmsServices\Url;

class SingletestController extends AcycController
{
    public const TRUST_RESULT_RISKY = 'risky';
    public const TRUST_RESULT_POSSIBLE = 'possible';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_INVALID = 'invalid';
    public const STATUS_WARNING = 'warning';
    public const STATUS_INFO = 'info';
    public const STATUS_NOT_TESTED = 'not_tested';

    public array $data;

    public function __construct()
    {
        parent::__construct();

        $this->name = 'Singletest';
        $this->data = [];
    }

    public function defaultTask()
    {
        $this->layout = 'default';
        $this->breadcrumb[Language::translation('ACYC_SINGLE_TEST')] = Url::completeLink('singletest');
        $this->display();
    }

    public function ajaxGetSingleTest(): void
    {
        $apiService = new ApiService();
        $email = Security::getVar('string', 'email', '');
        if (!empty($email)) {
            $user = new \stdClass();
            $user->email = $email;
            $conditions = 'disposable,accept_all,domain_not_exists,free_domain,invalid_smtp,not_existing,role_based';
            $result = $apiService->testUser($user, $conditions, true);
        } else {
            $result = [];
        }

        if (empty($result) || !is_array($result)) {
            Ajax::sendAjaxResponse(Language::translation('ACYC_COULD_NOT_CALL_API'), [], false);

            return;
        }

        $this->data['tests'] = [
            ApiService::API_TEST_KEY_VALID_ADDRESS => [
                'status' => $result[ApiService::API_TEST_KEY_VALID_ADDRESS] ? self::STATUS_SUCCESS : self::STATUS_INVALID,
                'label' => $result[ApiService::API_TEST_KEY_VALID_ADDRESS] ? Language::translation('ACYC_TEST_VALID_LABEL') : Language::translation('ACYC_TEST_INVALID_LABEL'),
                'description' => $result[ApiService::API_TEST_KEY_VALID_ADDRESS] ? Language::translation('ACYC_TEST_VALID_DESC') : Language::translation('ACYC_TEST_INVALID_DESC'),
            ],
            ApiService::API_TEST_KEY_DOMAIN_EXISTS => [
                'status' => $result[ApiService::API_TEST_KEY_DOMAIN_EXISTS] ? self::STATUS_SUCCESS : self::STATUS_INVALID,
                'label' => $result[ApiService::API_TEST_KEY_DOMAIN_EXISTS]
                    ? Language::translation('ACYC_TEST_DOMAIN_EXISTS_LABEL')
                    : Language::translation('ACYC_TEST_DOMAIN_NOT_EXISTS_LABEL'),
                'description' => $result[ApiService::API_TEST_KEY_DOMAIN_EXISTS]
                    ? Language::translation('ACYC_TEST_DOMAIN_EXISTS_DESC')
                    : Language::translation('ACYC_TEST_DOMAIN_NOT_EXISTS_DESC'),
            ],
        ];

        if ($result[ApiService::API_TEST_KEY_EMAIL_EXISTS] === ApiService::TRUST_RESULT_VALID) {
            $this->data['tests'][ApiService::API_TEST_KEY_EMAIL_EXISTS] = [
                'status' => self::STATUS_SUCCESS,
                'label' => Language::translation('ACYC_TEST_EXISTS_LABEL'),
                'description' => Language::translation('ACYC_TEST_EXISTS_DESC'),
            ];
        } elseif ($result[ApiService::API_TEST_KEY_EMAIL_EXISTS] === ApiService::TRUST_RESULT_POSSIBLE) {
            $this->data['tests'][ApiService::API_TEST_KEY_EMAIL_EXISTS] = [
                'status' => self::STATUS_SUCCESS,
                'label' => Language::translation('ACYC_TEST_PROBABLY_EXISTS_LABEL'),
                'description' => Language::translation('ACYC_TEST_PROBABLY_EXISTS_DESC'),
            ];
        } elseif ($result[ApiService::API_TEST_KEY_EMAIL_EXISTS] === ApiService::TRUST_RESULT_RISKY) {
            $this->data['tests'][ApiService::API_TEST_KEY_EMAIL_EXISTS] = [
                'status' => self::STATUS_WARNING,
                'label' => Language::translation('ACYC_TEST_RISKY'),
                'description' => Language::translation('ACYC_TEST_RISKY_DESC'),
            ];
        } else {
            $this->data['tests'][ApiService::API_TEST_KEY_EMAIL_EXISTS] = [
                'status' => self::STATUS_INVALID,
                'label' => Language::translation('ACYC_TEST_NOT_EXISTING_LABEL'),
                'description' => Language::translation('ACYC_TEST_NOT_EXISTING_DESC'),
            ];
        }

        if ($result[ApiService::API_TEST_KEY_DOMAIN_EXISTS]) {
            $this->data['tests'][ApiService::API_TEST_KEY_DISPOSABLE] = [
                'status' => $result[ApiService::API_TEST_KEY_DISPOSABLE] ? self::STATUS_WARNING : self::STATUS_INFO,
                'label' => $result[ApiService::API_TEST_KEY_DISPOSABLE]
                    ? Language::translation('ACYC_TEST_IT_IS_DISPOSABLE_LABEL')
                    : Language::translation('ACYC_TEST_IS_NOT_DISPOSABLE_LABEL'),
                'description' => Language::translation('ACYC_TEST_DISPOSABLE_DESC'),
            ];
            $this->data['tests'][ApiService::API_TEST_KEY_FREE_MAILBOX] = [
                'status' => self::STATUS_INFO,
                'label' => $result[ApiService::API_TEST_KEY_FREE_MAILBOX]
                    ? Language::translation('ACYC_TEST_IS_FREE_LABEL')
                    : Language::translation('ACYC_TEST_IS_NOT_FREE_LABEL'),
                'description' => Language::translation('ACYC_TEST_FREE_DESC'),
            ];
            $this->data['tests'][ApiService::API_TEST_KEY_ROLE_BASED] = [
                'status' => self::STATUS_INFO,
                'label' => $result[ApiService::API_TEST_KEY_ROLE_BASED]
                    ? Language::translation('ACYC_TEST_IS_ROLE_LABEL')
                    : Language::translation('ACYC_TEST_IS_NOT_ROLE_LABEL'),
                'description' => Language::translation('ACYC_TEST_ROLE_DESC'),
            ];
            $this->data['tests'][ApiService::API_TEST_KEY_ACCEPT_ALL] = [
                'status' => self::STATUS_INFO,
                'label' => $result[ApiService::API_TEST_KEY_ACCEPT_ALL]
                    ? Language::translation('ACYC_TEST_IS_ACCEPT_ALL_LABEL')
                    : Language::translation('ACYC_TEST_IS_NOT_ACCEPT_ALL_LABEL'),
                'description' => Language::translation('ACYC_TEST_ACCEPT_ALL_DESC'),
            ];
        } else {
            $this->data['tests'][ApiService::API_TEST_KEY_DISPOSABLE] = [
                'status' => self::STATUS_NOT_TESTED,
                'label' => Language::translation('ACYC_DISPOSABLE'),
                'description' => Language::translation('ACYC_TEST_DISPOSABLE_DESC'),
            ];
            $this->data['tests'][ApiService::API_TEST_KEY_FREE_MAILBOX] = [
                'status' => self::STATUS_NOT_TESTED,
                'label' => Language::translation('ACYC_TEST_FREE_LABEL'),
                'description' => Language::translation('ACYC_TEST_FREE_DESC'),
            ];
            $this->data['tests'][ApiService::API_TEST_KEY_ROLE_BASED] = [
                'status' => self::STATUS_NOT_TESTED,
                'label' => Language::translation('ACYC_TEST_ROLE_LABEL'),
                'description' => Language::translation('ACYC_TEST_ROLE_DESC'),
            ];
            $this->data['tests'][ApiService::API_TEST_KEY_ACCEPT_ALL] = [
                'status' => self::STATUS_NOT_TESTED,
                'label' => Language::translation('ACYC_TEST_ACCEPT_ALL_LABEL'),
                'description' => Language::translation('ACYC_TEST_ACCEPT_ALL_DESC'),
            ];
        }

        $this->data['result'] = $result;
        $this->data['colorMap'] = [
            self::STATUS_SUCCESS => 'green',
            self::STATUS_INVALID => 'red',
            self::STATUS_WARNING => 'orange',
            self::STATUS_INFO => 'blue',
            self::STATUS_NOT_TESTED => 'gray',
        ];
        $this->data['isValid'] = $result[ApiService::API_TEST_KEY_EMAIL_EXISTS];

        ob_start();
        include ViewService::getView('Singletest', 'default_results');
        $resultsHtml = ob_get_clean();

        ob_start();
        include ViewService::getView('Singletest', 'default_details');
        $detailsHtml = ob_get_clean();

        Ajax::sendAjaxResponse('', [
            'results' => $result,
            'html' => [
                'results' => $resultsHtml,
                'details' => $detailsHtml,
            ],
        ]);
    }
}
