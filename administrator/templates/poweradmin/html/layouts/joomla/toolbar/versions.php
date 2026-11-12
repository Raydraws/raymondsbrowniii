<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

HTMLHelper::_('behavior.core');
HTMLHelper::_('script', 'system/joomla-toolbar-button.min.js', ['version' => 'auto', 'relative' => true]);

/**
 * @var  string  $id
 * @var  string  $itemId
 * @var  string  $typeId
 * @var  string  $typeAlias
 * @var  string  $title
 */
extract($displayData, EXTR_OVERWRITE);

// Load the webcomponent helper from template override
$webcomponentPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/webcomponent.php';

if (file_exists($webcomponentPath)) {
	require_once $webcomponentPath;
}

echo JHtmlWebcomponent::renderModal(
	'versionsModal',
	array(
		'url'    => 'index.php?' . http_build_query(
			[
				'option' => 'com_contenthistory',
				'view' => 'history',
				'layout' => 'modal',
				'tmpl' => 'component',
				'item_id' => $itemId,
				Session::getFormToken() => 1
			]
		),
		'title'  => $title,
		'height' => '75vh',
		'width'  => '85vw',
		'modalWidth'  => '85',
		'bodyHeight'  => '75',
		'footer' => '<button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">'
			. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>'
	)
);
?>
<joomla-toolbar-button id="toolbar-versions" class="d-none d-lg-block">
	<button
		class="btn btn-secondary"
		type="button"
		onclick="document.getElementById('versionsModal').open()"
		data-toggle="modal">
		<span class="icon-code-branch icon-md" aria-hidden="true"></span>
		<?php echo $title; ?>
	</button>
</joomla-toolbar-button>