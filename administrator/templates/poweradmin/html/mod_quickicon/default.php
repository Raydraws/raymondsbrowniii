<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_quickicon
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;


use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;

// Load poweradmin quickicon JS instead of core
$wa = Factory::getDocument()->getWebAssetManager();
$wa->registerAndUseScript(
	'template.poweradmin.quickicon',
	'administrator/templates/poweradmin/js/quickicon.js',
	['version' => 'auto'],
	['type' => 'module'],
	['core']
);

$saveOrderingUrl = 'index.php?option=com_modules&task=Module.saveParamsOrderAjax&format=json&' . Session::getFormToken() . '=1';

$html = HTMLHelper::_('icons.buttons', $buttons);
?>
<?php if (!empty($html)) : ?>
	<nav class="quick-icons js-enable-dragula" aria-label="<?php echo Text::_('MOD_QUICKICON_NAV_LABEL') . ' ' . $module->title; ?>" data-containers=".quickicon-draggable-container" data-fields="sub_module_name[],module_id" data-url-quickicon="<?php echo $saveOrderingUrl; ?>">
		<div class="row quickicon-draggable-container">
			<?php echo $html; ?>
			<input type="hidden" value="<?php echo $module->id ?>" name="module_id">
		</div>
	</nav>
<?php endif; ?>
