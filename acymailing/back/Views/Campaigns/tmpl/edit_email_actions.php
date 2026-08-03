<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');
?>
<div class="cell grid-x text-center acym__campaign__email__save-button">
	<div class="cell medium-shrink medium-margin-bottom-0 margin-bottom-1 text-left">
        <?php
        $campaignType = $data['mailInformation']->sending_type ?? $data['campaign_type'];
        acym_backToListing(
            in_array($campaignType, ['birthday', 'woocommerce_cart'])
                ? 'campaigns&task=specificListing&type='.$campaignType : null
        );
        ?>
	</div>
	<div class="cell medium-auto grid-x text-right">
		<div class="cell medium-auto"></div>
        <?php if (!empty($data['campaignID'])) { ?>
			<button data-before-action="<?php echo esc_attr($data['before-save']); ?>"
			        data-task="save"
			        data-step="listing"
			        type="submit"
			        class="cell button-secondary medium-shrink button medium-margin-bottom-0 margin-next-1 acy_button_submit">
                <?php echo esc_html(acym_translation('ACYM_SAVE_EXIT')); ?>
			</button>
        <?php } ?>
		<button data-before-action="<?php echo esc_attr($data['before-save']); ?>"
		        data-task="save"
		        data-step="recipients"
		        type="submit"
		        class="cell medium-shrink button margin-bottom-0 acy_button_submit">
            <?php echo esc_html(acym_translation('ACYM_SAVE_CONTINUE')); ?>
			<i class="acymicon-chevron-right"></i>
		</button>
	</div>
</div>
