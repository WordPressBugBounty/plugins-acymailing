<?php
defined('ABSPATH') || die('Restricted Access');
?>
<form id="acym_form" action="<?php echo esc_url(acym_completeLink(acym_getVar('cmd', 'ctrl'))); ?>" method="post" name="acyForm">
	<div id="acym__override" class="acym__content cell">
		<div class="grid-x text-center">
			<h1 class="cell acym__listing__empty__subtitle"><?php echo esc_html(acym_translation('ACYM_EMAILS_OVERRIDE_ARE_NOT_INSTALLED')); ?></h1>
			<div class="cell text-center">
				<button data-task="reInstallOverrideEmails" class="button acy_button_submit">
                    <?php echo esc_html(acym_translation('ACYM_INSTALL_OVERRIDE')); ?>
				</button>
			</div>
		</div>
	</div>
    <?php acym_formOptions(); ?>
</form>
