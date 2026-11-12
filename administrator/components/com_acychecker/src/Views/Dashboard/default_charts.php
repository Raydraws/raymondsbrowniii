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
<div class="cell grid-x acyc_content margin-top-2" id="acyc__dashboard__chart__container">
    <?php if (!empty($this->data['emptyStats'])) { ?>
		<h1 class="cell text-center acyc__dashboard__chart__title-empty"><?php echo Language::translation('ACYC_FAKE_STATS') ?></h1>
    <?php } ?>
	<div class="cell grid-x align-center margin-top-2">
		<h3 class="cell text-center acyc__dashboard__chart__title"><?php echo Language::translation('ACYC_USER_BLOCKED_THIS_MONTH'); ?></h3>
        <?php if (!empty($this->data['emptyStatsBlocked']) && empty($this->data['emptyStats'])) { ?>
			<p class="cell text-center"><?php echo Language::translation('ACYC_FAKE_STATS_BLOCKED_USERS'); ?></p>
        <?php } ?>
		<div class="cell text-center"
			 id="acyc__dashboard__chart__blocked"
			 data-acyc-options="<?php echo Security::escape($this->data['block_reason']) ?>"></div>
	</div>
	<div class="cell grid-x align-center margin-top-2">
		<h3 class="cell text-center acyc__dashboard__chart__title"><?php echo Language::translation('ACYC_REPARTITION_EMAIL_TESTED'); ?></h3>
        <?php foreach ($this->data['donutData'] as $donutData) { ?>
			<div class="cell medium-2"
				 id="acyc__dashboard__chart__<?php echo $donutData['nameKey']; ?>"
				 data-acyc-<?php echo $donutData['nameKey']; ?>="<?php echo $donutData['value']; ?>"></div>
        <?php } ?>
	</div>
	<div class="cell grid-x align-center margin-top-2">
		<h3 class="cell text-center acyc__dashboard__chart__title"><?php echo Language::translation('ACYC_NUMBER_REQUEST_MONTH') ?></h3>
		<div class="cell grid-x margin-top-1"
			 id="acyc__dashboard__chart__line"
			 data-acyc-options="<?php echo Security::escape(json_encode($this->data['month_calls'])); ?>"></div>
	</div>
</div>
