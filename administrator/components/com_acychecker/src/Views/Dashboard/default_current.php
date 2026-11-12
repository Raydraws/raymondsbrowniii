<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use AcyChecker\Services\ViewService;
use AcyCheckerCmsServices\Language;
use AcyCheckerCmsServices\Url;

?>
<div class="cell grid-x acyc_content">
	<div class="cell"><?php echo Language::translation('ACYC_YOU_CURRENTLY_HAVE'); ?></div>
	<ul class="cell">
		<li>
            <?php echo Language::translationSprintf(
                'ACYC_X_Y_USERS_AVERAGE_Z_PER_MONTH',
                '<b>'.$this->data['cmsUsers'].'</b>',
                ACYC_CMS_TITLE,
                $this->data['cmsUsersEvolution']
            ); ?>
		</li>
        <?php if (!empty($this->data['acyUsers'])) { ?>
			<li>
                <?php echo Language::translationSprintf(
                    'ACYC_X_Y_USERS_AVERAGE_Z_PER_MONTH',
                    '<b>'.$this->data['acyUsers'].'</b>',
                    'AcyMailing',
                    $this->data['acyUsersEvolution']
                ); ?>
			</li>
        <?php } ?>
        <?php if (!empty($this->data['acy5Users'])) { ?>
			<li>
                <?php echo Language::translationSprintf(
                    'ACYC_X_Y_USERS_AVERAGE_Z_PER_MONTH',
                    '<b>'.$this->data['acy5Users'].'</b>',
                    'AcyMailing 5',
                    $this->data['acy5UsersEvolution']
                ); ?>
			</li>
        <?php } ?>
	</ul>
    <?php include ViewService::getView('Dashboard', 'default_free'); ?>
    <?php if (!empty($this->config->get('license_level')) && $this->config->get('license_level') !== 'AcyChecker-Starter') { ?>
		<div class="cell medium-6 text-center margin-y">
			<a class="button button-secondary" href="<?php echo Url::completeLink('database'); ?>">
                <?php echo Language::translation('ACYC_CLEAN_DATABASE'); ?>
			</a>
		</div>
		<div class="cell medium-6 text-center">
			<a class="button button-secondary" href="<?php echo Url::completeLink('registration'); ?>">
                <?php echo Language::translation('ACYC_BLOCK_FAKE_USERS'); ?>
			</a>
		</div>
    <?php } ?>
</div>
