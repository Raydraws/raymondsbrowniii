<?php

/**
 * @package     Poweradmin
 * @subpackage  mod_extension_updates
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Module\ExtensionUpdates\Administrator\Helper\ExtensionUpdatesHelper;

/** @var CMSApplication $app */
$app = Factory::getApplication();
$currentTemplate = $app->getTemplate();

if ($currentTemplate !== 'poweradmin') {
    return;
}

$extensionContents = ExtensionUpdatesHelper::extractExtensionsContent();

// Render the module
require ModuleHelper::getLayoutPath('mod_extension_updates', $params->get('layout', 'default'));
