<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_accessibility
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ModuleHelper;

/** @var CMSApplication $app */
$app = Factory::getApplication();
$currentTemplate = $app->getTemplate();

if ($currentTemplate !== 'poweradmin') {
    return;
}

$db = Factory::getDbo();
$query = $db->getQuery(true)
    ->select($db->quoteName('enabled'))
    ->from($db->quoteName('#__extensions'))
    ->where($db->quoteName('element') . ' = ' . $db->quote('accessibility'))
    ->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
    ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
    ->where($db->quoteName('name') . ' = ' . $db->quote('plg_system_accessibility'))
    ->where($db->quoteName('enabled') . ' = 1');
$db->setQuery($query);

$extension = $db->loadObject();
if (empty($extension) || !$extension->enabled) {
    return;
}

// Include js
HTMLHelper::_('script', 'mod_accessibility/accessibility.js', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('stylesheet', 'mod_accessibility/style.css', ['relative' => true]);

require ModuleHelper::getLayoutPath('mod_accessibility', $params->get('layout', 'default'));
