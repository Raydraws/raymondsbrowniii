<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_checkins
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<div class="mod-checkins text-center text-xl-left mod-checkins-<?php echo $params->get('moduleclass_sfx', ''); ?>" id="mod-checkins-<?php echo $module->id; ?>">
	<div class="j-card-body">
		<div class="row align-items-center">
			<div class="col-12 col-xl-auto mb-4 mb-xl-0">
				<?php if($checkins > 0) : ?>
					<?php echo HTMLHelper::_('image', 'mod_checkins/checkins.svg', Text::_('MOD_CHECKINS_ALT_TEXT'), array('class' => "mod-checkins-image", 'width' => "100%"), true); ?>
				<?php else : ?>
					<?php echo HTMLHelper::_('image', 'mod_checkins/no-checkins.svg', Text::_('MOD_CHECKINS_NO_CHECKINS_ALT_TEXT'), array('class' => "mod-checkins-image", 'width' => "100%"), true); ?>
				<?php endif; ?>
			</div>
			<div class="col">
				<div class="j-card-vertical-content pl-xl-4">
					<div class="j-card-counter-msg">
						<?php echo Text::plural('MOD_CHECKINS_GLOBAL_CHECKIN_MSG', $checkins); ?>
					</div>
					<p><?php echo Text::_('MOD_CHECKINS_DESC'); ?></p>
					<div class="j-card-vertical-btn-group">
						<a href="<?php echo Route::_('index.php?option=com_checkin'); ?>" class=""><?php echo Text::_('MOD_CHECKINS_GO_TO_CHECKINS'); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
