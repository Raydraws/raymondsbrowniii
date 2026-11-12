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


use Joomla\CMS\Factory;

Factory::getDocument()->getWebAssetManager()
	->useScript('core')
	->useScript('webcomponent.toolbar-button');

/**
 * @var  int     $id
 * @var  string  $name
 * @var  string  $doTask
 * @var  string  $class
 * @var  string  $text
 * @var  string  $btnClass
 * @var  string  $tagName
 * @var  bool    $listCheck
 * @var  string  $htmlAttributes
 */
extract((array) $displayData, EXTR_OVERWRITE);

$tagName = $tagName ?? 'button';
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

$idAttr   = !empty($id)        ? ' id="' . $id . '"' : '';
$listAttr = !empty($listCheck) ? ' list-selection' : '';

?>
<joomla-toolbar-button <?php echo $idAttr.$listAttr; ?>>
<<?php echo $tagName; ?>
	value="<?php echo $doTask; ?>"
	class="<?php echo $btnClass; ?>"
	<?php echo $htmlAttributes; ?>
	onclick="document.getElementById('<?php echo $selector; ?>').open();"
>
	<?php if ($svgIconKey) : ?>
		<span class="icon-md" aria-hidden="true"><?php echo \JHtmlPoweradmin::icon($svgIconKey, 16, 'currentColor'); ?></span>
	<?php else : ?>
		<span class="<?php echo $class; ?> icon-md" aria-hidden="true"></span>
	<?php endif; ?>
	<?php echo $text; ?>
</<?php echo $tagName; ?>>
</joomla-toolbar-button>
