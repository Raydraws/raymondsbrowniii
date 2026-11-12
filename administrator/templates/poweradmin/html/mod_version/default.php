<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_version
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

?>
<div class="joomlaversion d-flex">
	<div class="d-flex align-items-center mr-2">
		<span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('joomla', 14, 'currentColor'); ?></span>
	</div>
	<div class="mx-auto">
		<span class="sr-only"><?php echo Text::sprintf('MOD_VERSION_CURRENT_VERSION_TEXT', $version); ?></span>
		<span aria-hidden="true"><?php echo $version; ?></span>
	</div>
</div>
