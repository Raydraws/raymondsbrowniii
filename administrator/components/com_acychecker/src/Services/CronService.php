<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyChecker\Services;


use AcyChecker\Classes\Acymailing5UserClass;
use AcyChecker\Classes\AcymailingUserClass;
use AcyChecker\Classes\CmsUserClass;
use AcyChecker\Classes\TestClass;
use AcyChecker\Libraries\AcycObject;
use AcyCheckerCmsServices\Database;
use AcyCheckerCmsServices\Language;
use AcyCheckerCmsServices\Message;
use AcyCheckerCmsServices\Url;
use AcyCheckerCmsServices\Security;

class CronService extends AcycObject
{
    const INTERVAL_CRON = [
        'week' => 604800,
        'month' => 2419200,
    ];
    const LIMIT_EMAIL_BATCH = 5000;
    const LIMIT_EMAIL_TOTAL = 10000;

    private $acymailing5UserClass;
    private $acymailingUserClass;
    private $cmsUserClass;

    private $apiKey;

    private $cmsOffset;
    private $acymailingOffset;
    private $acymailing5Offset;

    private $numberOfEmailsInserted;
    private $remainingCreditsBatch;

    public function __construct()
    {
        parent::__construct();

        $this->apiKey = $this->config->get('license_key');

        $this->acymailing5UserClass = new Acymailing5UserClass();
        $this->acymailingUserClass = new AcymailingUserClass();
        $this->cmsUserClass = new CmsUserClass();

        $this->cmsOffset = 0;
        $this->acymailingOffset = 0;
        $this->acymailing5Offset = 0;
        $this->numberOfEmailsInserted = 0;

        $this->remainingCreditsBatch = intval($this->config->get('remaining_credits_batch', 0));
    }

    public function process()
    {
        // If we are on the callback url we don't trigger the cron
        $controller = Security::getVar('string', 'ctrl', '');
        if ($controller === 'callbackController') return;

        // If the user didn't set any conditions we do not trigger any batch
        $conditions = $this->config->get('conditions_selected');
        if (empty($conditions)) return;

        // If no api key set we don't trigger the cron
        if (empty($this->apiKey)) return;

        // If the execution timing is set to now we don't trigger the cron
        $executionTime = $this->config->get('execution_selected', 'now');
        if ($executionTime == 'now' || empty(self::INTERVAL_CRON[$executionTime])) return;

        $lastCronTrigger = $this->config->get('last_cron_trigger', '');
        $time = time();

        // We check if we have to trigger the cron
        if (!empty($lastCronTrigger) && $lastCronTrigger > $time - self::INTERVAL_CRON[$executionTime]) return;

        $this->config->save(['last_cron_trigger' => $time]);

        $this->processBatches();
    }

    public function processBatches()
    {
        if ($this->addNewEmailsToQueue()) {
            $this->sendNextBatchToAPI();
        }
    }

    private function addNewEmailsToQueue()
    {
        $this->config->load();
        // We get the config for which users to test (cms/acymailing/acymailing 5)
        $tablesSelected = $this->config->get('tables_selected');
        if (empty($tablesSelected)) return false;
        $tablesSelected = explode(',', $tablesSelected);

        // for the CMS users
        if (in_array('cms', $tablesSelected)) {
            do {
                $batchCms = $this->batchCmsUsers(true);
            } while ($batchCms);
        }

        // for AcyMailing users
        if (in_array('acymailing', $tablesSelected)) {
            do {
                $batchAcymailing = $this->batchAcymailingUsers(true);
            } while ($batchAcymailing);
        }

        // for AcyMailing 5 users
        if (in_array('acymailing5', $tablesSelected)) {
            do {
                $batchAcymailing5 = $this->batchAcymailing5Users(true);
            } while ($batchAcymailing5);
        }

        if (empty($this->numberOfEmailsInserted)) {
            Message::enqueueMessage(Language::translation('ACYC_NO_ADDRESS_TO_TEST'), 'info');

            return false;
        } else {
            Message::enqueueMessage(Language::translationSprintf('ACYC_X_EMAIL_ADDRESSES_QUEUED', $this->numberOfEmailsInserted), 'info');
        }

        return true;
    }

    private function batchAcymailingUsers($onlyNew)
    {
        //We get the lists filter
        $listsFilter = $this->config->get('table_filter_acymailing', []);
        if (!empty($listsFilter)) {
            $listsFilter = explode(',', $listsFilter);
        }

        // Get users for a batch
        if ($onlyNew) {
            $users = $this->acymailingUserClass->getUsers(0, self::LIMIT_EMAIL_BATCH, true, $onlyNew, $listsFilter);
        } else {
            $users = $this->acymailingUserClass->getUsers($this->acymailingOffset, self::LIMIT_EMAIL_BATCH, true, $onlyNew, $listsFilter);
            $this->acymailingOffset += count($users);
        }

        // If no user we don't do anything
        if (empty($users)) {
            return false;
        }

        return $this->newBatch($users);
    }

