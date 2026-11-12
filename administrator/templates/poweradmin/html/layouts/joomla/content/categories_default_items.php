<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('JPATH_BASE') or die;

$class     = ' class="first"';

$item      = $displayData->item;
$items     = $displayData->get('items');
$params    = $displayData->params;
$extension = $displayData->get('extension');
$className = substr($extension, 4);

// This will work for the core components but not necessarily for other components
// that may have different pluralisation rules.
if (substr($className, -1) === 's')
{
	$className = rtrim($className, 's');
}
