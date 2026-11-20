<?php
/**
 * @package   acymailing
 * @copyright Copyright (c)2009-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */

use AcyMailing\Helpers\UpdateHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerScript;

defined('_JEXEC') or die();

class Pkg_AcymailingInstallerScript extends InstallerScript
{
    public function preflight($type, $parent)
    {
        if (!parent::preflight($type, $parent)) {
            return false;
        }

        if (!in_array($type, ['install', 'update'])) {
            return true;
        }

        if (version_compare(PHP_VERSION, '7.4.0', '<')) {
            $app = Factory::getApplication();
            $app->enqueueMessage('This version of AcyMailing requires at least PHP 7.4.0, it is time to update the PHP version of your server!', 'error');

            return false;
        }

        $jversion = preg_replace('#[^0-9\.]#i', '', JVERSION);
        if (version_compare($jversion, '3.0.0', '<')) {
            $app = Factory::getApplication();
            $app->enqueueMessage('This version of AcyMailing requires at least Joomla 3, please upgrade your website first!', 'error');

            return false;
        }

        return true;
    }

    public function postflight($type, $parent)
    {
        if (in_array($type, ['install', 'update'])) {
            $this->installAcym();
            $this->addUpdateSite();

            if ($type === 'install') {
                $this->activePlugins();
            }
        }

        return true;
    }

    public function uninstall($parent)
    {
        ?>
		AcyMailing successfully uninstalled.<br /><br />
		If you want to completely uninstall AcyMailing and remove its data, run the following query on your database manager:
		<br /><br />
        <?php
        $tables = [
            'mail_stat_detail',
            'scenario_history_line',
            'scenario_queue',
            'scenario_process',
            'scenario_step',
            'scenario',
            'mail_archive',
            'mailbox_action',
            'custom_zone',
            'mail_override',
            'followup_has_mail',
            'followup',
            'segment',
            'form',
            'plugin',
            'action',
            'condition',
            'history',
            'rule',
            'user_has_field',
            'field',
            'url_click',
            'url',
            'user_stat',
            'mail_stat',
            'queue',
            'mail_has_list',
            'tag',
            'step',
            'automation',
            'user_has_list',
            'campaign',
            'list',
            'mail',
            'configuration',
            'user',
        ];

        $db = Factory::getDbo();
        $prefix = $db->getPrefix().'acym_';
        echo 'DROP TABLE '.$prefix.implode(', '.$prefix, $tables).';';
        ?>
		<br /><br />
		If you don't do this, you will be able to install AcyMailing again without losing your data.<br />
		Please note that you don't have to uninstall AcyMailing to install a new version, simply install it over the current version.<br /><br />
        <?php
    }

    private function installAcym()
    {
        try {
            $this->loadAcyMailingLibrary();
        } catch (Exception $e) {
            echo 'Initialization error, please re-install';

            return;
        }

        // The installation steps may take a while when updating from an old version
        acym_increasePerf();

        $updateHelper = new UpdateHelper();
        $updateHelper->installTables();
        $updateHelper->addPref();
        $updateHelper->updatePref();
        acym_config(true);
        $updateHelper->updateSQL();
        $updateHelper->checkDB();

        $updateHelper->installList();
        $updateHelper->installNotifications();

        if ($updateHelper->firstInstallation) {
            $updateHelper->installTemplates();
            $updateHelper->installDefaultAutomations();
        }

        $updateHelper->deleteNewSplashScreenInstall();

        $updateHelper->installFields();
        $updateHelper->installLanguages();
        $updateHelper->installBackLanguages();
        $updateHelper->installBounceRules();
        $updateHelper->installAddons();
        $updateHelper->installOverrideEmails();
        $updateHelper->updateAddons();

        $config = acym_config();
        $config->saveConfig(['installcomplete' => 1]);
    }

    private function addUpdateSite(): void
    {
        if (!$this->loadAcyMailingLibrary()) {
            return;
        }

        acym_syncUpdateSite();
    }

    private function activePlugins()
    {
        $jversion = preg_replace('#[^0-9\.]#i', '', JVERSION);
        $method = version_compare($jversion, '4.0.0', '>=') ? 'execute' : 'query';

        $db = Factory::getDbo();
        $db->setQuery(
            'UPDATE `#__extensions` 
            SET enabled = 1 
            WHERE type = "plugin" 
              AND element IN ("acymtriggers", "jceacym")'
        );
        $db->$method();
    }

    private function loadAcyMailingLibrary(): bool
    {
        $ds = DIRECTORY_SEPARATOR;

        if (!include_once rtrim(JPATH_ADMINISTRATOR, $ds).$ds.'components'.$ds.'com_acym'.$ds.'Core'.$ds.'init.php') {
            return false;
        }

        return true;
    }
}
