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
use Joomla\CMS\HTML\HTMLHelper;

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

HTMLHelper::_('script', 'system/joomla-callout.min.js', array('version'=> 'auto', 'relative' => true));

$form = $displayData->getForm();

// JLayout for standard handling of metadata fields in the administrator content edit screens.
$fieldSets = $form->getFieldsets('metadata');
?>

<?php foreach ($fieldSets as $name => $fieldSet) : ?>
	<?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
		<div class="j-alert j-alert-info mt-0 mb-4">
			<span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('help', 20, 'currentColor'); ?></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
			<?php echo $this->escape(Text::_($fieldSet->description)); ?>
		</div>
	<?php endif; ?>

	<?php
	// Include the real fields in this panel.
	if ($name === 'jmetadata')
	{
		echo $form->renderField('metadesc');
		echo $form->renderField('metakey');
	}
	?>

	<a href="javascript:;" id="jmetadata-callout" class="btn btn-secondary btn-block"><span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('settings', 18, 'currentColor'); ?></span> <?php echo Text::_('TPL_POWERADMIN_FIELDSET_SEO_ADVANCED_OPTIONS'); ?></a>
	<joomla-callout for="#jmetadata-callout" dismiss="true" position="bottom">
		<div class="callout-content">
		<?php
		foreach ($form->getFieldset($name) as $field)
		{
			if ($field->name !== 'jform[metadata][tags][]')
			{
				echo $field->renderField();
			}
		} ?>
		</div>
	</joomla-callout>
<?php endforeach; ?>
