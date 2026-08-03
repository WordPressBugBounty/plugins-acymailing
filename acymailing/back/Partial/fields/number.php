<?php
defined('ABSPATH') || die('Restricted Access');
?>

<div class="cell grid-x acym_vcenter">
	<input type="number"
        <?php echo isset($option['min']) ? 'min="'.intval($option['min']).'"' : ''; ?>
        <?php echo isset($option['max']) ? 'max="'.intval($option['max']).'"' : ''; ?>
		   class="cell medium-3 margin-next-1"
		   v-model="<?php echo esc_attr($vModel); ?>"
		   id="<?php echo esc_attr($id); ?>"
		   name="<?php echo esc_attr($name); ?>">
    <?php
    if (!empty($option['unit'])) {
        echo '<span class="cell shrink">'.esc_html($option['unit']).'</span>';
    }
    ?>
</div>
