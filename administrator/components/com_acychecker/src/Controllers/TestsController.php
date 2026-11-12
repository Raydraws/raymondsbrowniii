<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyChecker\Controllers;

use AcyChecker\Classes\Acymailing5UserClass;
use AcyChecker\Classes\AcymailingUserClass;
use AcyChecker\Classes\CmsUserClass;
use AcyChecker\Classes\DeleteUserClass;
use AcyChecker\Classes\TestClass;
use AcyChecker\Libraries\AcycController;
use AcyChecker\Services\DatabaseService;
use AcyChecker\Services\FileService;
use AcyChecker\Services\ApiService;
use AcyChecker\Services\PaginationService;
use AcyChecker\Services\TestService;
use AcyCheckerCmsServices\Ajax;
use AcyCheckerCmsServices\Database;
use AcyChecker\Services\HttpService;
use AcyCheckerCmsServices\Form;
use AcyCheckerCmsServices\Language;
use AcyCheckerCmsServices\Message;
use AcyCheckerCmsServices\Router;
use AcyCheckerCmsServices\Security;
use AcyCheckerCmsServices\Url;

class TestsController extends AcycController
{
    const PAGINATION_NAME = 'test_pagination';
    const DEFAULT_ELEMENTS_PER_PAGE = 20;
    const SEPARATOR = '","';
    const EOL = "\r\n";
    const BEFORE = '"';
    const AFTER = '"';

    private $testClass;

    public function __construct()
    {
        parent::__construct();

        $this->name = 'Tests';
        $this->defaultTask = 'listing';

        $this->testClass = new TestClass();
    }

    private function getNumberOfElementsToDisplay()
    {
        $selectValue = Security::getVar('int', 'acyc_element_per_page', $this->config->get('listing_element_page', self::DEFAULT_ELEMENTS_PER_PAGE));

        $this->config->save(['listing_element_page' => $selectValue]);

        return $selectValue;
    }

    public function listing()
    {
        $this->layout = 'listing';

        $data = [
            'per_page' => $this->getNumberOfElementsToDisplay(),
            'page' => $this->getVarFiltersListing('int', self::PAGINATION_NAME, 1),
        ];

        $this->prepareFilters($data);
        $this->prepareBlockReasons($data);
        $this->prepareTestsElements($data);
        $this->preparePagination($data);
        $this->prepareTexts($data);
        $this->prepareStatus($data);
        $this->prepareHandleModal($data);
        $this->displayInfoInProgress();

        $this->breadcrumb[Language::translation('ACYC_TESTS')] = Url::completeLink('tests');

        $this->display($data);
    }

    private function prepareHandleModal(&$data)
    {
        $tablesSelected = explode(',', $this->config->get('tables_selected'));

        $actionSelected = $this->config->get('action_selected');
        if (!in_array($actionSelected, ['block_users', 'delete_users'])) {
            $actionSelected = 'block_users';
        }

        $conditionsSelected = $this->config->get('conditions_selected');
        $conditionsSelected = empty($conditionsSelected) ? [] : explode(',', $conditionsSelected);

        $data['current_config'] = [
            'tables_selected' => $tablesSelected,
            'action_selected' => $actionSelected,
            'conditions_selected' => $conditionsSelected,
        ];

        $data['tables_select'] = DatabaseService::getTablesForSelect();
        $data['action_select'] = DatabaseService::getActionsForSelect(false);
        $data['condition_select'] = DatabaseService::getConditionsForSelect();
        $data['condition_trust'] = DatabaseService::getConditionsTrustForSelect();
    }

    private function prepareFilters(&$data)
    {
        $data['search'] = $this->getVarFiltersListing('string', 'search', '');
        $data['current_status'] = $this->getVarFiltersListing('string', 'status', '');
        $data['ordering'] = $this->getVarFiltersListing('string', 'tests_ordering', 'date');
        $data['orderingSortOrder'] = $this->getVarFiltersListing('string', 'tests_ordering_sort_order', 'desc');
    }

    private function prepareBlockReasons(&$data)
    {
        $testService = new TestService();
        $data['block_reasons'] = $testService->getBanReasons();
    }

    private function prepareTestsElements(&$data)
    {
        $data['offset'] = ($data['page'] - 1) * $data['per_page'];
        $results = $this->testClass->getMatchingElements($data);
        $data = array_merge($data, $results);

        $deleteUserClass = new DeleteUserClass();
        $cmsUserClass = new CmsUserClass();
        $acyUserClass = new AcymailingUserClass();
        $acy5UserClass = new Acymailing5UserClass();
        foreach ($data['elements'] as $test) {
            $deleteHistory = $deleteUserClass->getOneById($test->email);
            $test->removed = !empty($deleteHistory);

            $test->siteUserLink = $cmsUserClass->getUserLink($test->email);
            $test->acyUserLink = $acyUserClass->getUserLink($test->email);
            $test->acy5UserLink = $acy5UserClass->getUserLink($test->email);
        }
    }

