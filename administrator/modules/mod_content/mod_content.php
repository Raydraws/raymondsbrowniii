<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_content
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Module\Content\Administrator\Helper\ModContentHelper;

/** @var CMSApplication $app */
$app = Factory::getApplication();
$currentTemplate = $app->getTemplate();

if ($currentTemplate !== 'poweradmin') {
    return;
}

$model = $app->bootComponent('com_content')->getMVCFactory()->createModel('Articles', 'Administrator', ['ignore_request' => true]);
$latests = ModContentHelper::getList($params, $model);
$popular = ModContentHelper::getPopular($params, $model);

require \Joomla\CMS\Helper\ModuleHelper::getLayoutPath('mod_content', $params->get('layout', 'default'));
