<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('JPATH_BASE') or die;

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

use Joomla\CMS\Language\Text;

$data = $displayData;
?>
<div class="j-alert j-alert-info d-flex mt-4">
	<div class="j-alert-icon-wrap"><span  aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('help-2', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span></div>
	<div class="j-alert-info-wrap"><?php echo $data['options']['noResultsText']; ?></div>
</div>