    private function batchAcymailing5Users($onlyNew)
    {
        //We get the lists filter
        $listsFilter = $this->config->get('table_filter_acymailing5', []);
        if (!empty($listsFilter)) {
            $listsFilter = explode(',', $listsFilter);
        }

        // Get users for a batch
        if ($onlyNew) {
            $users = $this->acymailing5UserClass->getUsers(0, self::LIMIT_EMAIL_BATCH, true, $onlyNew, $listsFilter);
        } else {
            $users = $this->acymailing5UserClass->getUsers($this->acymailing5Offset, self::LIMIT_EMAIL_BATCH, true, $onlyNew, $listsFilter);
            $this->acymailing5Offset += count($users);
        }

        // If no user we don't do anything
        if (empty($users)) {
            return false;
        }

        return $this->newBatch($users);
    }

    private function batchCmsUsers($onlyNew)
    {
        //We get the lists filter
        $groupsFilter = $this->config->get('table_filter_cms');
        if (!empty($groupsFilter)) {
            $groupsFilter = explode(',', $groupsFilter);
        }

        // Get users for a batch
        if ($onlyNew) {
            $users = $this->cmsUserClass->getUsers(0, self::LIMIT_EMAIL_BATCH, true, $onlyNew, $groupsFilter);
        } else {
            $users = $this->cmsUserClass->getUsers($this->cmsOffset, self::LIMIT_EMAIL_BATCH, true, $onlyNew, $groupsFilter);
            $this->cmsOffset += count($users);
        }

        // If no user we don't do anything
        if (empty($users)) {
            return false;
        }

        return $this->newBatch($users);
    }

    private function newBatch($users)
    {
        // Prepare the query to insert the test
        $insertQuery = 'INSERT IGNORE INTO #__acyc_test (`email`, `name`, `date`, `current_step`) VALUES ';
        $allTests = [];

        // Prepare the date to set on each test
        $dateTimeNow = new \DateTime('NOW');
        $dateTimeNow->setTimezone(new \DateTimeZone('UTC'));
        $dateNow = $dateTimeNow->format('Y-m-d H:i:s');

        // We fill all the test values
        $testClass = new TestClass();
        foreach ($users as $user) {
            $oneTest = [
                Database::escapeDB($user->email),
                Database::escapeDB($user->name),
                Database::escapeDB($dateNow),
                $testClass::STEP['pending'],
            ];
            $allTests[] = implode(',', $oneTest);
        }
        $insertQuery .= '('.implode('),(', $allTests).')';

        // We execute the query to store the email tested
        $nbEmailsInserted = Database::query($insertQuery);

        if (!empty($nbEmailsInserted)) {
            $this->numberOfEmailsInserted += $nbEmailsInserted;
        }

        return true;
    }

    public function sendNextBatchToAPI($messages = true)
    {
        $this->config->load();

        if (empty($this->remainingCreditsBatch)) {
            if ($messages) Message::enqueueMessage(Language::translation('ACYC_DONE_WHEN_CREDITS'), 'warning');

            return;
        }

        // Prepare the emails to send to the API
        $nbEmailsToSend = self::LIMIT_EMAIL_BATCH;
        if ($this->remainingCreditsBatch < $nbEmailsToSend) {
            $nbEmailsToSend = $this->remainingCreditsBatch;
        }

        $testClass = new TestClass();
        $currentlyTesting = $testClass->getNbResults($testClass::STEP['in_progress']);

        if ($currentlyTesting >= self::LIMIT_EMAIL_TOTAL) {
            if ($messages) Message::enqueueMessage(Language::translation('ACYC_WILL_TEST_WHEN_PREVIOUS_BATCH_END'), 'info');

            return;
        }

        if ($nbEmailsToSend + $currentlyTesting > self::LIMIT_EMAIL_TOTAL) {
            $nbEmailsToSend = self::LIMIT_EMAIL_TOTAL - $currentlyTesting;
        }

        $emails = $testClass->getNextBatch($nbEmailsToSend);

        if (empty($emails)) {
            if ($messages) Message::enqueueMessage(Language::translation('ACYC_NO_ADDRESS_TO_TEST'), 'info');

            return;
        }

        // We call the API
        $apiService = new ApiService();
        $result = $apiService->dispatchBatch($emails);

        if (!$result['success']) {
            DebugService::logMessage('batch_tests.log', 'Error while creating batch - '.$result['message']);
            Message::enqueueMessage($result['message'], 'warning');

            return;
        }

        $nbEmailsSent = count($emails);
        $this->remainingCreditsBatch -= $nbEmailsSent;

        // We store the new batch ID in the configuration
        $urlsBatch = $this->config->get('urls_results_batch', '[]');
        $urlsBatch = json_decode($urlsBatch, true);
        $urlsBatch[$result['data']['test_id']] = [
            'url' => $result['data']['url_result'],
            'last_check' => time(),
            'attempts' => 0,
        ];
        $this->config->save(['urls_results_batch' => json_encode($urlsBatch)]);

        $testClass->setBatchId($result['data']['test_id'], $emails);

        $apiService->refreshCreditsUsed();

        $message = Language::translationSprintf('ACYC_X_EMAIL_ADDRESSES_SENT', $nbEmailsSent);
        $message .= ' <a href="'.Url::completeLink('tests').'">'.Language::translation('ACYC_RESULTS').'</a>';
        Message::enqueueMessage($message, 'info');
    }
}
