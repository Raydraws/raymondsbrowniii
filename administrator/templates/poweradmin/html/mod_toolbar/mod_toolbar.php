<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_toolbar
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Toolbar\Toolbar;

$toolbarFile = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/PoweradminToolbar/autoload.php';
if (is_file($toolbarFile))
{
	require_once $toolbarFile;
}

$toolbar = Toolbar::getInstance('toolbar')->render();

require ModuleHelper::getLayoutPath('mod_toolbar', $params->get('layout', 'default'));
