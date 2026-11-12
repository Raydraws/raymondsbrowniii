<?php
/**
 * @package     Poweradmin
 * @subpackage  Templates.Poweradmin
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 * @since       4.0
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/** @var JDocumentError $this */

// Authenticated versus guest have different displays
$user = Factory::getUser();

if ($user->guest)
{
	require __DIR__ . '/error_login.php';
}
else
{
	require __DIR__ . '/error_full.php';
}
