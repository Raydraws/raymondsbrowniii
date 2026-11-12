<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_submenu
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Component\Menus\Administrator\Helper\MenusHelper;
use Joomla\Module\Submenu\Administrator\Menu\Menu;

$menutype = $params->get('menutype', '*');
$root     = false;

if ($menutype === '*')
{
	$name   = $params->get('preset', 'system');
	$root = MenusHelper::loadPreset($name);
}
else
{
	$root = MenusHelper::getMenuItems($menutype, true);
}

if ($root && $root->hasChildren())
{
	Factory::getLanguage()->load(
		'mod_menu',
		JPATH_ADMINISTRATOR,
		Factory::getLanguage()->getTag(),
		true
	);

	Menu::preprocess($root);

	// Render the module layout
	require ModuleHelper::getLayoutPath('mod_submenu', $params->get('layout', 'default'));
}
