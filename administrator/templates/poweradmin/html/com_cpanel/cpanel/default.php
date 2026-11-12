<?php
/**
 * @package     Poweradmin
 * @subpackage  com_cpanel
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

HTMLHelper::_('behavior.core');
// Load JavaScript message titles
Text::script('ERROR');
Text::script('WARNING');
Text::script('NOTICE');
Text::script('MESSAGE');

Text::script('COM_CPANEL_UNPUBLISH_MODULE_SUCCESS');
Text::script('COM_CPANEL_UNPUBLISH_MODULE_ERROR');

HTMLHelper::_('script', 'com_cpanel/admin-cpanel-default.js', array('version' => 'auto', 'relative' => true));

$user = Factory::getUser();

HTMLHelper::_('script', 'com_cpanel/admin-add_module.js', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('script', 'vendor/dragula/dragula.js', ['framework' => false, 'relative' => true]);
HTMLHelper::_('stylesheet', 'vendor/dragula/dragula.css', ['framework' => false, 'relative' => true, 'pathOnly' => false]);
HTMLHelper::_('script', 'mod_quickicon/cpanel-draggble.js', ['version' => 'auto', 'relative' => true]);

$saveOrderingUrl = 'index.php?option=com_modules&task=module.saveModuleOrderDashboard&format=json&tmpl=component&' . Session::getFormToken() . '=1';

// Organize modules - split into columns if modules are provided as a flat array from the view
// Check if modules are already organized (associative array with expected keys)
$isOrganized = is_array($this->modules) && (isset($this->modules['quickicon']) || isset($this->modules['left_column']) || isset($this->modules['right_column']));

if (!$isOrganized) {
	// Reorganize modules if they come as a flat array from the view
	$organizedModules = array(
		'quickicon' => !empty($this->quickicons) ? $this->quickicons : array(),
		'left_column' => array(),
		'right_column' => array()
	);
	
	// Split modules into left and right columns based on column_position param
	// column_position: 0 = left (default), 1 = right
	if (!empty($this->modules) && is_array($this->modules)) {
		foreach ($this->modules as $module) {
			$moduleParams = new Registry($module->params);
			$columnPosition = (int) $moduleParams->get('column_position', 0);
			
			if ($columnPosition === 1) {
				$organizedModules['right_column'][] = $module;
			} else {
				$organizedModules['left_column'][] = $module;
			}
		}
	}
	
	$this->modules = $organizedModules;
}
?>
<div id="cpanel-modules">
	<div class="cpanel-modules <?php echo $this->position; ?>">
	<?php if (!empty($this->modules['left_column'])): ?>
				<?php
					foreach($this->modules['left_column'] as $module)
					{
						if ($module->name === 'submenu' && $module->position === 'cpanel-system' && $module->title === "System Dashboard") {
							echo '<div style="width: 100%; column-count: 2; column-gap: 1.5rem; padding-bottom: 1.5rem; " class="j-layout-masonry-special">';
							echo ModuleHelper::renderModule($module, array('style' => 'card'));
							echo '</div>';
						}
					}
				?>
			<?php endif; ?>
		<?php
			if (!empty($this->modules['quickicon']))
			{
				foreach ($this->modules['quickicon'] as $quickicon)
				{
					echo ModuleHelper::renderModule($quickicon, array('style' => 'simple'));
				}
			}
		?>
		<div class="row js-enable-dragula" data-fields="order[],cid[],position[]" data-containers="#cpanel-left-col,#cpanel-right-col" data-url-card="<?php echo $saveOrderingUrl; ?>" data-direction="asc" data-nested="true" data-drag_handler="handle">
			<div class="col-12 col-md-6" id="cpanel-left-col" data-position="left">
				<?php if (!empty($this->modules['left_column'])): ?>
						<?php
							foreach($this->modules['left_column'] as $module)
							{
								if ($module->title !== "System Dashboard") {
									echo ModuleHelper::renderModule($module, array('style' => 'card'));
								}
							}
						?>
				<?php endif; ?>
			</div>
			<div class="col-12 col-md-6" id="cpanel-right-col" data-position="right">
				<?php if (!empty($this->modules['right_column'])): ?>
						<?php
							foreach($this->modules['right_column'] as $module)
							{
								echo ModuleHelper::renderModule($module, array('style' => 'card'));
							}
						?>
				<?php endif; ?>
			</div>
		</div>

		<?php if ($user->authorise('core.create', 'com_modules')) : ?>
			<div>
				<a href="#moduleEditModal" data-href="#moduleDashboardAddModal" class="btn btn-secondary btn-xl btn-block add-module-btn" data-toggle="modal" role="button">
					<span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('plus', 14, 'currentColor'); ?></span> <?php echo Text::_('COM_CPANEL_ADD_DASHBOARD_MODULE'); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
	
</div>

<?php 
// Load the webcomponent helper from template override
$webcomponentPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/webcomponent.php';

if (file_exists($webcomponentPath)) {
	require_once $webcomponentPath;
}

echo JHtmlWebcomponent::renderModal(
	'moduleDashboardAddModal',
	array(
		'title'       => Text::_('COM_CPANEL_ADD_MODULE_MODAL_TITLE'),
		'backdrop'    => 'static',
		'url'         => Route::_('index.php?option=com_cpanel&task=addModule&function=jSelectModuleType&position=' . $this->escape($this->position)),
		'height'      => '75vh',
		'width'       => '85vw',
		'class'		  => 'j-modal-gray',
		'bodyHeight'  => 75,
		'modalWidth'  => 85,
		'footer'      => '<button type="button" class="button-cancel btn btn-sm btn-danger" aria-label="' . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '" data-dismiss><span aria-hidden="true">' . \JHtmlPoweradmin::icon('cancel', 14, 'currentColor', ['aria-hidden' => 'true']) . '</span>'. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') .'</button>
						<button type="button" class="button-save btn btn-sm btn-success hidden" aria-label="' . Text::_('JSAVE') . '" data-target="#saveBtn"><span class="icon-save" aria-hidden="true"></span>'. Text::_('JSAVE') .' </button>',
	)
);
?>
