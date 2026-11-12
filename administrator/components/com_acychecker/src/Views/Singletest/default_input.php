<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use AcyCheckerCmsServices\Language;

?>

<div class="cell grid-x grid-margin-x">
	<div class="cell grid-x flex-dir-column">
		<div class="cell acyc__cell__title__container margin-bottom-1">
			<div class="acyc__cell__title"><?php echo Language::translation('ACYC_TEST_EMAIL_ADDRESS'); ?></div>
		</div>
		<div class="cell grid-x grid-margin-x align-center">
			<input type="text"
				   id="acyc__single__test-input"
				   placeholder="address@example.com"
				   class="cell small-9 medium-6 large-4"
				   name="search"
				   value="">
			<button class="cell small-3 medium-2 button acyc__check__singletest">
				<?php echo Language::translation('ACYC_VERIFY'); ?>
			</button>
		</div>
		<div class="cell grid-x acyc__single__test__error">
		</div>
	</div>
</div>
