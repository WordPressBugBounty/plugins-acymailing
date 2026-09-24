<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');
?>

<?php foreach ($data['values'] as $radioKey => $radioText) { ?>
	<label>
		<input
            <?php
            echo ' type="radio"';
            echo ' name="'.esc_attr($data['name']).'"';
            echo ' value="'.esc_attr($radioKey).'"';
            if (!empty($data['data-required'])) {
                echo ' data-required="'.esc_attr($data['data-required']).'"';
            }
            checked($data['value'] == $radioKey);
            ?>
		> <?php echo esc_html($radioText); ?>
	</label>
<?php } ?>
