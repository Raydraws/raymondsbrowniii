<?php

/**
 * @package     Poweradmin
 * @subpackage  mod_draft_article
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

HTMLHelper::_('script', 'mod_quick_article/quickarticle.js', ['version' => 'auto', 'relative' => true]);

require_once __DIR__ . '/helper.php';

$categoryField = ModQuickArticleHelper::getCategories();

// Render the module
require ModuleHelper::getLayoutPath('mod_quick_article', $params->get('layout', 'default'));
