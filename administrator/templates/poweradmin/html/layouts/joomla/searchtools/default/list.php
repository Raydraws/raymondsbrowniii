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

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

$data = $displayData;

// Load the form list fields
$list = $data['view']->filterForm->getGroup('list');
$state = $data['view']->get('State');

$orderDirn = 'asc';
if (isset($list['list_fullordering'])) {
	$orderDirn = explode(' ', $state->get('list.fullordering', $list['list_fullordering']->getAttribute('default')))[1];
}

?>
<?php if ($list) : ?>
	<div class="ordering-select d-none d-xl-block">
		<?php foreach ($list as $fieldName => $field) : ?>
			<?php if($fieldName !== 'list_limit') : ?>
				<div class="js-stools-field-list">
					<div class="js-stools-field-list-content j-card">
						<?php if($fieldName === 'list_fullordering') : ?>
							<span class="js-stools-sort-by-label"><?php echo Text::_('TPL_POWERADMIN_SEARCHTOOLS_ORDER_BY'); ?></span>
						<?php endif; ?>
						<div class="js-stools-list-inner">
							<span class="sr-only"><?php echo $field->label; ?></span>
							<?php echo $field->input; ?>
						</div>
					</div>
					<?php if($fieldName === 'list_fullordering') : ?>
						<button type="button" 
							class="btn btn-link js-stools-order-toggler hasTooltip" 
							title="<?php echo !empty($orderDirn) && strtoupper($orderDirn) === 'ASC' ? Text::_('TPL_POWERADMIN_SEARCHTOOLS_ORDER_DESCENDING') : Text::_('TPL_POWERADMIN_SEARCHTOOLS_ORDER_ASCENDING'); ?>"
							aria-label="<?php echo !empty($orderDirn) && strtoupper($orderDirn) === 'ASC' ? Text::_('TPL_POWERADMIN_SEARCHTOOLS_ORDER_DESCENDING') : Text::_('TPL_POWERADMIN_SEARCHTOOLS_ORDER_ASCENDING'); ?>"
						>
							<span class="icon-lg" aria-hidden="true"><?php echo !empty($orderDirn) && strtoupper($orderDirn) === 'ASC' ? \JHtmlPoweradmin::icon('filter-up', 20, 'currentColor') : \JHtmlPoweradmin::icon('filter-down', 20, 'currentColor'); ?></span>
						</button>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
