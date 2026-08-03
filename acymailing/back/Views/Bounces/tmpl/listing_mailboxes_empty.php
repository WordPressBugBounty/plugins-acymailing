<?php
defined('ABSPATH') || die('Restricted Access');
?>
<div class="grid-x text-center">
	<h1 class="acym__listing__empty__title cell"><?php echo esc_html(acym_translation('ACYM_YOU_DONT_HAVE_ANY_MAILBOX_ACTION')); ?></h1>
	<div class="medium-4"></div>
	<div class="medium-4 cell">
		<button data-task="mailboxAction" type="button" class="button acy_button_submit margin-auto">
            <?php echo esc_html(acym_translation('ACYM_CREATE_NEW_MAILBOX_ACTION')); ?>
		</button>
	</div>
	<div class="medium-4"></div>
</div>
