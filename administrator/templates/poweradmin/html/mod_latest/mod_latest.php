<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_latest
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\Module\Latest\Administrator\Helper\ModLatestHelper;

$model = $app->bootComponent('com_content')->getMVCFactory()->createModel('Articles', 'Administrator', ['ignore_request' => true]);
$list = ModLatestHelper::getList($params, $model);

if ($params->get('automatic_title', 0))
{
	$module->title = ModLatestHelper::getTitle($params);
}

require \Joomla\CMS\Helper\ModuleHelper::getLayoutPath('mod_latest', $params->get('layout', 'default'));
