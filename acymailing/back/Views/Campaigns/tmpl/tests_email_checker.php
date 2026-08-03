<?php
defined('ABSPATH') || die('Restricted Access');
if (!empty($data['recipients']) && $data['recipients'] > 200) {
    ?>
	<div id="email_checker_ad" class="cell grid-x">
		<h6 class="acym__title acym__title__secondary margin-bottom-0">
            <?php echo esc_html(acym_translation('ACYM_ACYCHECKER_TEST_AD')); ?>
			<img class="acychecker_logo" alt="logo AcyChecker" src="<?php echo esc_url(ACYM_IMAGES.'icons/logo_acychecker.png'); ?>" />
		</h6>
		<div class="margin-bottom-1 grid-x acychecker_ad">
            <?php echo wp_kses(acym_translation('ACYM_ACYCHECKER_TEST_AD_DESC'), ['br' => []]); ?>
			<a class="cell shrink button button-secondary" href="<?php echo esc_url(acym_completeLink('dashboard&task=acychecker')); ?>">
                <?php echo esc_html(acym_translation('ACYM_ACYCHECKER_MORE_INFORMATION')); ?>
			</a>
		</div>
	</div>
    <?php
}
