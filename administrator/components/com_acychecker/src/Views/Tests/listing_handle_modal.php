<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use AcyChecker\Services\ModalService;
use AcyChecker\Services\ViewService;
use AcyCheckerCmsServices\Language;
use AcyCheckerCmsServices\Security;

ob_start();
?>
	<div class="cell grid-x align-center">
		<div class="cell grid-x large-10">
			<h2 class="cell text-center"><?php echo Language::translation('ACYC_HANDLE_RESULTS'); ?></h2>
			<h2 class="cell acyc__title"><?php echo Language::translation('ACYC_WHO'); ?></h2>
			<div class="cell grid-x">
				<p class="cell medium-3"><?php echo Language::translation('ACYC_WHICH_TABLE_USERS_CLEAN'); ?></p>
                <?php foreach ($this->data['tables_select'] as $tableInfo) { ?>
					<div class="cell shrink margin-right-1">
						<label>
							<input type="checkbox" <?php echo in_array($tableInfo['value'], $this->data['current_config']['tables_selected']) ? 'checked' : ''; ?>
								   value="<?php echo $tableInfo['value']; ?>"
								   name="acyc_config[tables_selected][<?php echo Security::escape($tableInfo['value']); ?>]">
							<span><?php echo $tableInfo['text']; ?></span>
						</label>
					</div>
                <?php } ?>
			</div>
            <?php
            include ViewService::getView('Database', 'default_action');
            include ViewService::getView('Database', 'default_conditions');
            ?>
			<div class="cell margin-top-2" id="acyc__tests__handle__selected_users"></div>
			<div class="cell grid-x align-center margin-top-1">
				<button
						class="cell shrink button"
						id="acyc__tests__handle__process"
						type="button">
                    <?php echo Language::translation('ACYC_PROCESS'); ?>
				</button>
			</div>
			<div id="acyc__tests__handle__progressbar" class="cell grid-x align-center margin-top-1 is-hidden">
				<div class="cell medium-9 large-6 progress" role="progressbar" aria-valuemin="0" aria-valuemax="100">
					<div class="progress-meter" style="width: 0"></div>
					<div class="progress-counter">0%</div>
				</div>
			</div>
			<div id="acyc__tests__handle__message" class="cell grid-x align-center margin-top-1 is-hidden">
				<i class="acycicon-close acyc_red"></i>
				<i class="acycicon-check acyc_green"></i>
				<div id="acyc__tests__handle__message__message"></div>
			</div>
		</div>
	</div>
<?php
$modalContent = ob_get_clean();

echo ModalService::modal(
    Language::translation('ACYC_HANDLE_RESULTS').' <i class="acycicon-lightning"></i>',
    $modalContent,
    'acycmodal_handle_modal',
    '',
    [
        'class' => 'cell small-6 large-shrink button button-secondary',
    ]
);
