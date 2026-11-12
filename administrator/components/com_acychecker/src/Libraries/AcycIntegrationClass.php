<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyChecker\Libraries;

use AcyChecker\Classes\BlockUserClass;
use AcyChecker\Classes\DeleteUserClass;
use AcyChecker\Classes\TestClass;
use AcyChecker\Services\ApiService;
use AcyChecker\Services\DebugService;
use AcyChecker\Services\TestService;
use AcyCheckerCmsServices\Database;
use AcyCheckerCmsServices\Language;

class AcycIntegrationClass extends AcycClass
{
    /**
     * @var BlockUserClass
     */
    protected $blockUserClass;

    /**
     * @var DeleteUserClass
     */
    protected $deleteUserClass;

    public function __construct()
    {
        parent::__construct();
        $this->blockUserClass = new BlockUserClass();
        $this->deleteUserClass = new DeleteUserClass();
    }

    public function handleBatchCallback($actionSelected, $conditionsSelected, $emailsResults, &$allTests)
    {
        // We get all the emails in the test
        $rawEmails = array_column($emailsResults, ApiService::API_TEST_KEY_EMAIL);
        if (empty($rawEmails)) {
            return;
        }

        $users = $this->getUsersFromEmails($rawEmails);
        if (empty($users)) {
            return;
        }

        $apiService = new ApiService();
        $testService = new TestService();
        $banReasons = $testService->getBanReasons();
        $this->blockUserClass->block_action = BlockUserClass::BLOCK_ACTION_BATCH;
        $this->deleteUserClass->delete_action = DeleteUserClass::DELETE_ACTION_BATCH;

        // We check if we have to block users of not
        foreach ($users as $user) {
            $arrayKey = array_search($user->email, $rawEmails);
            $emailOk = $apiService->isUserAccepted($emailsResults[$arrayKey], $conditionsSelected, $user);

            if (isset($allTests[$user->email]) && empty($allTests[$user->email]['name'])) {
                $allTests[$user->email]['name'] = Database::escapeDB($user->name);
            }

            if ($emailOk === true) {
                continue;
            }

            if ($actionSelected === 'delete_users') {
                $this->deleteOneUser($user, $emailOk);
            } else {
                $this->blockOneUser($user, $emailOk);
            }

            $message = Language::translationSprintf('ACYC_ADDRESS_NOT_OK', $banReasons[$emailOk] ?? $emailOk);
            DebugService::logMessage('individual_tests.log', $user->email.' - '.$message);

            $allTests[$user->email]['block_reason'] = Database::escapeDB($emailOk);
        }
    }

    public function blockUsers($emails, &$blockedUsers)
    {
        $users = $this->getUsersFromEmails($emails);
        if (empty($users)) return;

        $this->blockUserClass->block_action = BlockUserClass::BLOCK_ACTION_MANUAL;
        foreach ($users as $user) {
            if ($this->blockOneUser($user, 'manual')) {
                $blockedUsers[$user->email] = true;
            }
        }

        $testClass = new TestClass();
        $testClass->setBlockReason(array_column($users, 'email'), 'manual');
    }

    public function deleteUsers($emails, &$deletedUsers)
    {
        $users = $this->getUsersFromEmails($emails);
        if (empty($users)) return;

        $this->deleteUserClass->delete_action = DeleteUserClass::DELETE_ACTION_MANUAL;
        foreach ($users as $user) {
            if ($this->deleteOneUser($user, 'manual')) {
                $deletedUsers[$user->email] = true;
            }
        }

        $testClass = new TestClass();
        $testClass->setBlockReason(array_column($users, 'email'), 'manual');
    }
}
