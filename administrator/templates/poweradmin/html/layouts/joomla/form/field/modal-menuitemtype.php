<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.poweradmin
 *
 * Custom layout override for the menu item type selector.
 *
 * @var   array  $displayData
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
$field         = $displayData['field'] ?? null;
$cards         = $displayData['cards'] ?? [];
$selected      = $displayData['selected'] ?? '';
$selectedLabel = $displayData['selectedLabel'] ?? '';
$inputId       = $field ? $field->id : 'jform_type';

$dropdownId = $inputId . '_dropdown';
?>
	<a href="javascript:void(0);" class="btn btn-secondary text-left j-has-dropdown"
	   data-target="<?php echo $dropdownId; ?>" aria-haspopup="true" aria-expanded="false">
		<?php echo Text::_('JSELECT'); ?> <span class="icon-caret-v" aria-hidden="true"></span>
	</a>

	<joomla-dropdown for="<?php echo $dropdownId; ?>" aria-labelledby="<?php echo $dropdownId; ?>">
		<?php foreach ($cards as $group) : ?>
			<li class="has-submenu right" data-action="hover">
				<a class="dropdown-item"><?php echo $group['title']; ?></a>
				<ul class="submenu">
					<?php foreach ($group['items'] as $item) : ?>
						<li>
							<a class="dropdown-item" href="javascript:void(0);"
							   onclick="Joomla.setMenuType('<?php echo $item['value']; ?>', '<?php echo $item['clientId']; ?>');">
								<h4 class="menu-item-title"><?php echo $item['title']; ?></h4>
								<span class="text-mute"><?php echo $item['description']; ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</li>
		<?php endforeach; ?>
	</joomla-dropdown>

	<?php if ($selectedLabel) : ?>
		<div class="selected-menu-type small text-muted mt-2">
			<?php echo Text::_('COM_MENUS_ITEM_FIELD_TYPE_LABEL'); ?>:
			<strong><?php echo htmlspecialchars($selectedLabel, ENT_COMPAT, 'UTF-8'); ?></strong>
		</div>
	<?php endif; ?>

	<input type="hidden" required="required" readonly="readonly"
	       id="<?php echo $inputId; ?>" value="<?php echo htmlspecialchars($selected, ENT_COMPAT, 'UTF-8'); ?>"
	       class="form-control">
	<input type="hidden" name="<?php echo $field ? $field->name : 'jform[type]'; ?>"
	       value="<?php echo htmlspecialchars($selected, ENT_COMPAT, 'UTF-8'); ?>" id="<?php echo $inputId; ?>_val">

