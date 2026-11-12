<?php
/**
 * @package     Poweradmin
 * @subpackage  com_newsfeeds
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

$this->fieldset = 'jbasic';
?>

<div id="fieldset-display" class="j-card options-grid-form options-grid-form-full mb-4">
	<div class="j-card-header">
		<?php echo Text::_('JGLOBAL_FIELDSET_DISPLAY_OPTIONS'); ?>
	</div>
	<div class="j-card-body">
		<?php echo LayoutHelper::render('joomla.edit.fieldset', $this); ?>
	</div>
</div>
