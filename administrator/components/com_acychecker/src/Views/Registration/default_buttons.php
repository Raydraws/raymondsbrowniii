<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use AcyChecker\Services\TooltipService;
use AcyCheckerCmsServices\Language;

?>

<div class="cell grid-x grid-margin-x align-center margin-top-1">
    <?php
    if (!empty($this->data['current_config']['registration_integrations'])) {
        echo TooltipService::tooltip(
            '<button class="cell shrink button button-secondary acyc_button_submit" data-task="stop" type="button">'.Language::translation('ACYC_DISABLE').'</button>',
            Language::translation('ACYC_DISABLE_USER_CHECK_ON_REGISTRATION')
        );
    }
    ?>
	<button class="cell shrink button acyc_button_submit"
			data-task="save"
			data-condition="tablesSelected">
        <?php echo Language::translation('ACYC_SAVE'); ?>
	</button>
</div>
