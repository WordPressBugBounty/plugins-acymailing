<?php
defined('ABSPATH') || die('Restricted Access');
?>
<div class="cell">
	<spectrum :name="'<?php echo esc_attr($name); ?>'" v-model="<?php echo esc_attr($vModel); ?>" :value="'<?php echo esc_attr($value); ?>'">
</div>
