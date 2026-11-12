<?php
/**
 * @package     Poweradmin
 * @subpackage  com_templates
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<div id="template-manager-copy" class="container-fluid">
	<div class="mt-2">
		<div class="col-md-12">
			<div class="control-group">
				<div class="control-label">
					<label for="new_name">
						<?php echo Text::_('COM_TEMPLATES_TEMPLATE_NEW_NAME_LABEL'); ?>
					</label>
					<small class="text-muted">
						<?php echo Text::_('COM_TEMPLATES_TEMPLATE_NEW_NAME_DESC'); ?>
					</small>
				</div>
				<div class="controls">
					<input class="form-control" type="text" id="new_name" name="new_name">
				</div>
			</div>
		</div>
	</div>
</div>
