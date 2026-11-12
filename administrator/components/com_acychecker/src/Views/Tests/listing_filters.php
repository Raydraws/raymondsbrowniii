<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use AcyChecker\Services\FormService;
use AcyChecker\Services\StatusService;
use AcyCheckerCmsServices\Language;
use AcyCheckerCmsServices\Security;

?>
<div class="cell grid-x grid-margin-x">
	<input type="text"
		   id="acyc__test__listing__actions__search-input"
		   placeholder="<?php echo Language::translation('ACYC_SEARCH'); ?>"
		   class="cell small-8 large-4"
		   name="search"
		   value="<?php echo Security::escape($this->data['search']); ?>">
	<button class="cell small-4 medium-shrink button" id="acyc__test__listing__actions__search-button">
        <?php echo Language::translation('ACYC_SEARCH'); ?>
	</button>
</div>
<div class="cell grid-x margin-top-1 margin-bottom-0 margin-y">
	<div class="cell medium-shrink grid-x acyc__listing__actions">
        <?php
        $actions = [
            'deleteResults' => Language::translation('ACYC_DELETE_RESULTS'),
            'blockUsers' => Language::translation('ACYC_BLOCK_USERS'),
            'unblockUsers' => Language::translation('ACYC_UNBLOCK_USERS'),
            'deleteUsers' => Language::translation('ACYC_DELETE_USERS'),
        ];
        echo FormService::listingActions($actions);
        ?>
	</div>
	<div class="cell medium-auto acyc_vcenter">
        <?php echo StatusService::initStatusListing($this->data['status'], empty($this->data['current_status']) ? 'all' : $this->data['current_status']); ?>
	</div>
	<div class="cell medium-auto acyc_listing_sort-by">
        <?php echo FormService::sortBy(
            [
                'email' => Language::translation('ACYC_EMAIL'),
                'block_reason' => Language::translation('ACYC_USER_STATUS'),
                'date' => Language::translation('ACYC_DATE'),
                'test_result' => Language::translation('ACYC_TRUSTWORTHINESS'),
                'domain_exists' => Language::translation('ACYC_DOMAIN_EXISTS'),
                'disposable' => Language::translation('ACYC_DISPOSABLE'),
                'accept_all' => Language::translation('ACYC_ACCEPT_ALL'),
                'role_email' => Language::translation('ACYC_ROLE_EMAIL'),
                'free' => Language::translation('ACYC_FREE'),
                'current_step' => Language::translation('ACYC_CURRENT_STEP'),
            ],
            'tests',
            $this->data['ordering'],
            'asc'
        ); ?>
	</div>
</div>
