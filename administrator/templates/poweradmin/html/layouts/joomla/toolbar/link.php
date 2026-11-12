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


/**
 * @var  int     $id
 * @var  string  $name
 * @var  string  $class
 * @var  string  $text
 * @var  string  $btnClass
 * @var  string  $tagName
 * @var  string  $htmlAttributes
 */
extract((array) $displayData, EXTR_OVERWRITE);


$id = isset($id) ? 'id="' . $id . '"' : '';
$svgIconMap = [
	'select'      => 'hand',
	'info-circle' => 'help-2',
	'options'     => 'settings',
	'publish'     => 'publish',
	'unpublish'   => 'unpublish',
	'featured'    => 'featured',
	'unfeatured'  => 'unfeatured',
	'batch'       => 'batch',
	'square'      => 'batch',
	'feature'          => 'featured',
	'unfeature'        => 'unfeatured',
];
$iconLookup = preg_replace('/^icon-/', '', (string) ($class ?? ''));
$svgIconKey = $svgIconMap[$iconLookup] ?? null;

$margin = (strpos($url ?? '', 'index.php?option=com_config') === false) ? '' : '';
$target = empty($target) ? '' : 'target="' . $target . '"';
?>
<joomla-toolbar-button class="<?php echo $margin; ?>" <?php echo $id; ?>>
	<a
		class="<?php echo $btnClass; ?>"
		href="<?php echo $url; ?>"
		<?php echo $target; ?>
		<?php echo $htmlAttributes; ?>>
		<?php if ($svgIconKey) : ?>
			<span class="icon-md" aria-hidden="true"><?php echo \JHtmlPoweradmin::icon($svgIconKey, 16, 'currentColor'); ?></span>
		<?php else : ?>
			<span class="<?php echo $class; ?> icon-md" aria-hidden="true"></span>
		<?php endif; ?>
		<?php echo $text ?: ''; ?>
	</a>
</joomla-toolbar-button>
