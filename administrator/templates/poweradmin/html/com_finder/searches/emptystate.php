<?php
/**
 * @package     Poweradmin
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

// Include the component HTML helpers.
HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

HTMLHelper::_('behavior.multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo Route::_('index.php?option=com_finder&view=searches'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<?php if (!empty($this->sidebar)) : ?>
			<div id="j-sidebar-container" class="col-md-2">
				<?php echo $this->sidebar; ?>
			</div>
		<?php endif; ?>
		<div class='<?php echo !empty($this->sidebar) ? 'col-md-10' : 'col-md-12'; ?>'>
			<div id="j-main-container" class="j-main-container">
				<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filterButton' => false))); ?>
				<?php if (empty($this->items)) : ?>
					<div class="j-alert j-alert-info d-flex mt-4">
						<div class="j-alert-icon-wrap"><span  aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('help-2', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span></div>
						<div class="j-alert-info-wrap"><?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div>
					</div>
				<?php else : ?>
				<table class="table j-list-table">
					<caption id="captionTable" class="sr-only">
						<?php echo Text::_('COM_FINDER_SEARCHES_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
					</caption>
					<thead>
						<tr>
							<th scope="col">
								<?php echo HTMLHelper::_('searchtools.sort', 'COM_FINDER_HEADING_PHRASE', 'a.searchterm', $listDirn, $listOrder); ?>
							</th>
							<th scope="col" style="width:15%">
								<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
							</th>
							<th scope="col" style="width:1%" class="text-center">
								<?php echo Text::_('COM_FINDER_HEADING_RESULTS'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($this->items as $i => $item) : ?>
						<tr class="row<?php echo $i % 2; ?>">
							<th scope="row" class="break-word">
								<?php echo $this->escape($item->searchterm); ?>
							</th>
							<td>
								<?php echo (int) $item->hits; ?>
							</td>
							<td class="text-center btns">
								<?php echo (int) $item->results; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				
				<!-- load the pagination. -->
				<div class="j-pagination-footer">
					<?php echo LayoutHelper::render('joomla.searchtools.default.listlimit', array('view' => $this)); ?>
					<?php echo $this->pagination->getListFooter(); ?>
				</div>
				<?php endif; ?>


				<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>