    private function preparePagination(&$data)
    {
        $data['pagination'] = new PaginationService($data['page'], $data['total'], $data['per_page'], self::PAGINATION_NAME);
    }

    private function prepareTexts(&$data)
    {
        $data['test_result_texts'] = [
            ApiService::TRUST_RESULT_NOT_EXISTING => '<span style="color: red">'.Language::translation('ACYC_NOT_EXISTING').'</span>',
            ApiService::TRUST_RESULT_RISKY => Language::translation('ACYC_RISKY'),
            ApiService::TRUST_RESULT_POSSIBLE => Language::translation('ACYC_POSSIBLE'),
            ApiService::TRUST_RESULT_VALID => Language::translation('ACYC_VALID'),
        ];
    }

    private function prepareStatus(&$data)
    {
        $data['status'] = [
            'all' => [
                'text' => Language::translation('ACYC_ALL'),
                'number' => $this->testClass->getStepNumber('all', $data),
            ],
            'finished' => [
                'text' => Language::translation('ACYC_FINISHED'),
                'number' => $this->testClass->getStepNumber('finished', $data),
            ],
            'in_progress' => [
                'text' => Language::translation('ACYC_IN_PROGRESS'),
                'number' => $this->testClass->getStepNumber('in_progress', $data),
            ],
            'pending' => [
                'text' => Language::translation('ACYC_PENDING'),
                'number' => $this->testClass->getStepNumber('pending', $data),
            ],
            'failed' => [
                'text' => Language::translation('ACYC_FAILED'),
                'number' => $this->testClass->getStepNumber('failed', $data),
            ],
        ];

        $testClass = new TestClass();
        $data['statuses'] = $testClass->getStatusLabels();
    }

    public function displayInfoInProgress()
    {
        $tests = $this->config->get('urls_results_batch', '[]');
        $tests = json_decode($tests, true);
        if (!empty($tests)) {
            $nbBatchs = count($tests);
            Message::enqueueMessage(Language::translationSprintf('ACYC_TEST_IN_PROGRESS_X_BATCH', $nbBatchs), 'info');
        }
    }

    public function doexport()
    {
        $settings = [];
        $this->prepareFilters($settings);

        $testClass = new TestClass();
        $query = 'SELECT test.* FROM #__acyc_test AS test';
        $query .= $testClass->buildFiltersFromSettings($settings);
        $query .= ' ORDER BY test.'.Database::secureDBColumn($settings['ordering']).' '.Database::secureDBColumn($settings['orderingSortOrder']);

        $columns = Database::loadResultArray('SHOW COLUMNS FROM #__acyc_test');

        // We replace the raw_results by the email suggestions if any
        $rawPos = array_search('raw_result', $columns);
        $columns = array_replace($columns, [$rawPos => ApiService::API_TEST_KEY_DOMAIN_SUGGESTIONS]);

        // We don't export the batch_id column
        $batchPos = array_search('batch_id', $columns);
        unset($columns[$batchPos]);

        $fileName = 'export_acychecker_'.date('Y-m-d');
        $this->exportElements($query, $columns, $fileName);
    }

    public function doExportBlockedUsers()
    {
        $query = 'SELECT * FROM #__acyc_block_history';
        $columns = Database::loadResultArray('SHOW COLUMNS FROM #__acyc_block_history');
        $fileName = 'export_acychecker_blocked_users_'.date('Y-m-d');
        $this->exportElements($query, $columns, $fileName);
    }

    public function doExportDeletedUsers()
    {
        $query = 'SELECT * FROM #__acyc_delete_history';
        $columns = Database::loadResultArray('SHOW COLUMNS FROM #__acyc_delete_history');
        $fileName = 'export_acychecker_deleted_users_'.date('Y-m-d');
        $this->exportElements($query, $columns, $fileName);
    }

    private function getExportLimit()
    {
        // Getting X users per batch based on the memory limit
        $serverLimit = FileService::bytes(ini_get('memory_limit'));
        if ($serverLimit > 150000000) {
            return 50000;
        } elseif ($serverLimit > 80000000) {
            return 15000;
        } else {
            return 5000;
        }
    }

