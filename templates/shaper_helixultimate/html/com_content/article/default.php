<?php
/**
 * @package Helix Ultimate Framework
 * @author JoomShaper https://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2025 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

// From the Walk styles + script, version-stamped so browsers never use a stale cached copy
$rsbWa = \Joomla\CMS\Factory::getApplication()->getDocument()->getWebAssetManager();
if (!$rsbWa->assetExists('style', 'rsb.walk')) {
    $rsbWa->registerAndUseStyle('rsb.walk', 'templates/shaper_helixultimate/css/walk.css', ['version' => '20261123a']);
    $rsbWa->registerAndUseScript('rsb.walk', 'templates/shaper_helixultimate/js/walk.js', ['version' => '20261123a'], ['defer' => true]);
}

require HelixUltimate\Framework\Platform\HTMLOverride::loadTemplate();
