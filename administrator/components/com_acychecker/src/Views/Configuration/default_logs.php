<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use AcyChecker\Services\ModalService;
use AcyCheckerCmsServices\Language;
use AcyCheckerCmsServices\Security;
use AcyCheckerCmsServices\Url;

?>
<h2 class="cell acyc__title margin-top-2"><?php echo Language::translation('ACYC_LOGS'); ?></h2>
<div class="cell grid-x margin-bottom-1">
	<label class="margin-right-1 medium-3"><?php echo Security::escape(Language::translation('ACYC_REPORT_BATCH')); ?></label>
    <?php
    echo ModalService::modal(
        Language::translation('ACYC_SEE_LOGS'),
        '',
        null,
        '',
        [
            'class' => 'button',
            'data-ajax' => 'true',
            'data-iframe' => '&ctrl=configuration&task=seeLogs&type=batch',
        ]
    );
    echo '<a href="'.Url::completeLink('configuration&task=deleteLogs&type=batch').'" class="margin-left-1 button">'.Language::translation('ACYC_DELETE_LOGS').'</a>';
    ?>
</div>
<div class="cell grid-x margin-bottom-1">
	<label class="margin-right-1 medium-3"><?php echo Security::escape(Language::translation('ACYC_REPORT_CALLBACK')); ?></label>
    <?php
    echo ModalService::modal(
        Language::translation('ACYC_SEE_LOGS'),
        '',
        null,
        '',
        [
            'class' => 'button',
            'data-ajax' => 'true',
            'data-iframe' => '&ctrl=configuration&task=seeLogs&type=callback',
        ]
    );
    echo '<a href="'.Url::completeLink('configuration&task=deleteLogs&type=callback').'" class="margin-left-1 button">'.Language::translation('ACYC_DELETE_LOGS').'</a>';
    ?>
</div>
<div class="cell grid-x margin-bottom-1">
	<label class="margin-right-1 medium-3"><?php echo Security::escape(Language::translation('ACYC_REPORT_INDIVIDUAL')); ?></label>
    <?php
    echo ModalService::modal(
        Language::translation('ACYC_SEE_LOGS'),
        '',
        null,
        '',
        [
            'class' => 'button',
            'data-ajax' => 'true',
            'data-iframe' => '&ctrl=configuration&task=seeLogs&type=individual',
        ]
    );
    echo '<a href="'.Url::completeLink('configuration&task=deleteLogs&type=individual').'" class="margin-left-1 button">'.Language::translation('ACYC_DELETE_LOGS').'</a>';
    ?>
</div>
<div class="cell grid-x">
	<label class="margin-right-1 medium-3"><?php echo Security::escape(Language::translation('ACYC_REPORT_SINGLE_TEST')); ?></label>
    <?php
    echo ModalService::modal(
        Language::translation('ACYC_SEE_LOGS'),
        '',
        null,
        '',
        [
            'class' => 'button',
            'data-ajax' => 'true',
            'data-iframe' => '&ctrl=configuration&task=seeLogs&type=single',
        ]
    );
    echo '<a href="'.Url::completeLink('configuration&task=deleteLogs&type=single').'" class="margin-left-1 button">'.Language::translation('ACYC_DELETE_LOGS').'</a>';
    ?>
</div>
