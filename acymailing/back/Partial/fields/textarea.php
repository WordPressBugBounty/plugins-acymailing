<?php
defined('ABSPATH') || die('Restricted Access');
?>
<div class="cell">
	<textarea class="cell"
	          name="<?php echo esc_attr($name); ?>"
	          v-model="<?php echo esc_attr($vModel); ?>">
		<?php echo esc_html($value); ?>
	</textarea>
</div>
