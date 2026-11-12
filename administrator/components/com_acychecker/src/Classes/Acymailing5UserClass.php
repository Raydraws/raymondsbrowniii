<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyChecker\Classes;

use AcyChecker\Libraries\AcycIntegrationClass;
use AcyChecker\Services\DebugService;
use AcyChecker\Services\SecurityService;
use AcyCheckerCmsServices\Database;
use AcyCheckerCmsServices\Extension;

class Acymailing5UserClass extends AcycIntegrationClass
{
    private $acy5UserClass;

    public function countUsers()
    {
        return Database::loadResult('SELECT COUNT(*) FROM #__acymailing_subscriber');
    }

    public function getLastYearUsers()
    {
        $lastYear = strtotime('-1 year');

        return Database::loadResult('SELECT COUNT(*) FROM #__acymailing_subscriber WHERE created > '.Database::escapeDB($lastYear));
    }

    public function getUsers($offset = 0, $limit = 5000, $fromCron = false, $onlyNew = false, $lists = [])
    {
        $query = 'SELECT `user`.email, `user`.name from #__acymailing_subscriber AS `user` ';

        // We filter the users if we need to
        $filters = [];

        // If we selected list we only select users subscribed to it
        if (!empty($lists)) {
            SecurityService::arrayToInteger($lists);
            $query .= 'JOIN #__acymailing_listsub AS user_list ON `user`.subid = user_list.subid ';
            $filters[] = 'user_list.listid IN ('.implode(',', $lists).')';
            $filters[] = 'user_list.status = 1';
        }

        if ($onlyNew) {
            $filters[] = ' `user`.email COLLATE utf8mb3_unicode_ci NOT IN (SELECT email COLLATE utf8mb4_unicode_ci FROM #__acyc_test)';
        }

        if ($fromCron) {
            $filters[] = '`user`.enabled = 1';
        }

        $queryWhere = empty($filters) ? '' : ' WHERE ('.implode(') AND (', $filters).')';

        $query .= ' '.$queryWhere.' LIMIT '.$offset.','.$limit;

        return Database::loadObjectList($query);
    }

    public function unblockUsers($emails, &$unblockedUsers)
    {
        $users = $this->getUsersFromEmails($emails);
        if (empty($users)) return;

        $emails = array_column($users, 'email');
        $safeEmails = array_map('AcyCheckerCmsServices\Database::escapeDB', $emails);

        try {
            Database::query('UPDATE #__acymailing_subscriber SET enabled = 1 WHERE email IN ('.implode(',', $safeEmails).')');

            $testClass = new TestClass();
            $testClass->setBlockReason($emails, null);

            foreach ($emails as $email) {
                $unblockedUsers[$email] = true;
            }
        } catch (\Exception $exception) {
            DebugService::logMessage('callback_controller.log', 'Error on request to unblock AcyMailing 5 users - '.$exception->getMessage());
        }
    }

    public function getUserLink($email): string
    {
        $user = $this->getUsersFromEmails([$email]);

        if (empty($user) || !$this->loadAcyMailing()) {
            return '';
        }

        return acymailing_completeLink('subscriber&task=edit&subid='.$user[0]->subid);
    }

    protected function blockOneUser($user, $reason)
    {
        $this->blockUserClass->recordBlockAction($user->email, $reason);

        try {
            return Database::query('UPDATE #__acymailing_subscriber SET enabled = 0 WHERE email = '.Database::escapeDB($user->email));
        } catch (\Exception $exception) {
            DebugService::logMessage('callback_controller.log', 'Error on request to block AcyMailing 5 users - '.$exception->getMessage());
        }

        return false;
    }

    protected function deleteOneUser($user, $reason)
    {
        if (empty($this->acy5UserClass)) {
            if (!$this->loadAcyMailing()) {
                return false;
            }

            $this->acy5UserClass = acymailing_get('class.subscriber');
        }

        $this->deleteUserClass->recordDeleteAction($user->email, $reason);

        return $this->acy5UserClass->delete($user->subid);
    }

    protected function getUsersFromEmails($emails)
    {
        if (empty($emails) || !$this->loadAcyMailing()) {
            return [];
        }

        $emails = array_map('AcyCheckerCmsServices\Database::escapeDB', $emails);

        try {
            return Database::loadObjectList('SELECT * FROM #__acymailing_subscriber WHERE email IN ('.implode(',', $emails).')');
        } catch (\Exception $exception) {
            DebugService::logMessage('callback_controller.log', 'Error on getting AcyMailing 5 users from results - '.$exception->getMessage());

            return [];
        }
    }

    private function loadAcyMailing(): bool
    {
        if (!Extension::isExtensionActive(ACYC_CMS === 'joomla' ? 'com_acymailing' : 'acymailing5/index.php')) {
            return false;
        }

        if (function_exists('acymailing_get')) {
            return true;
        }

        $initPath = ACYC_CMS === 'joomla' ? rtrim(JPATH_ADMINISTRATOR, DS).DS.'components'.DS.'com_acymailing' : WP_PLUGIN_DIR.DS.'acymailing5'.DS.'back';
        $initPath .= DS.'helpers'.DS.'helper.php';

        if (include_once $initPath) {
            return true;
        } else {
            return false;
        }
    }
}
