<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_cache
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ModuleHelper;

$app = Factory::getApplication();
$currentTemplate = $app->getTemplate();
if ($currentTemplate !== 'poweradmin') {
    return;
}

//include js
HTMLHelper::_('script', 'mod_cache/cache.js', ['version' => 'auto', 'relative' => true]);
// include helper file - use absolute path to ensure it's found
$helperPath = JPATH_ADMINISTRATOR . '/modules/mod_cache/helper.php';

if (is_file($helperPath)) {
	try {
		require_once $helperPath;
	} catch (\Throwable $e) {
		// If helper file fails to load, silently continue - will use fallback below
	}
}

// Ensure class exists before using it
if (!class_exists('ModCacheHelper')) {
	// Fallback: return early with empty cache info
	$cacheInfo = ['size' => 0, 'unit' => 'MB', 'raw' => 0];
	require ModuleHelper::getLayoutPath('mod_cache', $params->get('layout', 'default'));
	return;
}

//get cache size unit
$cacheUnit = $params->get('size_unit', 'MB');
// load cache sizes info
$cacheInfo   = ModCacheHelper::getCacheSize($cacheUnit);
$cacheInfo['unit'] = $cacheInfo['unit'] ? $cacheInfo['unit']: $cacheUnit;

require ModuleHelper::getLayoutPath('mod_cache', $params->get('layout', 'default'));