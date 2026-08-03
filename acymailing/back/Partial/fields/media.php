<?php
defined('ABSPATH') || die('Restricted Access');
?>
<div class="cell">
	<acym-media :value="<?php echo esc_attr($vModel); ?>" :text="imageText" v-on:change="<?php echo esc_attr($vModel); ?> = $event">
</div>
