<?php
/**
 * @package     Poweradmin
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/** @var \Joomla\Component\Admin\Administrator\View\Sysinfo\HtmlView $this */

?>
<div class="sysinfo">
	<div class="j-card">
		<div class="j-card-body">
			<table class="table j-striped-table">
				<caption class="sr-only">
					<?php echo Text::_('COM_ADMIN_DIRECTORY_PERMISSIONS'); ?>
				</caption>
				<thead>
					<tr>
						<th scope="col" style="width:850px">
							<?php echo Text::_('COM_ADMIN_DIRECTORY'); ?>
						</th>
						<th scope="col">
							<?php echo Text::_('COM_ADMIN_STATUS'); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->directory as $dir => $info) : ?>
						<tr>
							<th scope="row">
								<?php echo '&#x200E;' . HTMLHelper::_('directory.message', $dir, $info['message']); ?>
							</th>
							<td>
								<?php echo HTMLHelper::_('directory.writable', $info['writable']); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
