<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use AcyChecker\Controllers\SingletestController;
use AcyChecker\Services\ApiService;
use AcyCheckerCmsServices\Language;

$tests = $this->data['tests'];
$colorMap = $this->data['colorMap'];
$isValid = $this->data['isValid'];
?>

<div class="cell grid-x" id="acyc__singletest__details">
	<div class="cell grid-x acyc_content cell-auto margin-bottom-2">
		<div class="cell grid-x flex-dir-column">
			<div class="cell grid-x flex-dir-column">
				<div class="cell acyc__cell__title__container align-left margin-bottom-1">
					<div class="acyc__cell__title"><?php echo Language::translation('ACYC_DETAILED_INFORMATION'); ?></div>
				</div>

				<div class="cell text-center margin-bottom-1">
                    <?php if ($isValid === ApiService::TRUST_RESULT_NOT_EXISTING) { ?>
						<div class="align-center margin-bottom-1 acyc__status__banner acyc__bg--red-light">
							<span class="acyc__status__badge acyc__bg--red acyc__color--red"><?php echo Language::translation('ACYC_NOT_EXISTING'); ?></span>
							<span class="acyc__status__text acyc__color--red"><?php echo Language::translation('ACYC_TEST_DETAILS_INVALID_TEXT'); ?></span>
						</div>
                    <?php } elseif ($isValid === ApiService::TRUST_RESULT_RISKY) { ?>
						<div class="align-center margin-bottom-1 acyc__status__banner acyc__bg--orange-light">
							<span class="acyc__status__badge acyc__bg--orange acyc__color--orange"><?php echo Language::translation('ACYC_RISKY'); ?></span>
							<span class="acyc__status__text acyc__color--orange"><?php echo Language::translation('ACYC_TEST_DETAILS_RISKY_TEXT'); ?></span>
						</div>
                    <?php } else { ?>
						<div class="align-center margin-bottom-1 acyc__status__banner"></div>
                    <?php } ?>
				</div>

                <?php foreach ($tests as $testKey => $testData) {
                    $color = $colorMap[$testData['status']];
                    ?>
					<div class="grid-x align-middle margin-left-2 acyc__test__result__container margin-bottom-1">
						<div class="cell acyc__test__result__badge acyc__bg--<?php echo $color; ?> acyc__color--<?php echo $color; ?>">
                            <?php switch ($testData['status']) {
                                case SingletestController::STATUS_SUCCESS:
                                    echo ucfirst(Language::translation('ACYC_SUCCESS'));
                                    break;
								case SingletestController::STATUS_INVALID:
                                    echo ucfirst(Language::translation('ACYC_INVALID'));
                                    break;
								case SingletestController::STATUS_WARNING:
									echo ucfirst(Language::translation('ACYC_WARNING'));
									break;
								case SingletestController::STATUS_INFO:
                                    echo ucfirst(Language::translation('ACYC_INFO'));
                                    break;
								case SingletestController::STATUS_NOT_TESTED:
                                    echo ucfirst(Language::translation('ACYC_NOT_TESTED'));
                                    break;
                            }
                            ?>
						</div>
						<div class="cell auto">
							<div class="acyc__test__title"><?php echo $testData['label']; ?></div>
							<div class="acyc__test__description"><?php echo $testData['description']; ?></div>
						</div>
					</div>
                <?php } ?>
			</div>
		</div>
	</div>
</div>
