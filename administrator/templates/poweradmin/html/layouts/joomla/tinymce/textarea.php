<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

use Joomla\CMS\Factory;

defined('JPATH_BASE') or die;

$data = $displayData;

$wa       = Factory::getDocument()->getWebAssetManager();
$template = Factory::getApplication()->getTemplate(true)->template;
$basePath = 'administrator/templates/' . $template . '/js';

if (!$wa->assetExists('script', 'tinymce')) {
	$wa->registerScript('tinymce', $basePath . '/vendor/tinymce/tinymce.js', ['relative' => true], ['defer' => true]);
}

if (!$wa->assetExists('script', 'plg_editors_tinymce')) {
	$wa->registerScript('plg_editors_tinymce', $basePath . '/plg_editors_tinymce/js/tinymce.js', ['relative' => true], ['defer' => true], ['core', 'tinymce']);
}

$wa->useScript('tinymce')->useScript('plg_editors_tinymce');

?>
<textarea
	name="<?php echo $data->name; ?>"
	id="<?php echo $data->id; ?>"
	cols="<?php echo $data->cols; ?>"
	rows="<?php echo $data->rows; ?>"
	style="width: <?php echo $data->width; ?>; height: <?php echo $data->height; ?>;"
	class="<?php echo empty($data->class) ? 'mce_editable' : $data->class; ?>"
	<?php echo $data->readonly ? ' readonly disabled' : ''; ?>
>
	<?php echo $data->content; ?>
</textarea>