<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_stats_admin
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\Module\StatsAdmin\Administrator\Helper\StatsAdminHelper;

$serverinfo = $params->get('serverinfo');
$siteinfo   = $params->get('siteinfo');
$list       = StatsAdminHelper::getStats($params, $app, Factory::getContainer()->get(DatabaseInterface::class));

require ModuleHelper::getLayoutPath('mod_stats_admin', $params->get('layout', 'default'));