    private function exportElements($query, $columns, $fileName)
    {
        // Getting X users per batch based on the memory limit
        $exportLimitPerBatch = $this->getExportLimit();

        $testClass = new TestClass();
        $stepLabels = $testClass->getStatusLabels();

        error_reporting(E_ALL);
        @ini_set('display_errors', 1);

        @ob_get_clean();
        HttpService::setDownloadHeaders($fileName);

        echo self::BEFORE.implode(self::SEPARATOR, $columns).self::AFTER.self::EOL;

        $start = 0;
        do {
            try {
                $elements = Database::loadObjectList($query.' LIMIT '.intval($start).', '.intval($exportLimitPerBatch));
            } catch (\Exception $e) {
                $elements = false;
            }

            // There is no other user to export, end here
            if (empty($elements)) {
                if (empty($start)) {
                    $completeLink = rtrim(Url::completeLink('tests', false, true), '&noheader=1');
                    Message::enqueueMessage(Language::translation('ACYC_NO_DATA_TO_EXPORT'), 'warning');
                    Router::redirect($completeLink);
                }
                break;
            }

            $start += $exportLimitPerBatch;
            foreach ($elements as $oneElement) {
                $line = [];
                if (isset($oneElement->current_step)) {
                    $oneElement->current_step = $stepLabels[$oneElement->current_step];
                }

                foreach ($columns as $oneColumn) {
                    if ($oneColumn === ApiService::API_TEST_KEY_DOMAIN_SUGGESTIONS && !empty($oneElement->raw_result)) {
                        $rawResult = json_decode($oneElement->raw_result, true);
                        if (empty($rawResult[ApiService::API_TEST_KEY_DOMAIN_SUGGESTIONS])) {
                            $oneElement->$oneColumn = '';
                        } elseif (is_string($rawResult[ApiService::API_TEST_KEY_DOMAIN_SUGGESTIONS])) {
                            $oneElement->$oneColumn = str_replace('|', ',', $rawResult[ApiService::API_TEST_KEY_DOMAIN_SUGGESTIONS]);
                        } else {
                            $oneElement->$oneColumn = implode(',', $rawResult[ApiService::API_TEST_KEY_DOMAIN_SUGGESTIONS]);
                        }
                    }

                    if (empty($oneElement->$oneColumn)) {
                        $oneElement->$oneColumn = '';
                    }

                    $line[] = htmlspecialchars($oneElement->$oneColumn, ENT_QUOTES);
                }

                echo self::BEFORE.implode(self::SEPARATOR, $line).self::AFTER.self::EOL;
                unset($line);
            }

            unset($elements);
        } while (true);

        exit;
    }

    public function clearTested()
    {
        $testClass = new TestClass();

        $testClass->deleteAllTested();

        $this->listing();
    }

    public function cancelPending()
    {
        $testClass = new TestClass();
        $testClass->deletePending();

        $this->listing();
    }

    public function deleteResults()
    {
        Form::checkToken();
        $emails = Security::getVar('array', 'elements_checked', []);

        if (empty($emails)) {
            Message::enqueueMessage(Language::translation('ACYC_SELECT_A_USER'), 'error');
        } else {
            $testClass = new TestClass();
            $testClass->delete($emails);

            Message::enqueueMessage(Language::translation('ACYC_RESULTS_DELETED'));
        }

        $this->listing();
    }

    private function executeActionOnUsers($action)
    {
        Form::checkToken();
        $emails = Security::getVar('array', 'elements_checked', []);

        if (empty($emails)) {
            Message::enqueueMessage(Language::translation('ACYC_SELECT_A_USER'), 'error');
        } else {
            $tablesSelected = $this->config->get('tables_selected');
            if (empty($tablesSelected)) {
                Message::enqueueMessage(Language::translation('ACYC_SELECT_A_TABLE'), 'error');
            } else {
                $tablesSelected = explode(',', $tablesSelected);

                $blockedUsers = [];
                if (in_array('acymailing', $tablesSelected)) {
                    $acymailingUserClass = new AcymailingUserClass();
                    $acymailingUserClass->$action($emails, $blockedUsers);
                }

                if (in_array('acymailing5', $tablesSelected)) {
                    $acymailing5UserClass = new Acymailing5UserClass();
                    $acymailing5UserClass->$action($emails, $blockedUsers);
                }

                if (in_array('cms', $tablesSelected)) {
                    $cmsUserClass = new CmsUserClass();
                    $cmsUserClass->$action($emails, $blockedUsers);
                }

                return count($blockedUsers);
            }
        }

        return false;
    }

    public function blockUsers()
    {
        $nbUsersBlocked = $this->executeActionOnUsers('blockUsers');
        if ($nbUsersBlocked !== false) {
            Message::enqueueMessage(Language::translationSprintf('ACYC_USERS_BLOCKED', $nbUsersBlocked));
        }

        $this->listing();
    }

    public function unblockUsers()
    {
        $nbUsersUnblocked = $this->executeActionOnUsers('unblockUsers');
        if ($nbUsersUnblocked !== false) {
            Message::enqueueMessage(Language::translationSprintf('ACYC_USERS_UNBLOCKED', $nbUsersUnblocked));
        }

        $this->listing();
    }

