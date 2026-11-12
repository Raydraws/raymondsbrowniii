<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_loginsupport
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
?>
<section class="loginsupport">
	<ul class="list-group list-group-flush">
		<li class="list-group-item"><a href="<?php echo $params->get('forum_url') ?>" target="_blank" rel="nofollow noopener">
			<?php echo Text::_('MOD_LOGINSUPPORT_FORUM'); ?></a>
		</li>
		<li class="list-group-item"><a href="<?php echo $params->get('documentation_url') ?>" target="_blank" rel="nofollow noopener">
			<?php echo Text::_('MOD_LOGINSUPPORT_DOCUMENTATION'); ?></a>
		</li>
		<li class="list-group-item"><a href="<?php echo $params->get('news_url') ?>" target="_blank" rel="nofollow noopener">
			<?php echo Text::_('MOD_LOGINSUPPORT_NEWS'); ?></a>
		</li>
	</ul>
</section>
