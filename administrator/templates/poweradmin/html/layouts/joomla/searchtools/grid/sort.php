<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;

$data = $displayData;
$icon = "caret-up-down";
$sort = '';
$caption = '';
$selected = '';
$id = '';

if ($data->order === $data->selected) :
	$icon = $data->direction === 'asc' ? 'caret-up' : 'caret-down';
	$sort = $data->direction === 'asc' ? 'ascending' : 'descending';
	$caption = !empty($data->title) ? Text::_($data->title) . ' - ' . $sort : Text::_('JGRID_HEADING_ID');
	$selected = ' selected';
	$id = 'id="sorted"';
endif;
?>

<a href="" onclick="return false;" class="js-stools-column-order<?php echo $selected; ?> js-stools-button-sort"
	<?php echo $id; ?>
	data-order="<?php echo $data->order; ?>"
	data-direction="<?php echo strtoupper($data->direction); ?>"
	data-caption="<?php echo $caption; ?>"
	<?php if (!empty($sort)) : ?>
		data-sort="<?php echo $sort; ?>"
	<?php endif; ?>>
	<?php if (!empty($data->title)) : ?>
		<span>
			<?php echo Text::_($data->title); ?>
		</span>
	<?php endif; ?>
	<span style="display: inline-block; transform: translate(1px, -1px); pointer-events: none;"><?php echo \JHtmlPoweradmin::icon($icon, $icon === "caret-up-down" ? 12 : 10, 'currentColor', ['aria-hidden' => 'true', 'style' => 'pointer-events: none;']); ?></span>
	<span class="sr-only">
		<?php echo Text::_('TPL_POWERADMIN_SEARCHTOOLS_SORT_BY'); ?>
		<?php echo (!empty($data->title)) ? Text::_($data->title) : Text::_('JGRID_HEADING_ORDERING'); ?>
	</span>
</a>
