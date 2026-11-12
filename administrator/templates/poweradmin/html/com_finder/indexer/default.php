<?php
/**
 * @package     Poweradmin
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

Text::script('COM_FINDER_INDEXER_MESSAGE_COMPLETE', true);

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('script', 'administrator/templates/poweradmin/js/com_finder/indexer.js', array('version' => 'auto', 'relative' => false));
?>

<div class="text-center">
	<h1 id="finder-progress-header" class="m-t-2" aria-live="assertive"><?php echo Text::_('COM_FINDER_INDEXER_HEADER_INIT'); ?></h1>
	<p id="finder-progress-message" aria-live="polite"><?php echo Text::_('COM_FINDER_INDEXER_MESSAGE_INIT'); ?></p>
	<div id="progress" class="progress">
		<div id="progress-bar" class="progress-bar bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
	</div>
	<?php if (JDEBUG) : ?>
	<dl id="finder-debug-data" class="row">
	</dl>
	<?php endif; ?>
	<input id="finder-indexer-token" type="hidden" name="<?php echo Factory::getSession()->getFormToken(); ?>" value="1">
</div>
