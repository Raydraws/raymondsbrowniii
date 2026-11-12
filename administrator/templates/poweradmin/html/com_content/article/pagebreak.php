<?php
/**
 * @package     Poweradmin
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.core');
HTMLHelper::_('script', 'com_content/admin-article-pagebreak.min.js', array('version' => 'auto', 'relative' => true));

$this->eName = Factory::getApplication()->input->getCmd('e_name', '');
$this->eName = preg_replace('#[^A-Z0-9\-\_\[\]]#i', '', $this->eName);
$this->document->setTitle(Text::_('COM_CONTENT_PAGEBREAK_DOC_TITLE'));

?>
<div class="container-popup">
	<form>
		<div class="control-group">
			<div class="control-label">
				<label for="title"><?php echo Text::_('COM_CONTENT_PAGEBREAK_TITLE'); ?></label>
			</div>
			<div class="controls">
				<input type="text" id="title" name="title">
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<label for="alias"><?php echo Text::_('COM_CONTENT_PAGEBREAK_TOC'); ?></label>
			</div>
			<div class="controls">
				<input type="text" id="alt" name="alt">
			</div>
		</div>

		<button onclick="insertPagebreak('<?php echo $this->eName; ?>');" class="btn btn-success">
			<?php echo Text::_('COM_CONTENT_PAGEBREAK_INSERT_BUTTON'); ?>
		</button>

	</form>
</div>
