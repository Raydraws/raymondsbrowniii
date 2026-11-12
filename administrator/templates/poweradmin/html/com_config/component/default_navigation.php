<?php
/**
 * @package     Poweradmin
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>
<div class="sidebar-content">
	<?php if ($this->userIsSuperAdmin) : ?>
		<h3 class="nav-header"><?php echo Text::_('COM_CONFIG_SYSTEM'); ?></h3>
		<div class="j-card mb-4">
			<div class="j-card-body">
				<a href="index.php?option=com_config"><?php echo Text::_('COM_CONFIG_GLOBAL_CONFIGURATION'); ?></a>
			</div>
		</div>
	<?php endif; ?>
	<h3 class="nav-header"><?php echo Text::_('COM_CONFIG_COMPONENT_FIELDSET_LABEL'); ?></h3>
	<div class="j-card">
		<div class="j-card-body">
			<ul class="nav flex-column">
				<?php foreach ($this->components as $component) : ?>
					<?php
					$active = '';
					if ($this->currentComponent === $component)
					{
						$active = ' class="active"';
					}
					?>
					<li<?php echo $active; ?>>
						<a href="index.php?option=com_config&view=component&component=<?php echo $component; ?>"><?php echo Text::_($component); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>
