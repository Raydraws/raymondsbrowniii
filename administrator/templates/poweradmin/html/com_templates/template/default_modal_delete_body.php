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
<div id="template-manager-delete" class="container-fluid">
	<div class="mt-2">
		<div class="col-md-12">
			<p><?php echo Text::sprintf('COM_TEMPLATES_MODAL_FILE_DELETE', $this->fileName); ?></p>
		</div>
	</div>
</div>