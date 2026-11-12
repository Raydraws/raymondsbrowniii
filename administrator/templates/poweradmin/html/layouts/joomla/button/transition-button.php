<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;


HTMLHelper::_('bootstrap.popover');

/**
 * @var $icon    string
 * @var $title   string
 * @var $value   string
 * @var $task    string
 * @var $options array
 */
extract($displayData, EXTR_OVERWRITE);

HTMLHelper::_('script', 'system/joomla-dropdown.min.js', ['version' => 'auto', 'relative' => true]);

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

$only_icon = empty($options['transitions']);
$disabled = !empty($options['disabled']);
$tip = !empty($options['tip']);
$id = (int) $options['id'];
$tipTitle = $options['tip_title'];
$checkboxName = $options['checkbox_name'];

switch(strtoupper($options['stage']))
{
	case 'UNPUBLISHED':
		$color = 'danger';
	break;
	case 'PUBLISHED':
		$color = 'success';
	break;
	case 'TRASHED':
		$color = 'warning';
	break;
	default:
		$color = 'primary';
}

$workflowActions = $options['workflow_actions'] ?? [];
$transitions = json_decode(json_encode($options['transitions']));

if (!empty($workflowActions))
{
	Factory::getDocument()->addScriptDeclaration("
		if (typeof window.poweradminRunWorkflowAction === 'undefined') {
			window.poweradminRunWorkflowAction = function(element, checkbox) {
				var task = element.dataset.task || '';

				if (task) {
					Joomla.listItemTask(checkbox, task);
				}
			};
		}
	");
}
else
{
Factory::getDocument()->addScriptDeclaration("
		if (typeof window.poweradminRunTransition === 'undefined') {
			window.poweradminRunTransition = function(element, id, checkbox, task) {
				var hidden = document.getElementById(id);

				if (hidden) {
					hidden.value = element.dataset.value;
				}

				Joomla.listItemTask(checkbox, task || 'articles.runTransition');
			};
	}
");
}

$attribs = [
	'id'        => 'transition-select_' . (int) $id,
	'list.attr' => [
		'class'    => 'custom-select custom-select-sm form-control form-control-sm',
		'onchange' => "Joomla.listItemTask('" . $checkboxName . $this->escape($row ?? '') . "', 'articles.runTransition')"]
];

?>


<div id="publishColloutId-<?php echo $id; ?>" class="j-transition-group" data-color="<?php echo $color; ?>">
	<a href="#" class="j-transition-select" data-target="<?php echo 'transition-select_' . (int) $id; ?>"><?php echo $this->escape($options['stage']); ?> <span class="down-arrow"><?php echo \JHtmlPoweradmin::icon('down-arrow', 14, 'currentColor'); ?></span></a>
	<joomla-dropdown for="<?php echo 'transition-select_' . (int) $id; ?>">
		<?php if (!empty($workflowActions)) : ?>
			<?php foreach ($workflowActions as $action) : ?>
				<li>
					<a href="javascript:" class="dropdown-item"
						data-task="<?php echo $action['task']; ?>"
						onclick="window.poweradminRunWorkflowAction(this, '<?php echo $checkboxName . $this->escape($row ?? ''); ?>');">
						<?php echo $action['text']; ?>
					</a>
				</li>
			<?php endforeach; ?>
		<?php else : ?>
		<?php foreach($transitions as $transition) : ?>
				<li>
					<a href="javascript:" class="dropdown-item"
					   onclick="window.poweradminRunTransition(this, '<?php echo 'transition_' . (int) $id; ?>', '<?php echo $checkboxName . $this->escape($row ?? ''); ?>');"
					   data-value="<?php echo $transition->value; ?>">
						<?php echo $transition->text; ?>
					</a>
				</li>
		<?php endforeach; ?>
		<input type="hidden" id="transition_<?php echo (int) $id; ?>" name="transition_<?php echo (int) $id; ?>" value="">
		<?php endif; ?>
	</joomla-dropdown>
</div>

<joomla-callout action="hover" for="#publishColloutId-<?php echo $id; ?>" position="top">
	<div class="callout-title"><?php echo Text::_($tipTitle ? : $title); ?></div>
	<div class="callout-content"><?php echo Text::_($title); ?></div>
</joomla-callout>