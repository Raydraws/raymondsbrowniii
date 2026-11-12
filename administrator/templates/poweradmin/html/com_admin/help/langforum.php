<?php
/**
 * @package     Poweradmin
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

Factory::getLanguage()->load('mod_menu', JPATH_ADMINISTRATOR, null, false, true);

$forumId   = (int) Text::_('MOD_MENU_HELP_SUPPORT_OFFICIAL_LANGUAGE_FORUM_VALUE');

if (empty($forumId))
{
	$forumId = 511;
}

$forumUrl = 'https://forum.joomla.org/viewforum.php?f=' . $forumId;

Factory::getApplication()->redirect($forumUrl);
