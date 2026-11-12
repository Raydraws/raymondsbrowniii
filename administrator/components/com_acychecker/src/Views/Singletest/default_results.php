<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use AcyChecker\Services\ApiService;
use AcyCheckerCmsServices\Language;

$result = $this->data['result'];
$email = htmlspecialchars($result[ApiService::API_TEST_KEY_EMAIL]);
$isValid = $result[ApiService::API_TEST_KEY_EMAIL_EXISTS];
?>

<div class="cell grid-x" id="acyc__singletest__results">
	<div class="cell grid-x acyc_content cell-auto margin-bottom-2 text-center">
		<div class="cell grid-x flex-dir-column">
			<div class="acyc__results__title cell">
				<div class="acyc__cell__icon cell margin-top-2 margin-bottom-2">
                    <?php if (in_array($isValid, [ApiService::TRUST_RESULT_VALID, ApiService::TRUST_RESULT_POSSIBLE])) { ?>
						<i class="acycicon-cloud-check acyc__icon--green"></i>
                    <?php } elseif ($isValid === ApiService::TRUST_RESULT_RISKY) { ?>
						<i class="acycicon-shield-warning acyc__icon--orange"></i>
                    <?php } else { ?>
						<i class="acycicon-circle-times acyc__icon--red"></i>
                    <?php } ?>
				</div>
				<div class="acyc__cell__title cell margin-bottom-2">
                    <?php
                    if ($isValid === ApiService::TRUST_RESULT_VALID) {
                        echo Language::translation('ACYC_TEST_RESULT_VALID');
                    }elseif ($isValid === ApiService::TRUST_RESULT_POSSIBLE) {
                        echo Language::translation('ACYC_TEST_RESULT_POSSIBLE');
                    } elseif ($isValid === ApiService::TRUST_RESULT_RISKY) {
                        echo Language::translation('ACYC_TEST_RESULT_RISKY');
                    } else {
                        echo Language::translation('ACYC_TEST_RESULT_INVALID');
                    }
                    ?>
				</div>
			</div>
			<div class="acyc__results__infos cell margin-bottom-1">
				<div class="acyc__cell__subtitle" id="acyc__single__test__desc">
                    <?php
                    if ($isValid === ApiService::TRUST_RESULT_VALID) {
                        echo Language::translationSprintf('ACYC_TEST_RESULT_VALID_DESC', '<strong>'.$email.'</strong>');
                    }elseif ($isValid === ApiService::TRUST_RESULT_POSSIBLE) {
                        echo Language::translationSprintf('ACYC_TEST_RESULT_POSSIBLE_DESC', '<strong>'.$email.'</strong>');
                    } elseif ($isValid === ApiService::TRUST_RESULT_RISKY) {
                        echo Language::translationSprintf('ACYC_TEST_RESULT_RISKY_DESC', '<strong>'.$email.'</strong>');
                    } else {
                        echo Language::translationSprintf('ACYC_TEST_RESULT_INVALID_DESC', '<strong>'.$email.'</strong>');
                    }
                    ?>
				</div>
			</div>
		</div>
	</div>
</div>
