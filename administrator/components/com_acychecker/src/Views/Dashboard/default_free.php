<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use AcyCheckerCmsServices\Language;

if (in_array($this->config->get('license_level', ''), ['', 'AcyChecker-Starter']) && $this->data['totalDisposableEmails'] > 0) {
    $acyText = '';
    if (!empty($this->data['acyUsers'])) {
        $acyText = Language::translationSprintf(
            'ACYC_AND_X_Y_USERS',
            '<b>'.$this->data['disposableAcyEmails'].'</b>',
            'acymailing'
        );
    }
    $linkGetLicense = ACYC_ACYCHECKER_WEBSITE.'pricing?utm_source=acychecker_plugin&utm_campaign=get_license&utm_medium=button_get_license';
    ?>
	<div class="cell">
        <?php echo Language::translationSprintf(
            'ACYC_BASED_ON_SAMPLE_POTENTIAL_X_FAKE_EMAILS',
            '<b>'.$this->data['disposableCmsEmails'].'</b>',
            ACYC_CMS_TITLE,
            $acyText
        ); ?>
	</div>
	<div class="cell margin-bottom-1">
        <?php echo Language::translationSprintf(
            'ACYC_BASED_ON_TEST_RECOMMENDATION',
            $this->data['suggestedPlan']
        ); ?>
	</div>
	<div class="cell grid-x align-center margin-y">
		<a class="cell shrink button" href="<?php echo $linkGetLicense; ?>" target="_blank">
            <?php echo Language::translation('ACYC_GET_A_LICENSE'); ?>
		</a>
	</div>
    <?php
}
