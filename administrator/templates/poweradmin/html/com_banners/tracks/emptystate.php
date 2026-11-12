<?php
/**
 * @package     Poweradmin
 * @subpackage  com_banners
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

/** @var \Joomla\Component\Banners\Administrator\View\Tracks\HtmlView $this */

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));

$webcomponentPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/webcomponent.php';
if (file_exists($webcomponentPath)) {
	require_once $webcomponentPath;
}
?>
<form action="<?php echo Route::_('index.php?option=com_banners&view=tracks'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
				<?php if (empty($this->items)) : ?>
					<div class="j-alert j-alert-info d-flex mt-4">
						<div class="j-alert-icon-wrap"><span  aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('help-2', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span></div>
						<div class="j-alert-info-wrap"><?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div>
					</div>
				<?php else : ?>
					<table class="table j-list-table">
						<caption id="captionTable" class="sr-only">
							<?php echo Text::_('COM_BANNERS_TRACKS_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
						</caption>
						<thead>
							<tr>
								<th scope="col" class="title">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_BANNERS_HEADING_NAME', 'b.name', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" style="width:20%">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_BANNERS_HEADING_CLIENT', 'cl.name', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" style="width:10%" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_BANNERS_HEADING_TYPE', 'a.track_type', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" style="width:10%" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_BANNERS_HEADING_COUNT', 'a.count', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" style="width:10%" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'JDATE', 'a.track_date', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->items as $i => $item) : ?>
								<tr class="row<?php echo $i % 2; ?>">
									<th scope="row">
										<?php echo $item->banner_name; ?>
										<div class="small">
											<?php echo Text::_('JCATEGORY') . ': ' . $this->escape($item->category_title); ?>
										</div>
									</th>
									<td>
										<?php echo $item->client_name; ?>
									</td>
									<td class="small d-none d-md-table-cell">
										<?php echo $item->track_type == 1 ? Text::_('COM_BANNERS_IMPRESSION') : Text::_('COM_BANNERS_CLICK'); ?>
									</td>
									<td class="d-none d-md-table-cell">
										<?php echo $item->count; ?>
									</td>
									<td class="d-none d-md-table-cell">
										<?php echo HTMLHelper::_('date', $item->track_date, Text::_('DATE_FORMAT_LC5')); ?>
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
				

				<?php // Load the export form ?>
				<?php echo JHtmlWebcomponent::renderModal(
					'downloadModal',
					[
						'title'       => Text::_('COM_BANNERS_TRACKS_DOWNLOAD'),
						'url'         => Route::_('index.php?option=com_banners&amp;view=download&amp;tmpl=component'),
						'height'      => '75vh',
						'width'       => '85vw',
						'modalWidth'  => '40',
						'footer'      => '<button type="button" class="btn" data-dismiss="modal"'
								. ' onclick="Joomla.iframeButtonClick({iframeSelector: \'#downloadModal\', buttonSelector: \'#closeBtn\'})">'
								. Text::_('COM_BANNERS_CANCEL') . '</button>'
								. '<button type="button" class="btn btn-success"'
								. ' onclick="Joomla.iframeButtonClick({iframeSelector: \'#downloadModal\', buttonSelector: \'#exportBtn\'})">'
								. Text::_('COM_BANNERS_TRACKS_EXPORT') . '</button>',
					]
				); ?>
				<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>
