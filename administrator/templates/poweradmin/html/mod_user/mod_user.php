<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_user
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\Database\DatabaseInterface;
use Joomla\Module\Multilangstatus\Administrator\Helper\MultilangstatusAdminHelper;

$db       = Factory::getContainer()->get(DatabaseInterface::class);
$user     = $app->getIdentity();
$sitename = htmlspecialchars($app->get('sitename', ''), ENT_QUOTES, 'UTF-8');

// Check if the multilangstatus module is present and enabled in the site
if (class_exists(MultilangstatusAdminHelper::class) && MultilangstatusAdminHelper::isEnabled($app, $db))
{
	// Publish and display the module
	MultilangstatusAdminHelper::publish($app, $db);

	if (Multilanguage::isEnabled($app, $db))
	{
		$module                          = ModuleHelper::getModule('mod_multilangstatus');
		$multilanguageStatusModuleOutput = ModuleHelper::renderModule($module);
	}
}

require ModuleHelper::getLayoutPath('mod_user', $params->get('layout', 'default'));