    public function deleteUsers()
    {
        $nbUsersDeleted = $this->executeActionOnUsers('deleteUsers');
        if ($nbUsersDeleted !== false) {
            Message::enqueueMessage(Language::translationSprintf('ACYC_USERS_DELETED', $nbUsersDeleted));
        }

        $this->listing();
    }

    public function ajaxGetTotalResults()
    {
        $testClass = new TestClass();
        $totalResults = $testClass->getNbResults($testClass::STEP['finished']);
        if (empty($totalResults)) {
            Ajax::sendAjaxResponse(Language::translation('ACYC_NO_RESULTS_FOUND'), [], false);
        } else {
            Ajax::sendAjaxResponse('', ['totalResults' => $totalResults]);
        }
    }

    public function ajaxHandleResults()
    {
        Form::checkToken();

        $action = Security::getVar('string', 'userAction');
        if (!in_array($action, [TestService::ACTION_BLOCK_USERS, TestService::ACTION_DELETE_USERS])) {
            Ajax::sendAjaxResponse(Language::translation('ACYC_ACTION_NOT_FOUND'), [], false);
        }

        $selectedTables = explode(',', Security::getVar('string', 'userTables'));
        $allowedTables = array_column(DatabaseService::getTablesForSelect(), 'value');
        $tablesSelected = array_intersect($selectedTables, $allowedTables);

        if (empty($tablesSelected)) {
            Ajax::sendAjaxResponse(Language::translation('ACYC_NO_USER_TABLE_SELECTED'), [], false);
        }

        $selectedConditions = explode(',', Security::getVar('string', 'actionConditions'));
        $allowedConditions = array_column(DatabaseService::getConditionsForSelect(), 'value');
        $allowedTrustConditions = array_column(DatabaseService::getConditionsTrustForSelect(), 'value');
        $conditions = array_intersect($selectedConditions, array_merge($allowedConditions, $allowedTrustConditions));

        if (empty($conditions)) {
            Ajax::sendAjaxResponse(Language::translation('ACYC_PLEASE_SELECT_A_CONDITION'), [], false);
        }

        $notAllowedConditions = array_diff($selectedConditions, $conditions);
        if (!empty($notAllowedConditions)) {
            Ajax::sendAjaxResponse(Language::translationSprintf('ACYC_CONDITION_NOT_ALLOWED', implode(', ', $notAllowedConditions)), [], false);
        }

        $selectedUsers = Security::getVar('array', 'selectedUsers', []);
        $testClass = new TestClass();
        if (!empty($selectedUsers)) {
            $resultsToHandle = $testClass->getResultsByEmail($selectedUsers);
        } else {
            $start = Security::getVar('int', 'start');
            $limit = Security::getVar('int', 'limit');

            if (empty($limit)) {
                Ajax::sendAjaxResponse(Language::translation('ACYC_ERROR_OCCURRED'), [], false);
            }

            $resultsToHandle = $testClass->getTestResults($start, $limit);
        }

        if (empty($resultsToHandle)) {
            Ajax::sendAjaxResponse(Language::translation('ACYC_ERROR_OCCURRED'), [], false);
        }

        // This variable will contain the email addresses of the users matching the conditions
        $usersChanged = [];
        if (in_array('acymailing', $tablesSelected)) {
            $acymailingUserClass = new AcymailingUserClass();
            $acymailingUserClass->handleBatchCallback($action, $conditions, $resultsToHandle, $usersChanged);
        }

        if (in_array('acymailing5', $tablesSelected)) {
            $acymailing5UserClass = new Acymailing5UserClass();
            $acymailing5UserClass->handleBatchCallback($action, $conditions, $resultsToHandle, $usersChanged);
        }

        if (in_array('cms', $tablesSelected)) {
            $cmsUserClass = new CmsUserClass();
            $cmsUserClass->handleBatchCallback($action, $conditions, $resultsToHandle, $usersChanged);
        }

        if (!empty($usersChanged)) {
            $testClass->addBlockReason($usersChanged);
        }

        Ajax::sendAjaxResponse(
            '',
            [
                'handledResults' => count($resultsToHandle),
                'usersChanged' => count($usersChanged),
            ]
        );
    }

    public function ajaxGetNbMatchingUsers()
    {
        $selectedUsers = Security::getVar('array', 'selectedUsers', []);
        $selectedConditions = explode(',', Security::getVar('string', 'actionConditions'));
        $allowedConditions = array_column(DatabaseService::getConditionsForSelect(), 'value');
        $allowedTrustConditions = array_column(DatabaseService::getConditionsTrustForSelect(), 'value');
        $conditions = array_intersect($selectedConditions, array_merge($allowedConditions, $allowedTrustConditions));

        $testClass = new TestClass();
        Ajax::sendAjaxResponse(
            '',
            [
                'matchingUsersNb' => $testClass->getNbMatchingUsers($conditions, $selectedUsers),
            ]
        );
    }
}
