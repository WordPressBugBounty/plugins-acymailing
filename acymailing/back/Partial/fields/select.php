<?php
defined('ABSPATH') || die('Restricted Access');
?>
<div class="cell">
	<select2 :name="'<?php echo esc_attr($name); ?>'"
	         :value="'<?php echo esc_attr($value); ?>'"
	         :options="<?php echo esc_attr(json_encode($option['options'])); ?>"
	         v-model="<?php echo esc_attr($vModel); ?>"></select2>
</div>
