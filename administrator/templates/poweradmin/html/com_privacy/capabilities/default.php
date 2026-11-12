<?php
/**
 * @package     Poweradmin
 * @subpackage  com_privacy
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

use Joomla\CMS\Language\Text;

/** @var PrivacyViewCapabilities $this */

?>
<div id="j-main-container">
	<div class="j-alert j-alert-info d-flex">
		<div class="j-alert-icon-wrap"><span  aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('help-2', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span></div>
		<div class="j-alert-info-wrap">
			<h2 class="alert-heading"><?php echo Text::_('COM_PRIVACY_MSG_CAPABILITIES_ABOUT_THIS_INFORMATION'); ?></h2>
			<?php echo Text::_('COM_PRIVACY_MSG_CAPABILITIES_INTRODUCTION'); ?>
		</div>
	</div>
	<?php if (empty($this->capabilities)) : ?>
		<div class="j-alert j-alert-info d-flex mt-4">
			<div class="j-alert-icon-wrap"><span  aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('help-2', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span></div>
			<div class="j-alert-info-wrap"><?php echo Text::_('COM_PRIVACY_MSG_CAPABILITIES_NO_CAPABILITIES'); ?></div>
		</div>
	<?php else : ?>
		<?php foreach ($this->capabilities as $extension => $capabilities) : ?>
			<details>
			<summary><?php echo $extension; ?></summary>
				<?php if (empty($capabilities)) : ?>
					<div class="j-alert j-alert-info d-flex mt-4">
						<div class="j-alert-icon-wrap"><span  aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('help-2', 14, 'currentColor', ['aria-hidden' => 'true']); ?></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span></div>
						<div class="j-alert-info-wrap"><?php echo Text::_('COM_PRIVACY_MSG_EXTENSION_NO_CAPABILITIES'); ?></div>
					</div>
				<?php else : ?>
					<ul>
						<?php foreach ($capabilities as $capability) : ?>
							<li><?php echo $capability; ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</details>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
