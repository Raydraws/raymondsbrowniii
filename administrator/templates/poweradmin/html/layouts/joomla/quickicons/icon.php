<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// Load mod_quickicon language file to ensure all strings are available
$lang = Factory::getApplication()->getLanguage();
$lang->load('mod_quickicon', JPATH_ADMINISTRATOR . '/modules/mod_quickicon');

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

$id      = empty($displayData['id']) ? '' : (' id="' . $displayData['id'] . '"');
$target  = empty($displayData['target']) ? '' : (' target="' . $displayData['target'] . '"');
$onclick = empty($displayData['onclick']) ? '' : (' onclick="' . $displayData['onclick'] . '"');

if (isset($displayData['ajaxurl'])) {
	$size = 'small';
	$dataAttributes = 'data-url="' . $displayData['ajaxurl'] . '"';
	$dataAttributes .= ' data-status="loading"';
} else {
	$size = 'big';
	$dataAttributes = 'data-status="none"';
	$dataLoading = '';
}

// The title for the link (a11y)
$title = empty($displayData['title']) ? '' : (' title="' . $this->escape($displayData['title']) . '"');

// The information
$text = empty($displayData['text']) ? '' : ('<span class="j-links-link">' . $displayData['text'] . '</span>');

$tmp = [];

// Set id and class pulse for update icons
if ($id && ($displayData['id'] === 'plg_quickicon_joomlaupdate'
	|| $displayData['id'] === 'plg_quickicon_extensionupdate'
	|| $displayData['id'] === 'plg_quickicon_privacycheck'
	|| $displayData['id'] === 'plg_quickicon_overridecheck'
	|| !empty($displayData['class'])))
{
	$tmp[] = 'pulse';
}

// Add the button class
if (!empty($displayData['class']))
{
	$tmp[] = $this->escape($displayData['class']);
}

if (!isset($displayData['icon_class'])) {
	$displayData['icon_class'] = 'primary';
}

// Make the class string
$class = !empty($tmp) ? ' class="' . implode(' ', array_unique($tmp)) . '"' : '';
?>
<div class="col-md-6 col-lg-4 col-xl-3 handle j-quickicon<?php echo isset($displayData['ajaxurl']) ? ' j-quickicon-skeleton' : ''; ?>" data-dragable-group="none" <?php echo $dataAttributes; ?>>
	<div <?php echo $id; ?> class="j-card j-card-has-hover mb-4 <?php echo $class; ?>">
		<div class="j-card-overview-box pt-3">
			<div class="j-card-overview-icon j-<?php echo $displayData['icon_class']; ?>">
				<?php if (!empty($displayData['svg'])): ?>
					<?php echo \JHtmlPoweradmin::icon($displayData['svg'], 20, ($displayData['fill'] ?? null), ['class' => 'quickicon-svg']); ?>
				<?php else: ?>
					<i class="<?php echo $displayData['image']; ?>"></i>
				<?php endif; ?>
			</div>
			<div class="j-card-overview-content" area-hidden="true">
				<?php if (isset($displayData['ajaxurl'])): ?>
					<span class="j-counter-animation">
						<i class="icon-spinner" aria-hidden="true"></i>
					</span>
				<?php endif; ?>
				<?php // Name indicates the component
				if (isset($displayData['name'])): ?>
					<sub <?php echo isset($displayData['ajaxurl']) ? ' aria-hidden="true"' : ''; ?>>
						<?php echo Text::_($displayData['name']); ?>
					</sub>
				<?php else: ?>
					<sub>&nbsp;</sub>
				<?php endif; ?>
				<div class="quickicon-sr-desc sr-only"></div>
			</div>
		</div>

		<div class="j-card-footer j-card-footer-lg">
			<div class="j-card-footer-item">
				<a href="<?php echo $displayData['link']; ?>">
					<span class="j-card-icon j-icon-lg" aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('eye', 20, 'currentColor'); ?></span>
					<?php
					// Get component name for screen reader
					$componentName = Text::_($displayData['name']);
					$srOnlyKey = $displayData['name'] . '_ADD_SRONLY';
					$srOnlyText = Text::_($srOnlyKey);
					// If translation doesn't exist, use a generic fallback
					if ($srOnlyText === $srOnlyKey) {
						$srOnlyText = Text::sprintf('MOD_QUICKICON_VIEW_ALL', $componentName);
					}
					?>
					<span class="sr-only"><?php echo $srOnlyText; ?></span>
					<span aria-hidden="true"><?php echo Text::sprintf('MOD_QUICKICON_VIEW_ALL', $componentName); ?></span>
				</a>
			</div>
			<?php // Add the link to the edit-form
			if (isset($displayData['linkadd'])): ?>
				<div class="j-card-footer-item j-card-footer-icon">
					<?php
					$componentName = Text::_($displayData['name']);
					$addNewText = Text::sprintf('MOD_QUICKICON_ADD_NEW', $componentName);
					$viewAllText = Text::sprintf('MOD_QUICKICON_VIEW_ALL', $componentName);
					?>
					<a href="<?php echo $displayData['linkadd']; ?>" title="<?php echo htmlspecialchars($addNewText, ENT_QUOTES, 'UTF-8'); ?>"><span aria-hidden="true"><?php echo \JHtmlPoweradmin::icon('plus', 14, 'currentColor'); ?></span><span class="sr-only"><?php echo $viewAllText; ?></span></a>
				</div>
			<?php endif; ?>
		</div>

		<input type="hidden" name="sub_module_name[]" value="<?php echo Text::_($displayData['name']); ?>" class="width-20 text-area-order">
	</div>
</div>
