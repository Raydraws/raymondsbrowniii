<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_privacy_dashboard
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Module\PrivacyDashboard\Administrator\Helper\PrivacyDashboardHelper;

// Only super user can view this data
if (!$app->getIdentity()->authorise('core.admin'))
{
	return;
}

// Boot component to ensure HTML helpers are loaded
$app->bootComponent('com_privacy');

// Load the privacy component language file.
$lang = $app->getLanguage();
$lang->load('com_privacy', JPATH_ADMINISTRATOR, null, false, true)
	|| $lang->load('com_privacy', JPATH_ADMINISTRATOR . '/components/com_privacy', null, false, true);

HTMLHelper::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_privacy/helpers/html');

$list = PrivacyDashboardHelper::getData();

require ModuleHelper::getLayoutPath('mod_privacy_dashboard', $params->get('layout', 'default'));
