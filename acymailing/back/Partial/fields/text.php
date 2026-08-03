<?php
defined('ABSPATH') || die('Restricted Access');
?>
<div class="cell">
	<input type="text"
	       class="cell"
	       id="<?php echo esc_attr($id); ?>"
	       name="<?php echo esc_attr($name); ?>"
	       v-model="<?php echo esc_attr($vModel); ?>"
        <?php echo isset($option['placeholder']) ? 'placeholder="'.esc_attr($option['placeholder']).'"' : ''; ?>>
</div>
