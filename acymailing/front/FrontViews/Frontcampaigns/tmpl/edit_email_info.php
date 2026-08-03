<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');
?>
<div class="cell grid-x grid-margin-x margin-y">
	<div class="cell large-6">
		<label>
            <?php echo esc_html(acym_translation('ACYM_CAMPAIGN_NAME')); ?>
			<input name="mail[name]" type="text" value="<?php echo esc_attr($data['mailInformation']->name); ?>">
		</label>
	</div>

    <?php
    if (empty($data['multilingual']) && empty($data['abtest'])) {
        $preheaderSize = '';
        include acym_getView('campaigns', 'edit_email_info_content', true);
    }
    ?>
</div>
