<?php
/**
 * @package     Poweradmin
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/** @var \Joomla\Component\Admin\Administrator\View\Sysinfo\HtmlView $this */
?>
<div class="sysinfo">
	<div class="j-card">
		<div class="j-card-body">
			<table class="table j-striped-table">
				<caption class="sr-only">
					<?php echo Text::_('COM_ADMIN_CONFIGURATION_FILE'); ?>
				</caption>
				<thead>
					<tr>
						<th scope="col" style="width:300px">
							<?php echo Text::_('COM_ADMIN_SETTING'); ?>
						</th>
						<th scope="col">
							<?php echo Text::_('COM_ADMIN_VALUE'); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->config as $key => $value) : ?>
						<tr>
							<th scope="row">
								<?php echo $key; ?>
							</th>
							<td>
								<?php echo htmlspecialchars((string)$value, ENT_QUOTES); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
