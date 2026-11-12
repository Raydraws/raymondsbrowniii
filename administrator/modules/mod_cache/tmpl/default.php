<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_cache
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

// cache ajax URL - using plugin-based handler to avoid core file modification
Factory::getDocument()->addScriptOptions('cacheurl', Uri::root() . 'administrator/index.php?option=com_ajax&plugin=cache&group=system&format=json');

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}

?>

<div class="mod-extension-cache text-center text-xl-left module-<?php echo $module->id; ?>" id="mod-extension-cache-<?php echo $module->id; ?>">
	<div class="j-card-body">
		<div class="row align-items-center">
			<div class="col-12 col-xl-auto mb-4 mb-xl-0">
				<?php echo HTMLHelper::_('image', 'mod_cache/cache-cleaning.svg', Text::_('MOD_CACHE_CACHE_CLEANING_ALT_TEXT'), array('class' => "mod-cache-image", 'width' => "100%"), true); ?>
			</div>
			<div class="col">
				<div class="j-card-vertical-content pl-xl-4">
					<div class="j-card-counter-msg">
						<span class="j-count-number j-cache-animation">&lrm;<?php echo $cacheInfo['size']; ?></span>
						<?php echo Text::sprintf('MOD_CACHE_QUICKICON_TOTAL_CACHE', $cacheInfo['unit']); ?>
					</div>
					<p><?php echo Text::_('MOD_CACHE_MSG'); ?></p>
					<div class="j-card-vertical-btn-group">
						<?php if($cacheInfo['raw']): ?>
							<button id="jclear-cache-btn" class="btn btn-primary" data-size="<?php echo $cacheInfo['size']; ?>" type="button">
								<span class="icon-md"><?php echo \JHtmlPoweradmin::icon('trash', 16, 'currentColor', ['aria-hidden' => 'true']); ?></span>
								<?php echo Text::_('MOD_CACHE_QUICKICON_CLEAR_CACHE'); ?>
							</button>
						<?php else: ?>
							<button class="btn btn-primary disabled">
								<span class="icon-info-circle icon-md" area-hidden="true"></span>
								<?php echo Text::_('MOD_CACHE_QUICKICON_SRONLY_NOCACHE'); ?>
							</button>
						<?php endif;?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
