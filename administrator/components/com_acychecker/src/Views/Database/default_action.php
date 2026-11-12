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
<h2 class="cell acyc__title margin-top-2"><?php echo Language::translation('ACYC_ACTION'); ?></h2>
<div class="cell grid-x">
	<p class="cell medium-3"><?php echo Language::translation('ACYC_ACTION_WOULD_YOU_TAKE_FAKE_USER'); ?></p>
    <?php foreach ($this->data['action_select'] as $actionSelect) { ?>
		<div class="cell shrink margin-right-1">
			<label>
				<input id="<?php echo Security::escape($actionSelect['value']); ?>"
					   type="radio" <?php echo $this->data['current_config']['action_selected'] === $actionSelect['value'] ? 'checked' : ''; ?>
					   name="acyc_config[action_selected]"
					   value="<?php echo Security::escape($actionSelect['value']); ?>">
				<span><?php echo $actionSelect['text']; ?></span>
			</label>
		</div>
    <?php } ?>
</div>
