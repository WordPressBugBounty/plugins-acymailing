<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');
?>
<button class="acym_vcenter align-center acy_button_submit cell medium-6 large-shrink button <?php echo $data['isPrimary'] ? '' : 'button-secondary'; ?>"
    <?php
    foreach ($data['attributes'] as $oneAttribute => $oneValue) {
        if (is_array($oneValue) || is_object($oneValue)) {
            $oneValue = json_encode($oneValue);
        } elseif ($oneValue === true) {
            $oneValue = $oneAttribute;
        }

        echo ' '.esc_attr($oneAttribute).'="'.esc_attr($oneValue).'"';
    }
    ?>
>
    <?php
    if (!empty($data['icon'])) {
        echo '<i class="acymicon-'.esc_attr($data['icon']).'"></i>';
    }

    echo ' '.wp_kses(
            $data['content'],
            [
                'span' => [
                    'id' => true,
                    'data-default' => true,
                ],
            ]
        );
    ?>
</button>
