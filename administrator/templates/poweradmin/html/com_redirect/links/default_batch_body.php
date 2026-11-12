<?php
/**
 * @package     Poweradmin
 * @subpackage  com_redirect
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$published = $this->state->get('filter.published');
$params    = $this->params;
$separator = $params->get('separator', '|');
?>

<div class="container">
	<div class="row">
		<div class="control-group col-md-12">
			<p><?php echo Text::sprintf('COM_REDIRECT_BATCH_TIP', $separator); ?></p>
			<div class="controls">
				<textarea class="col-md-12" rows="10" value="" id="batch_urls" name="batch_urls"></textarea>
			</div>
		</div>
	</div>
</div>
