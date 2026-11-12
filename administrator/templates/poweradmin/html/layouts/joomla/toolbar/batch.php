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


use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// TODO: Deprecate this file since we can use popup button to raise batch modal.

HTMLHelper::_('behavior.core');

$id    = isset($displayData['id']) ? $displayData['id'] : '';
$title = $displayData['title'];
Text::script('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
Text::script('ERROR');
$message = "{'error': [Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST')]}";
$alert = "Joomla.renderMessages(" . $message . ")";
?>
<button<?php echo $id; ?> type="button" data-toggle="modal" onclick="if (document.adminForm.boxchecked.value==0){<?php echo $alert; ?>}else{document.getElementById('collapseModal').open(); return true;}" class="btn btn-primary">
	<span class="icon-md" aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('batch', 16, 'currentColor'); ?></span>
	<?php echo $title; ?>
</button>
