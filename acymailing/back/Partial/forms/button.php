<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');
?>
<div class="acym__subscription__form__button">
    <?php
    if (empty($form->settings['button']['text'])) {
        $form->settings['button']['text'] = 'ACYM_SUBSCRIBE';
    }

    if (acym_isMultilingual()) {
        $defaultLanguage = $this->config->get('multilingual_default');
        $currentLanguageTag = acym_getLanguageTag();

        if (!empty($form->settings['button']['lang'][$currentLanguageTag])) {
            $form->settings['button']['text'] = $form->settings['button']['lang'][$currentLanguageTag];
        } elseif (!empty($form->settings['button']['lang'][$defaultLanguage])) {
            $form->settings['button']['text'] = $form->settings['button']['lang'][$defaultLanguage];
        }
    }
    ?>
	<button type="submit">
        <?php echo esc_html(acym_translation($form->settings['button']['text'])); ?>
	</button>
	<style>
		<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name).' '; ?>.acym__subscription__form__button{
			display: flex;
			justify-content: center;
			align-items: center
		}

		<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name).' '; ?>.acym__subscription__form__button button{
			background-color: <?php echo esc_html($form->settings['button']['background_color']); ?>;
			color: <?php echo esc_html($form->settings['button']['text_color']); ?>;
			border-width: <?php echo esc_html($form->settings['button']['border_size']); ?>px;
			border-style: <?php echo esc_html($form->settings['button']['border_type']); ?>;
			border-color: <?php echo esc_html($form->settings['button']['border_color']); ?>;
			border-radius: <?php echo esc_html($form->settings['button']['border_radius']); ?>px;
			padding: <?php echo esc_html($form->settings['button']['size']['height']); ?>px <?php echo esc_html($form->settings['button']['size']['width']); ?>px;
		}
	</style>
</div>
