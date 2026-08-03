<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');
?>

<input type="hidden" name="<?php echo esc_attr($data['name']); ?>" value="">
<?php foreach ($data['values'] as $checkboxKey => $checkboxText) { ?>
    <?php if (!empty($data['displayFront'])) { ?>
		<label>
			<input
                <?php
                echo ' type="checkbox"';
                echo ' name="'.esc_attr($data['name'].'['.$checkboxKey.']').'"';
                echo ' value="'.esc_attr($checkboxKey).'"';
                if (!empty($data['data-required'])) {
                    echo ' data-required="'.esc_attr($data['data-required']).'"';
                }
                acym_checked(in_array($checkboxKey, $data['value']));
                ?>
			> <?php echo esc_html($checkboxText); ?>
		</label>
    <?php } else { ?>
		<label<?php echo in_array($checkboxKey, $data['value']) ? '' : ' class="cell margin-top-1"'; ?>>
			<input
                <?php
                echo ' type="checkbox"';
                echo ' name="'.esc_attr($data['name'].'['.$checkboxKey.']').'"';
                echo ' class="acym__users__creation__fields__checkbox"';
                if (in_array($checkboxKey, $data['value'])) {
                    echo ' checked="checked"';
                    if (!empty($data['data-required'])) {
                        echo ' data-required="'.esc_attr($data['data-required']).'"';
                    }
                }
                ?>
			><?php echo esc_html($checkboxText); ?>
		</label>
    <?php } ?>
<?php } ?>
