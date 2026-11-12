<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use AcyChecker\Services\ViewService;
use AcyCheckerCmsServices\Language;
use AcyCheckerCmsServices\Security;
use AcyCheckerCmsServices\Url;

?>
<div class="cell grid-x grid-margin-x acyc__test__listing__actions margin-y align-right margin-bottom-0">
	<button type="button"
			id="acyc__test__listing__actions-cancel"
			class="cell small-6 large-shrink button button-secondary"
			data-acyc-redirect="<?php echo Url::completeLink('tests&task=cancelPending'); ?>"
			data-acyc-confirmation="<?php echo Security::escape(Language::translation('ACYC_CONFIRM_CANCEL_TESTS')); ?>">
        <?php echo Language::translation('ACYC_CANCEL_PENDING'); ?>
		<i class="acycicon-circle-times"></i>
	</button>
	<button type="button"
			id="acyc__test__listing__actions-delete"
			class="cell small-6 large-shrink button button-secondary"
			data-acyc-redirect="<?php echo Url::completeLink('tests&task=clearTested'); ?>"
			data-acyc-confirmation="<?php echo Security::escape(Language::translation('ACYC_CONFIRM_DELETE_ALL_TESTS')); ?>">
        <?php echo Language::translation('ACYC_CLEAR_FINISHED'); ?>
		<i class="acycicon-delete"></i>
	</button>

	<div class="cell small-6 large-shrink acyc-button-dropdown">
		<button aria-haspopup="true" aria-expanded="false" class="button button-secondary">
            <?php echo Language::translation('ACYC_EXPORT_GENERAL'); ?>
			<i class="acycicon-download"></i>
		</button>
		<div class="acyc-button-dropdown-menu" role="menu" aria-hidden="true">
			<button role="menuitem"
					type="button"
					id="acyc__test__listing__actions-export"
					class="button button-secondary"
					data-acyc-redirect="<?php echo Url::completeLink('tests&task=doexport&noheader=1'); ?>">
                <?php echo Language::translation('ACYC_EXPORT'); ?>
			</button>
			<button role="menuitem"
					type="button"
					id="acyc__test__listing__actions-exportBlockedUsers"
					class="button button-secondary"
					data-acyc-redirect="<?php echo Url::completeLink('tests&task=doExportBlockedUsers&noheader=1'); ?>">
                <?php echo Language::translation('ACYC_EXPORT_BLOCKED_USERS'); ?>
			</button>
			<button role="menuitem"
					type="button"
					id="acyc__test__listing__actions-exportDeletedUsers"
					class="button button-secondary"
					data-acyc-redirect="<?php echo Url::completeLink('tests&task=doExportDeletedUsers&noheader=1'); ?>">
                <?php echo Language::translation('ACYC_EXPORT_DELETED_USERS'); ?>
			</button>
		</div>
	</div>
    <?php include ViewService::getView('Tests', 'listing_handle_modal'); ?>
</div>
