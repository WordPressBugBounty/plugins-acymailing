<?php
defined('ABSPATH') || die('Restricted Access');
?>

<input
    <?php
    echo ' type="'.esc_attr($data['type']).'"';
    echo ' name="'.esc_attr($data['name']).'"';
    if (!empty($data['id'])) {
        echo ' id="'.esc_attr($data['id']).'"';
    }
    if (!empty($data['autocomplete'])) {
        echo ' autocomplete="'.esc_attr($data['autocomplete']).'"';
    }
    if (!empty($data['class'])) {
        echo ' class="'.esc_attr($data['class']).'"';
    }
    if (!empty($data['placeholder'])) {
        echo ' placeholder="'.esc_attr($data['placeholder']).'" aria-label="'.esc_attr($data['placeholder']).'"';
    }
    if (!empty($data['data-required'])) {
        echo ' data-required="'.esc_attr($data['data-required']).'"';
    }
    if (isset($data['value'])) {
        echo ' value="'.esc_attr($data['value']).'"';
    }
    if (isset($data['authorizedContent'])) {
        echo ' data-authorized-content="'.esc_attr($data['authorizedContent']).'"';
    }
    if (isset($data['style'])) {
        echo ' style="'.esc_attr($data['style']).'"';
    }
    if (isset($data['maxCharacters'])) {
        echo ' maxlength="'.esc_attr($data['maxCharacters']).'"';
    }
    if (!empty($data['required'])) {
        echo ' required';
    }
    if (!empty($data['readonly'])) {
        echo ' readonly="readonly"';
    }
    acym_disabled($data['disabled'] ?? false);
    ?>
>
