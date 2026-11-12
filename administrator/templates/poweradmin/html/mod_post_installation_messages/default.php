<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_post_installation_messages
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
HTMLHelper::_('script', 'system/joomla-callout.min.js', ['version'=> 'auto', 'relative' => true]);

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

$hideLinks = $app->input->getBool('hidemainmenu');
$messages = empty($messages) ? [] : $messages;
?>
<?php if ($app->getIdentity()->authorise('core.manage', 'com_postinstall')) : ?>
	<div class="j-card-body">
		<a href="#" id="showCollout" class="header-item-link<?php echo ($hideLinks ? ' disabled' : ''); ?>"<?php echo ($hideLinks ? ' disabled' : ''); ?> title="<?php echo Text::_('MOD_POST_INSTALLATION_MESSAGES'); ?>" role="button">
			<span class="header-item-icon" aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('notification', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span> <span class="header-item-text"><?php echo Text::_('MOD_POST_INSTALLATION_MESSAGES'); ?></span> <span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('right-arrow', 14, 'currentColor'); ?></span>
			<?php if (count((array) $messages) > 0) : ?>
				<span class="badge badge-pill badge-danger"><?php echo count($messages); ?></span>
			<?php endif; ?>
		</a>
	</div>
	<?php if (!$hideLinks) : ?>
		<joomla-callout for="#showCollout" dismiss="true" position="bottom">
			<div class="callout-title">
				<?php echo Text::_('MOD_POST_INSTALLATION_MESSAGES'); ?>
			</div>
			<div class="callout-content">
				<?php if (empty($messages)) : ?>
					<div class="dropdown-item">
						<a href="<?php echo Route::_('index.php?option=com_postinstall&eid=' . $joomlaFilesExtensionId); ?>">
							<?php echo Text::_('COM_POSTINSTALL_LBL_NOMESSAGES_TITLE'); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
			<div class="callout-footer">
				<?php foreach ($messages as $message) : ?>
					<?php $route = 'index.php?option=com_postinstall&amp;eid=' . $joomlaFilesExtensionId; ?>
					<?php $title = Text::_($message->title_key); ?>
					<a href="<?php echo Route::_($route); ?>" class="callout-link" title="<?php echo $title; ?>">
						<?php echo $title; ?>
					</a>
				<?php endforeach; ?>
			</div>
		</joomla-callout>
	<?php endif; ?>
<?php endif; ?>
