<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use AcyChecker\Services\TooltipService;
use AcyCheckerCmsServices\Language;
use AcyCheckerCmsServices\Security;

?>
<h2 class="cell acyc__title margin-top-2"><?php echo Language::translation('ACYC_GLOBAL_CONFIGURATION'); ?></h2>
<div class="cell grid-x acyc_vcenter margin-y">
	<label class="cell medium-3 large-2" for="acyc__configuration__license__input"><?php echo Security::escape(Language::translation('ACYC_LICENSE_KEY')); ?></label>
	<div class="cell medium-9 large-10">
		<input type="text"
			   id="acyc__configuration__license__input"
			   class="acyc__configuration__text__input"
			   name="config[license_key]"
			   value="<?php echo Security::escape($this->data['licenseKey']); ?>">
	</div>

	<div class="cell small-3 large-2">
		<a target="_blank"
		   href="<?php echo ACYC_ACYCHECKER_WEBSITE.'my-account/license?utm_source=acychecker_plugin&utm_campaign=get_my_key&utm_medium=button_get_my_key_dashboard'; ?>">
            <?php echo Language::translation('ACYC_GET_MY_KEY'); ?>
		</a>
	</div>
	<div class="cell small-9 large-10 grid-x">
		<div id="acyc__configuration__button_container" class="cell grid-x align-right">
            <?php if (empty($this->data['licenseKey'])) { ?>
				<button class="cell shrink button acyc_button_submit" data-task="attachLicenseKey">
                    <?php echo Language::translation('ACYC_ATTACH_MY_LICENSE'); ?>
				</button>
            <?php } else { ?>
				<button class="cell shrink button acyc_button_submit" data-task="detachLicenseKey">
                    <?php echo Language::translation('ACYC_DETACH_MY_LICENSE'); ?>
				</button>
            <?php } ?>
		</div>
	</div>

	<label class="cell medium-3 large-2 acyc_vcenter" for="acyc__configuration__whitelist__input">
        <?php
        echo TooltipService::tooltip(
            Language::translation('ACYC_WHITELIST_ADDRESSES'),
            Language::translation('ACYC_WHITELIST_ADDRESSES_DESC'),
            'cell medium-shrink'
        );
        ?>
	</label>
	<div class="cell medium-9 large-10">
		<input type="text"
			   id="acyc__configuration__whitelist__input"
			   class="acyc__configuration__text__input"
			   name="config[whitelist]"
			   value="<?php echo Security::escape($this->data['whitelist']); ?>"
			   placeholder="@gmail.com,.co.uk,@[regex]{5}.com$...">
	</div>

	<label class="cell medium-3 large-2 acyc_vcenter" for="acyc__configuration__blacklist__input">
        <?php
        echo TooltipService::tooltip(
            Language::translation('ACYC_BLACKLIST_ADDRESSES'),
            Language::translation('ACYC_BLACKLIST_ADDRESSES_DESC'),
            'cell medium-shrink'
        );
        ?>
	</label>
	<div class="cell medium-9 large-10">
		<input type="text"
			   id="acyc__configuration__blacklist__input"
			   class="acyc__configuration__text__input"
			   name="config[blacklist]"
			   value="<?php echo Security::escape($this->data['blacklist']); ?>"
			   placeholder="@excluded.com,.co.za,@[regex]{3}.com$...">
	</div>

	<label class="cell medium-3 large-2 acyc_vcenter" for="acyc__configuration__blacklist__name__input">
        <?php
        echo TooltipService::tooltip(
            Language::translation('ACYC_BLACKLIST_NAMES'),
            Language::translation('ACYC_BLACKLIST_NAMES_DESC'),
            'cell medium-shrink'
        );
        ?>
	</label>
	<div class="cell medium-9 large-10">
		<input type="text"
			   id="acyc__configuration__blacklist__name__input"
			   class="acyc__configuration__text__input"
			   name="config[blacklist_name]"
			   value="<?php echo Security::escape($this->data['blacklist_name']); ?>"
			   placeholder="bitcoin,whatsapp,http,^[regex]*$...">
	</div>

	<div class="cell">
		<label for="acyc__configuration__blacklist__name__length__input">
            <?php
            echo Language::translationSprintf(
                'ACYC_BLACKLIST_NAME_LENGTH',
                '<input type="number"
					   class="acyc__configuration__intext__input"
					   id="acyc__configuration__blacklist__name__length__input"
					   name="config[blacklist_name_length]"
					   value="'.Security::escape($this->data['blacklist_name_length']).'"
					   placeholder="150"
					   min="0">'
            );
            ?>
		</label>
	</div>
</div>
