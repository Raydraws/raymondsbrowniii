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

<div class="cell grid-x grid-margin-x align-center">
    <?php
    if ($this->data['allow_stop_periodic']) {
        echo TooltipService::tooltip(
            '<button class="cell shrink button button-secondary acyc_button_submit" data-task="stop" type="button">'.Language::translation('ACYC_STOP_CURRENT').'</button>',
            Language::translationSprintf('ACYC_STOP_CURRENT_DESC', strtolower($this->data['execution_select'][$this->data['current_config']['execution_selected']]['text']))
        );
    }
    ?>
	<button class="cell shrink button acyc_button_submit"
			data-task="save"
			data-condition="tablesSelected"
			id="acyc__database__save__button">
        <?php echo Language::translation('ACYC_SAVE'); ?>
	</button>
</div>
