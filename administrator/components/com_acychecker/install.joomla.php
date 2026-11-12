<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

// The structure inside the installation package unzipped in the tmp folder
include_once __DIR__.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
include_once __DIR__.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'defines.php';

use AcyChecker\Classes\ConfigurationClass;
use AcyChecker\Services\UpdateService;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Installer\Installer;

class com_acycheckerInstallerScript
{
    public function install($parent)
    {
        installAcyc();
    }

    public function update($parent)
    {
        installAcyc();
    }

    public function uninstall($parent)
    {
        uninstallAcyc();
    }

    public function preflight($type, $parent)
    {
        return true;
    }

    public function postflight($type, $parent)
    {
        return true;
    }
}

function installAcyc()
{
    $updateService = new UpdateService();
    $updateService->installTables();
    $updateService->addPref();
    $updateService->updatePref();
    $updateService->updateSQL();
    $updateService->installBackLanguage();

    $newConfig = new stdClass();
    $newConfig->installcomplete = 1;

	$configurationClass = ConfigurationClass::getConfiguration();
    $configurationClass->save($newConfig);
}

function uninstallAcyc()
{
    ?>
	AcyChecker successfully uninstalled.<br />
	If you want to completely uninstall it and remove its data, please uninstall its plugin from the Joomla Extensions Manager then run the following query on your database manager:
	<br /><br />
    <?php

    $tables = [
        'configuration',
        'test',
        'block_history',
        'delete_history',
    ];

    $db = Factory::getDbo();
    $prefix = $db->getPrefix().'acyc_';
    echo 'DROP TABLE '.$prefix.implode(', '.$prefix, $tables).';';

    ?>
	<br /><br />
	If you don't do this, you will be able to install AcyChecker again without losing your data.<br /><br />
    <?php
}
