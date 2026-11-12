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
<h2 class="cell acyc__title margin-top-2"><?php echo Language::translation('ACYC_MAINTENANCE'); ?></h2>
<div class="cell grid-x">
    <?php
    echo TooltipService::tooltip(
        '<button type="button" class="cell medium-shrink button button-secondary" id="checkdb_button">'.Language::translation('ACYC_CHECK_DB').'</button>',
        Language::translation('ACYC_INTRO_CHECK_DATABASE'),
        'cell medium-shrink'
    );
    ?>
	<div class="cell auto padding-left-1" id="checkdb_report"></div>
</div>
