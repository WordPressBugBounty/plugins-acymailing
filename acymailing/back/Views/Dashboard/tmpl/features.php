<?php
defined('ABSPATH') || die('Restricted Access');
?>
<div id="acym__dashboard__splashscreen" class="cell grid-x acym__content">
	<form id="acym_form" action="<?php echo esc_url(acym_completeLink(acym_getVar('cmd', 'ctrl'))); ?>" method="post" name="acyForm" class="cell grid-x">
        <?php include ACYM_NEW_FEATURES_SPLASHSCREEN; ?>
        <?php acym_formOptions(); ?>
	</form>
</div>
