<?php
defined('ABSPATH') || die('Restricted Access');
?>
<div class="cell grid-x grid-margin-y align-center acym__gopro__head">
	<div class="cell large-6 padding-3 align-center-middle gopro_header grid-x align-center align-middle">
		<h3 class="acym__title margin-bottom-2 text-center">
            <?php echo esc_html(acym_translation('ACYM_GO_PRO_NOW')); ?>
		</h3>
		<a class="cell medium-6 large-shrink button acym__button__upgrade text-center" target="_blank" href="<?php echo esc_url($pricingPage); ?>">
            <?php echo esc_html(acym_translation('ACYM_UNLOCK_UNLIMITED')); ?>
		</a>
	</div>
</div>
