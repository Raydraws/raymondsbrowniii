<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_extension_updates
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.core');
HTMLHelper::_('script', 'system/joomla-progress.js', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('stylesheet', 'mod_extension_updates/style.css', ['relative' => true]);

$updateMsg = '';

if (!empty($extensionContents['updatableInfo']['component']))
{
	$updateMsg .= Text::plural('MOD_EXTENSION_UPDATES_COMPONENTS', $extensionContents['updatableInfo']['component']);
}

if (!empty($extensionContents['updatableInfo']['module']))
{
	$updateMsg .= Text::plural('MOD_EXTENSION_UPDATES_MODULES', $extensionContents['updatableInfo']['module']);
}

if (!empty($extensionContents['updatableInfo']['plugin']))
{
	$updateMsg .= Text::plural('MOD_EXTENSION_UPDATES_PLUGINS', $extensionContents['updatableInfo']['plugin']);
}

if (!empty($extensionContents['updatableInfo']['library']))
{
	$updateMsg .= Text::plural('MOD_EXTENSION_UPDATES_LIBRARIES', $extensionContents['updatableInfo']['library']);
}

if (!empty($extensionContents['updatableInfo']['package']))
{
	$updateMsg .= Text::plural('MOD_EXTENSION_UPDATES_PACKAGES', $extensionContents['updatableInfo']['package']);
}
?>

<div class="mod-extension-updates text-center text-xl-left j-card-body module-<?php echo $module->id; ?>" id="mod-extension-updates-<?php echo $module->id; ?>">
	<div class="row align-items-center">
		<div class="col-12 col-xl-auto mb-4 mb-xl-0">
			<div class="j-card-module-update">
				<joomla-progress progress="<?php echo (!$extensionContents['isUpdatable']) ? 100 : $extensionContents['percentage']; ?>" radius="100" stroke="14" duration="300" >
					<h2><span data-counter="true"><?php echo (!$extensionContents['isUpdatable']) ? 100 : $extensionContents['percentage']; ?></span>%</h2>
					<span><?php echo Text::_('MOD_EXTENSION_UPDATES_UPDATED'); ?></span>
				</joomla-progress>
			</div>
		</div>
		<div class="col">
			<div class="j-card-vertical-content pl-xl-4">
				<?php
					if(!empty($extensionContents['updateJoomla']) && $extensionContents['isUpdatable']) {
						echo "<h4>" . Text::sprintf('MOD_EXTENSION_UPDATES_JOOMLA_OUTDATED', JVERSION) . "</h4>";
					}else{
						echo "<h4>". Text::sprintf('MOD_EXTENSION_UPDATES_JOOMLA_UPDATED', JVERSION) ."</h4>";
					}
					$update_condition = !empty($extensionContents['updatableInfo']['component']) || !empty($extensionContents['updatableInfo']['module']) || !empty($extensionContents['updatableInfo']['plugin']) || !empty($extensionContents['updatableInfo']['library']) || !empty($extensionContents['updatableInfo']['package']);
					if($update_condition){
						echo "<p>" . Text::sprintf('MOD_EXTENSION_UPDATES_UPDATE_EXTENSIONS_MSG', $updateMsg) . "</p>";
					}else{
						echo "<p>" . Text::_('MOD_EXTENSION_UPDATES_UPTODATE') . "</p>";
					}
				?>

				<div class="j-card-vertical-btn-group">
					<?php
						if(!empty($extensionContents['updateJoomla']) && $extensionContents['isUpdatable']) {
							?>
							<a class="btn btn-secondary" href="<?php echo Route::_('index.php?option=com_joomlaupdate'); ?>">
								<?php echo Text::sprintf('MOD_EXTENSION_UPDATES_JOOMLA_UPDATED_MSG', $extensionContents['updateJoomla']); ?>
							</a>
							<?php
						}
						if($update_condition){
							?>
							<a href="<?php echo Route::_('index.php?option=com_installer&view=update'); ?>" class="btn btn-primary">
								<span class="icon-rebuild icon-md" area-hidden="true"></span>
								<?php echo Text::_('MOD_EXTENSION_UPDATES_UPDATE_ALL'); ?>
							</a>
							<?php
						}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
