<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('JPATH_BASE') or die;

$form  = $displayData->getForm();

$title = $form->getField('title') ? 'title' : ($form->getField('name') ? 'name' : '');

?>
<div class="row title-alias form-no-margin mb-3">
	<div class="col-lg-7">
		<?php echo $title ? $form->renderField($title) : ''; ?>
	</div>
	<div class="col-lg-5">
		<?php echo $form->renderField('alias'); ?>
	</div>
</div>