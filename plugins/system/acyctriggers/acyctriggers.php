<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */

use AcyChecker\Services\ApiService;
use AcyChecker\Services\CronService;
use AcyCheckerCmsServices\Language;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

class plgSystemAcyctriggers extends CMSPlugin
{
    private $libraryLoaded = false;

    const INTERVAL_CRON = 86400;

    private function loadAutoloader()
    {
        if ($this->libraryLoaded) return true;

        $ds = DIRECTORY_SEPARATOR;
        $autoloadFile = rtrim(JPATH_ADMINISTRATOR, $ds).$ds.'components'.$ds.'com_acychecker'.$ds.'vendor'.$ds.'autoload.php';

        if (!file_exists($autoloadFile) || !include_once $autoloadFile) {
            $this->libraryLoaded = false;

            return false;
        }

        $definesFile = rtrim(JPATH_ADMINISTRATOR, $ds).$ds.'components'.$ds.'com_acychecker'.$ds.'defines.php';

        if (!file_exists($definesFile) || !include_once $definesFile) {
            $this->libraryLoaded = false;

            return false;
        }

        $this->libraryLoaded = true;

        return $this->libraryLoaded;
    }

    public function onUserBeforeSave($user, $isnew, $new)
    {
        if (!$isnew) {
            return true;
        }

        $db = Factory::getDbo();

        if (!in_array($db->getPrefix().'acyc_configuration', $db->getTableList())) {
            return true;
        }

        $db->setQuery('SELECT `value` FROM #__acyc_configuration WHERE `name` = "registration_integrations"');
        $integrations = explode(',', $db->loadResult());

        // The email verification is disabled in the configuration
        if (!in_array('cms', $integrations)) {
            return true;
        }

        $db->setQuery('SELECT `value` FROM #__acyc_configuration WHERE `name` = "registration_conditions"');
        $conditions = $db->loadResult();

        if (empty($conditions)) {
            return true;
        }

        $this->loadAutoloader();

        $testUser = new stdClass();
        $testUser->email = $new['email'];
        $testUser->name = $new['name'] ?? '';
        $testUser->username = $new['username'] ?? '';

        $apiService = new ApiService();
        $emailOk = $apiService->testUser($testUser, $conditions);
        if ($emailOk !== true) {
            if ($emailOk === 'blacklisted_name') {
                throw new \Exception(Language::translation('ACYC_ENTER_VALID_NAME'));
            } else {
                throw new \Exception(Language::translation('ACYC_INVALID_EMAIL_ADDRESS'));
            }
        }

        return true;
    }

    public function onAfterInitialise()
    {
        if (!empty($_REQUEST['option']) && $_REQUEST['option'] === 'com_installer') {
            return;
        }

        $db = Factory::getDbo();

        if (!in_array($db->getPrefix().'acyc_configuration', $db->getTableList())) {
            return;
        }

        $db->setQuery('SELECT `value` FROM #__acyc_configuration WHERE `name` = "license_key"');
        $licenseKey = $db->loadResult();

        if (empty($licenseKey)) {
            return;
        }

        $db->setQuery('SELECT `value` FROM #__acyc_configuration WHERE `name` = "last_cron_trigger"');
        $cronLastExecution = $db->loadResult();

        if (!empty($cronLastExecution) && $cronLastExecution > (time() - self::INTERVAL_CRON)) {
            return;
        }

        if ($this->loadAutoloader()) {
            $this->processCron();
        }
    }

    private function processCron()
    {
        $cronService = new CronService();
        $cronService->process();
    }
}
