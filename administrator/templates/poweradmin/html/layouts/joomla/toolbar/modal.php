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

HTMLHelper::_('script', 'system/joomla-toolbar-button.min.js', ['version' => 'auto', 'relative' => true]);

$webcomponentPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/webcomponent.php';
if (file_exists($webcomponentPath)) {
	require_once $webcomponentPath;
}

/**
 * Generic toolbar button layout to open a modal
 * -----------------------------------------------
 * @param   array   $displayData    Button parameters. Default supported parameters:
 *                                  - selector  string  Unique DOM identifier for the modal. CSS id without #
 *                                  - class     string  Button class
 *                                  - icon      string  Button icon
 *                                  - text      string  Button text
 */

$selector = $displayData['selector'];
$id       = isset($displayData['id']) ? $displayData['id'] : '';
$class    = isset($displayData['class']) ? $displayData['class'] : 'btn btn-secondary';
$icon     = isset($displayData['icon']) ? $displayData['icon'] : 'download';
$text     = isset($displayData['text']) ? $displayData['text'] : '';
$svgIconMap = [
	'select'      => 'hand',
	'info-circle' => 'help-2',
	'options'     => 'settings',
	'publish'     => 'publish',
	'unpublish'   => 'unpublish',
	'download'    => 'download',
	'featured'    => 'featured',
	'unfeatured'  => 'unfeatured',
	'batch'       => 'batch',
	'square'      => 'batch',
	'feature'          => 'featured',
	'unfeature'        => 'unfeatured',
];
$svgIconKey = $svgIconMap[preg_replace('/^icon-/', '', (string) $icon)] ?? null;
?>

<!-- Render the button -->
<joomla-toolbar-button <?php echo $id; ?> onclick="document.getElementById('modal_<?php echo $selector; ?>').open()" class="<?php echo $class; ?>" data-toggle="modal">
	<?php if ($svgIconKey) : ?>
		<span class="icon-md" aria-hidden="true"><?php echo \JHtmlPoweradmin::icon($svgIconKey, 16, 'currentColor'); ?></span>
	<?php else : ?>
		<span class="<?php echo $icon; ?> icon-md" aria-hidden="true"></span>
	<?php endif; ?>
	<?php echo $text; ?>
</joomla-toolbar-button>

<!-- Render the modal -->
<?php
echo JHtmlWebcomponent::renderModal(
	'modal_' . $selector,
	[
		'url'         => $displayData['doTask'],
		'title'       => $text,
		'height'      => '75vh',
		'width'       => '85vw',
		'modalWidth'  => 80,
		'bodyHeight'  => 60,
		'closeButton' => true,
		'footer'      => '<a class="btn btn-secondary" data-dismiss="modal" type="button"'
						. ' onclick="window.parent.Joomla.Modal.getCurrent().close();">'
						. Text::_('COM_BANNERS_CANCEL') . '</a>'
						. '<button class="btn btn-success" type="button"'
						. ' onclick="Joomla.iframeButtonClick({iframeSelector: \'#modal_downloadModal\', buttonSelector: \'#exportBtn\'})">'
						. Text::_('COM_BANNERS_TRACKS_EXPORT') . '</button>',
	]
);
?>
