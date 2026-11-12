<?php

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

/**
 * @package     Poweradmin
 * @subpackage  com_modules
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$app = Factory::getApplication();

$function  = $app->input->getCmd('function');

Text::script('COM_MODULES_SELECT', true);
$moduleData = array(
	'items' 			=> $this->items,
	'clientId' 			=> $this->state->get('client_id', 0),
	'modalLink' 		=> $this->modalLink,
	'functionPlain' 	=> $function,
	'functionEscape' 	=> $this->escape($function),
	'routeUrl' 			=> Route::_('index.php?option=com_modules&task=module.add&client_id=')
);

Factory::getDocument()->addScriptOptions('modules', $moduleData);
HTMLHelper::_('script', 'com_modules/admin-select-modal.js', ['version' => 'auto', 'relative' => true]);
?>

<div class="j-search-module-container mb-4">
	<h2 class="mb-3" id="j-search-module-label"><?php echo Text::_('COM_MODULES_TYPE_CHOOSE'); ?></h2>
	<div class="input-group">
		<span aria-hidden="true" style="position: absolute; z-index: 999999; transform: translate(80%, 35%);"><?php echo \JHtmlPoweradmin::icon('search', 14, 'currentColor'); ?></span>
		<input type="text" class="form-control" id="j-search-cpanel-module" autocomplete="off" aria-labelledby="j-search-module-label" placeholder="<?php echo Text::_('COM_MODULES_SEARCH_MODULES'); ?>">
	</div>
</div>

<div id="new-modules-list">
	<div class="row">
		<?php foreach ($this->items as &$item) : ?>
			<div class="col-sm-6 col-md-4 col-lg-3">
				<div class="j-card mb-4">
					<?php // Prepare variables for the link. ?>
					<?php $link = 'index.php?option=com_modules&task=module.add&client_id=' . $this->state->get('client_id', 0) . $this->modalLink . '&eid=' . $item->extension_id; ?>
					<?php $name = $this->escape($item->name); ?>
					<?php $desc = HTMLHelper::_('string.truncate', $this->escape(strip_tags($item->desc)), 200); ?>

					<div class="j-card-header">
						<strong><?php echo $name; ?></strong>
					</div>

					<div class="j-card-body">
						<p class="text-muted m-0">
							<?php echo $desc; ?>
						</p>
					</div>

					<div class="j-card-footer">
						<div class="j-card-footer-item">
							<a href="<?php echo Route::_($link); ?>" <?php echo $function ? 'class=" select-link" data-function="' . $this->escape($function) . '"' : ''; ?> aria-label="<?php echo Text::sprintf('COM_MODULES_SELECT_MODULE', $item->name); ?>">
								<?php echo Text::_('COM_MODULES_SELECT'); ?>
							</a>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
