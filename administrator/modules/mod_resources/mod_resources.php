<?php

/**
 * @package     Poweradmin
 * @subpackage  mod_resources
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

/** @var CMSApplication $app */
$app = Factory::getApplication();
$currentTemplate = $app->getTemplate();

if ($currentTemplate !== 'poweradmin') {
    return;
}

// Render the module
require ModuleHelper::getLayoutPath('mod_resources', $params->get('layout', 'default'));
