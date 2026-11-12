<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use AcyCheckerCmsServices\Language;
use AcyCheckerCmsServices\Security;

?>
<h2 class="cell acyc__title margin-top-2"><?php echo Language::translation('ACYC_WHEN'); ?></h2>
<div class="cell grid-x">
	<p class="cell medium-3"><?php echo Language::translation('ACYC_WHEN_CLEANING_PROCESS_RUN'); ?></p>
    <?php foreach ($this->data['execution_select'] as $executionInfo) { ?>
		<div class="cell shrink margin-right-1">
			<label>
				<input type="radio" <?php echo $this->data['current_config']['execution_selected'] === $executionInfo['value'] ? 'checked' : ''; ?>
					   value="<?php echo Security::escape($executionInfo['value']); ?>"
					   name="acyc_config[execution_selected]">
				<span><?php echo $executionInfo['text']; ?></span>
			</label>
		</div>
    <?php } ?>
</div>
