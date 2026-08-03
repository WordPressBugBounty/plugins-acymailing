<?php
defined('ABSPATH') || die('Restricted Access');
?>
<button type="button"
        id="acym__dtext__button"
        class="<?php echo esc_attr($data['class']); ?>"
        data-acym-editor="<?php echo esc_attr($data['editor']); ?>"
        data-acym-selection="<?php echo esc_attr($data['selection']); ?>">
    <?php
    if (!empty($data['icon'])) {
        echo '<i class="'.esc_attr($data['icon']).'"></i>';
    }
    ?>
    <?php echo esc_html($data['text']); ?>
</button>
