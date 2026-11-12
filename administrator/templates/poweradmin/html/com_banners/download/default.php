<?php
/**
 * @package     Poweradmin
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

/** @var \Joomla\Component\Banners\Administrator\View\Download\HtmlView $this */

?>
<div class="container-popup">
	<form
		class="form-horizontal form-validate"
		id="download-form"
		name="adminForm"
		action="<?php echo Route::_('index.php?option=com_banners&task=tracks.display&format=raw'); ?>"
		method="post">

		<?php foreach ($this->form->getFieldset() as $field) : ?>
			<?php echo $this->form->renderField($field->fieldname); ?>
		<?php endforeach; ?>

		<button class="sr-only"
			id="exportBtn"
			type="button"
			onclick="this.form.submit();window.top.setTimeout('window.parent.Joomla.Modal.getCurrent().close()', 700);">
		</button>
	</form>
</div>
