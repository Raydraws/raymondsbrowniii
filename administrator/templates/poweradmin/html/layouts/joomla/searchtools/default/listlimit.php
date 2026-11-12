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

// Load the form list fields
$list = $data['view']->filterForm->getGroup('list');

?>
<?php if(!empty($list) && isset($list['list_limit'])) : ?>
	<div class="ordering-select d-none d-sm-block">
		<div class="j-list-limit">
			<div class="j-list-limit-label" id="list_limit_lbl">
				<?php echo Text::_('TPL_POWERADMIN_SEARCHTOOLS_ITEMS_PER_PAGE'); ?>
			</div>
			<div class="j-list-limit-input" aria-labelledby="list_limit_lbl">
				<?php echo $list['list_limit']->input; ?>
			</div>
		</div>
	</div>
<?php endif; ?>