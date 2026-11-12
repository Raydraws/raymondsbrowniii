<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

if (!class_exists('JHtmlPoweradmin'))
{
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

/**
 * =========================================================================================================
 * IMPORTANT: The scope of this layout file is the `var  \Joomla\Module\Menu\Administrator\Menu\CssMenu` object
 * and NOT the module context.
 * =========================================================================================================
 */
/** @var  \Joomla\Module\Menu\Administrator\Menu\CssMenu  $this */
$class   = '';

// Build the CSS class suffix
if (!$this->enabled)
{
	$class = "disabled";
}
elseif ($current->type == 'separator')
{
	$class = $current->title ? "menuitem-group" : "divider";
}
elseif ($current->hasChildren())
{
	$class = "dropdown-submenu";

	if ($current->level == 1)
	{
		$class = "parent";
	}
	elseif (($current->class ?? '') === 'scrollable-menu')
	{
		$class = "dropdown scrollable-menu";
	}
}

// if the menu is help then set class to li
$class = ($current->title == 'MOD_MENU_HELP' && $current->element == 'com_cpanel') ? "j-help-menu" : $class;
$class = $class == '' ? '' : 'class="'.$class.'"';

// Set the correct aria role and print the item
if ($current->type == 'separator')
{
	echo '<li ' .$class. ' role="presentation">';
}
else
{
	echo '<li ' .$class. ' role="menuitem">';
}

// Print a link if it exists
$linkClass  = [];
$dataToggle = '';
$iconClass  = '';
$toggleIcon = '';

if ($current->hasChildren())
{
	$linkClass[] = 'has-arrow';
	$toggleIcon = '<span class="icon-toggler" aria-hidden="true">'
		. '<span class="icon-toggler-svg icon-toggler-svg--down">' . \JHtmlPoweradmin::icon('arrow-down', 12, 'currentColor') . '</span>'
		. '<span class="icon-toggler-svg icon-toggler-svg--up">' . \JHtmlPoweradmin::icon('arrow-up', 10, 'currentColor') . '</span>'
		. '</span>';

	if ($current->level > 2)
	{
		$dataToggle  = ' data-toggle="dropdown"';
	}
}
else
{
	$linkClass[] = 'no-dropdown';
}

// Implode out $linkClass for rendering
$linkClass = ' class="' . implode(' ', $linkClass) . '" ';

// Get the menu link
$link      = $current->link ?? '';

// Get the menu icon
$icon      = $this->getIconClass($current);
$svgMap = [
	'TPL_POWERADMIN_MENU_CONTROL_PANEL' => 'dashboard',
	'MOD_MENU_COM_CONTENT' => 'content',
	'MOD_MENU_MENUS' => 'menus',
	'MOD_MENU_COMPONENTS' => 'layer',
	'MOD_MENU_COM_USERS_USERS' => 'users',
	'MOD_MENU_EXTENSIONS_EXTENSIONS' => 'puzzle',
	'MOD_MENU_SYSTEM' => 'system',
	'MOD_MENU_HELP' => 'help',
	'MOD_MENU_INFORMATION_NOTIFICATIONS' => 'notification',
	'MOD_MESSAGES_PRIVATE_MESSAGES' => 'chat',
];

$svgIcon = ($current->level == 1 && isset($svgMap[$current->title])) ? $svgMap[$current->title] : '';

if ($svgIcon !== '')
{
	$iconClass = '<span style="line-height: 0;">' . \JHtmlPoweradmin::icon($svgIcon, 20, 'currentColor', ['class' => 'sidebar-svg-icon', 'aria-hidden' => 'true']) . '</span>';
}
else
{
	$iconClass = ($icon != '' && $current->level == 1) ? '<span class="' . $icon . '" aria-hidden="true"></span>' : '';
}

$ajax      = $current->ajaxbadge ? '<span class="menu-badge"><span class="icon-spin icon-spinner mt-1 system-counter" data-url="' . $current->ajaxbadge . '"></span></span>' : '';

$anchorLink = $this->enabled ? ' href="' . $link . '"' : '';

if ($link != '' && $current->target != '')
{
	echo "<a" . $linkClass . $dataToggle . $anchorLink . " target=\"" . $current->target . "\">"
		. $iconClass
		. '<span class="sidebar-item-title">' . Text::_($current->title) . '</span>' . $ajax . $toggleIcon . '</a>';
}
elseif ($link != '')
{
	echo "<a" . $linkClass . $dataToggle . $anchorLink . ">"
		. $iconClass
		. '<span class="sidebar-item-title">' . Text::_($current->title) . '</span>' . $ajax . $toggleIcon . '</a>';
}
elseif ($current->title != '' && ($current->class ?? '') !== 'separator')
{
	echo "<a" . $linkClass . $dataToggle . ">"
		. $iconClass
		. '<span class="sidebar-item-title">' . Text::_($current->title) . '</span>' . $ajax . $toggleIcon . '</a>';
}
else
{
	echo '<span>' . Text::_($current->title) . '</span>' . $ajax;
}

if ($current->getParams()->get('menu-quicktask', false))
{
	$params = $current->getParams();
	$user = $this->application->getIdentity();
	$link = $params->get('menu-quicktask-link');
	$icon = $params->get('menu-quicktask-icon', 'plus');
	$title = $params->get('menu-quicktask-title', 'MOD_MENU_QUICKTASK_NEW');
	$permission = $params->get('menu-quicktask-permission');
	$scope = $current->scope !== 'default' ? $current->scope : null;

	if (!$permission || $user->authorise($permission, $scope))
	{
		echo '<span class="menu-quicktask"><a href="' . $link . '">';
		echo '<span class="icon-' . $icon . '" title="' . htmlentities(Text::_($title)) . '" aria-hidden="true"></span>';
		echo '<span class="sr-only">' . Text::_($title) . '</span>';
		echo '</a></span>';
	}
}

// Recurse through children if they exist
if ($this->enabled && $current->hasChildren())
{
	if ($current->level > 1)
	{
		$id = isset($current->id) && $current->id ? ' id="menu-' . strtolower($current->id) . '"' : '';

		echo '<ul' . $id . ' class="collapse collapse-level-' . $current->level . '">' . "\n";
	}
	else
	{
		echo '<ul id="collapse' . $this->getCounter() . '" class="collapse-level-1 collapse" role="menu" aria-haspopup="true">' . "\n";
	}

	// WARNING: Do not use direct 'include' or 'require' as it is important to isolate the scope for each call
	$this->renderSubmenu(__FILE__, $current);

	echo "</ul>\n";
}

echo "</li>\n";
