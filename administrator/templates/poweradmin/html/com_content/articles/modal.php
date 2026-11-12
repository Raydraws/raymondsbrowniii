<?php
/**
 * @package     Poweradmin
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Component\Content\Site\Helper\RouteHelper as ContentRouteHelper;

$app = Factory::getApplication();

if ($app->isClient('site'))
{
	Session::checkToken('get') or die(Text::_('JINVALID_TOKEN'));
}

HTMLHelper::_('behavior.core');
HTMLHelper::_('script', 'com_content/admin-articles-modal.min.js', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('behavior.multiselect');

$function  = $app->input->getCmd('function', 'jSelectArticle');
$editor    = $app->input->getCmd('editor', '');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$onclick   = $this->escape($function);

if (!empty($editor))
{
	// This view is used also in com_menus. Load the xtd script only if the editor is set!
	$this->document->addScriptOptions('xtd-articles', array('editor' => $editor));
	$onclick = "jSelectArticle";
}
?>
<div class="container-popup">

	<form action="<?php echo Route::_('index.php?option=com_content&view=articles&layout=modal&tmpl=component&function=' . $function . '&' . Session::getFormToken() . '=1&editor=' . $editor); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">

		<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

		<?php if (empty($this->items)) : ?>
			<div class="j-alert j-alert-info d-flex mt-4">
				<div class="j-alert-icon-wrap"><span  aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('help-2', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span></div>
				<div class="j-alert-info-wrap"><?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div>
			</div>
		<?php else : ?>
			<table class="table j-striped-table my-4">
				<caption id="captionTable" class="sr-only">
					<?php echo Text::_('COM_CONTENT_ARTICLES_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
				</caption>
				<thead>
					<tr>
						<th scope="col" style="width:1%" class="text-center">
							<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
						</th>
						<th scope="col" class="title">
							<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th scope="col" style="width:10%" class="d-none d-md-table-cell">
							<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
						</th>
						<th scope="col" style="width:15%">
							<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
						</th>
						<th scope="col" style="width:5%" class="d-none d-md-table-cell">
							<?php echo HTMLHelper::_('searchtools.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
						</th>
						<th scope="col" style="width:1%" class="d-none d-md-table-cell">
							<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$iconStates = array(
					-2 => 'trash',
					0  => 'unpublish',
					1  => 'publish',
				);
				?>
				<?php foreach ($this->items as $i => $item) : ?>
					<?php if ($item->language && Multilanguage::isEnabled())
					{
						$tag = strlen($item->language);
						if ($tag == 5)
						{
							$lang = substr($item->language, 0, 2);
						}
						elseif ($tag == 6)
						{
							$lang = substr($item->language, 0, 3);
						}
						else {
							$lang = '';
						}
					}
					elseif (!Multilanguage::isEnabled())
					{
						$lang = '';
					}
					?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="text-center tbody-icon">
							<?php if ($iconStates[$this->escape($item->state)] === 'trash') : ?>
									<?php echo \JHtmlPoweradmin::icon('trash', 14, 'currentColor', ['aria-hidden' => 'true']); ?>
								<?php else : ?>
									<?php if ($iconStates[$this->escape($item->state)] === 'trash') : ?>
									<?php echo \JHtmlPoweradmin::icon('trash', 14, 'currentColor', ['aria-hidden' => 'true']); ?>
								<?php else : ?>
									<?php if ($iconStates[$this->escape($item->state)] === 'publish' || $iconStates[$this->escape($item->state)] === 'unpublish' || $iconStates[$this->escape($item->state)] === 'trash') : ?>
									<?php echo \JHtmlPoweradmin::icon($iconStates[$this->escape($item->state)], 14, 'currentColor', ['aria-hidden' => 'true']); ?>
								<?php else : ?>
									<span class="<?php echo $iconStates[$this->escape($item->state)]; ?>" aria-hidden="true"></span>
								<?php endif; ?>
								<?php endif; ?>
								<?php endif; ?>
						</td>
					<th scope="row">
						<a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', '<?php echo $item->catid; ?>', null, '<?php echo $this->escape(ContentRouteHelper::getArticleRoute($item->id, $item->catid, $item->language)); ?>', '<?php echo $this->escape($lang); ?>');">
							<?php echo $this->escape($item->title); ?>
						</a>
							<span class="small break-word">
								<?php if (empty($item->note)) : ?>
									<?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
								<?php else : ?>
									<?php echo Text::sprintf('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note)); ?>
								<?php endif; ?>
							</span>
							<div class="small">
								<?php echo Text::_('JCATEGORY') . ': ' . $this->escape($item->category_title); ?>
							</div>
						</th>
						<td class="small d-none d-md-table-cell">
							<?php echo $this->escape($item->access_level); ?>
						</td>
						<td class="small">
							<?php echo LayoutHelper::render('joomla.content.language', $item); ?>
						</td>
						<td class="small d-none d-md-table-cell">
							<?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')); ?>
						</td>
						<td class="small d-none d-md-table-cell">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<?php // load the pagination. ?>
			<?php echo $this->pagination->getListFooter(); ?>

		<?php endif; ?>

		<input type="hidden" name="task" value="">
		<input type="hidden" name="boxchecked" value="0">
		<input type="hidden" name="forcedLanguage" value="<?php echo $app->input->get('forcedLanguage', '', 'CMD'); ?>">
		<?php echo HTMLHelper::_('form.token'); ?>

	</form>
</div>
