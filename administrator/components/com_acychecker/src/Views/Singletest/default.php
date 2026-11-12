<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use AcyChecker\Services\ViewService;

?>
<div class="cell grid-x" id="acyc__singletest">
	<div class="cell grid-x acyc_content margin-bottom-2">
        <?php include ViewService::getView('Singletest', 'default_input'); ?>
	</div>
	<div class="cell grid-x" id="acyc__results-container">
	</div>
</div>
