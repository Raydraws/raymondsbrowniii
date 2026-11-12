<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use AcyChecker\Services\FormService;
use AcyChecker\Services\TooltipService;
use AcyCheckerCmsServices\Language;
use AcyCheckerCmsServices\Security;

?>
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
<?php foreach ($this->data['tables_filters'] as $nameKey => $tableFilter) { ?>
	<div class="cell grid-x" data-acyc-table="<?php echo Security::escape($nameKey); ?>">
		<label for="<?php echo Security::escape('table_filter_'.$nameKey); ?>" class="cell medium-3">
            <?php
            echo Language::translationSprintf(
                'ACYC_X_ADDITIONAL_FILTER',
                $tableFilter['text']
            );
            echo TooltipService::info(Language::translation('ACYC_IF_NO_FILTER_SELECTED_ALL_TAKEN'));
            ?>
		</label>
		<div class="cell large-3">
            <?php
            $selected = [];
            if (!empty($this->data['current_config']['table_filter_'.$nameKey])) {
                $selected = $this->data['current_config']['table_filter_'.$nameKey];
            }
            echo FormService::selectMultiple(
                $tableFilter['values'],
                'acyc_config[table_filter_'.$nameKey.']',
                $selected,
                [
                    'id' => 'table_filter_'.$nameKey,
                    'class' => 'acyc__select',
                ]
            );
            ?>
		</div>
	</div>
<?php } ?>
