<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_popular
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Module\Popular\Administrator\Helper\PopularHelper;

$model = $app->bootComponent('com_content')->getMVCFactory()->createModel('Articles', 'Administrator', ['ignore_request' => true]);
$list = PopularHelper::getList($params, $model);

// Get module data.
if ($params->get('automatic_title', 0))
{
	$module->title = PopularHelper::getTitle($params);
}

// Render the module
require ModuleHelper::getLayoutPath('mod_popular', $params->get('layout', 'default'));
