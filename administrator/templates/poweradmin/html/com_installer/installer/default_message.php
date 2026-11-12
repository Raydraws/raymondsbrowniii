<?php
/**
 * @package     Poweradmin
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

$state    = $this->get('State');
$message1 = $state->get('message');
$message2 = $state->get('extension_message');
?>

<?php if ($message1) : ?>
	<div class="container-fluid">
		<div class="col-md-12">
			<strong><?php echo $message1; ?></strong>
		</div>
	</div> 
<?php endif; ?> 
<?php if ($message2) : ?> 
	<div class="container-fluid">
		<div class="col-md-12">
			<?php echo $message2; ?>
		</div> 
	</div>
<?php endif; ?>
