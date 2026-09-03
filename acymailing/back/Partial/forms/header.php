<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');
?>
<div id="acym_fulldiv_<?php echo esc_attr($form->form_tag_name); ?>" class="acym__subscription__form__header acym__subscription__form-erase full-width">
    <?php
    if ($edition) {
        echo '<form action="#" onsubmit="return false;" id="'.esc_attr($form->form_tag_name).'">';
    } else {
        $cookieExpiration = empty($form->settings['cookie']['cookie_expiration']) ? '1' : $form->settings['cookie']['cookie_expiration'];
        echo '<form
        		acym-data-id="'.esc_attr($form->id).'"
        		acym-data-cookie="'.intval($cookieExpiration).'"
        		action="'.esc_url($form->form_tag_action).'"
        		id="'.esc_attr($form->form_tag_name).'"
        		name="'.esc_attr($form->form_tag_name).'"
        		aria-label="'.esc_attr(acym_translation('ACYM_SUBSCRIPTION')).'"
        		enctype="multipart/form-data"
        		onsubmit="return submitAcymForm(\'subscribe\',\''.esc_attr($form->form_tag_name).'\', \'acymSubmitSubForm\');">';
    }
    include acym_getPartial('forms', 'fields');
    include acym_getPartial('forms', 'button');
    include acym_getPartial('forms', 'hidden_params');

    echo '</form>';
    ?>
</div>
<style>
	<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name); ?>.acym__subscription__form__header{
	<?php echo esc_html(!empty($form->settings['style']['size']['width'])? 'width: '.$form->settings['style']['size']['width'].'%': ''); ?>;
		height: <?php echo esc_html($form->settings['style']['size']['height']); ?>px;
		background-color: <?php echo esc_html($form->settings['style']['background_color']); ?>;
		color: <?php echo esc_html($form->settings['style']['text_color']); ?> !important;
		padding: .5rem;
		z-index: 999999;
		text-align: center;
		display: flex;
		justify-content: center;
		align-items: center;
		margin-left: auto;
		margin-right: auto;
	}

	<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name); ?>.acym__subscription__form__header .responseContainer{
		margin-bottom: 0 !important;
		padding: .4rem !important;
	}

	<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name); ?>.acym__subscription__form__header <?php echo '#'.esc_html($form->form_tag_name); ?>{
		margin: 0;
		display: flex;
		justify-content: center;
		align-items: center
	}

	<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name); ?>.acym__subscription__form__header .acym__subscription__form__fields, <?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name); ?>.acym__subscription__form__header .acym__subscription__form__button{
		display: flex;
		justify-content: center;
		align-items: center
	}

    <?php if (($form->settings['style']['position'] ?? '') !== 'button-right') { ?>
	<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name); ?>.acym__subscription__form__header .acym__subscription__form__button{
		order: -1;
	}
    <?php } ?>

	<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name); ?>
	.acym__users__creation__fields__title{
		margin: 0.5rem
	}

	<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name); ?>.acym__subscription__form__header,
	<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name); ?>.acym__subscription__form__header <?php echo '#'.esc_html($form->form_tag_name); ?>,
	<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name); ?>.acym__subscription__form__header .acym__subscription__form__fields{
		flex-wrap: wrap;
		max-width: 100%;
	}

	<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name); ?>.acym__subscription__form__header{
		height: auto;
		min-height: <?php echo esc_html($form->settings['style']['size']['height']); ?>px;
	}

	<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name); ?>.acym__subscription__form__header .acym__subscription__form__fields .onefield{
		width: auto;
	}

	<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name); ?>.acym__subscription__form__header .acym__subscription__form__fields .onefield input:not([type="radio"]):not([type="checkbox"]):not([type="hidden"]),
	<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name); ?>.acym__subscription__form__header .acym__subscription__form__fields .onefield select,
	<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name); ?>.acym__subscription__form__header .acym__subscription__form__fields .onefield textarea{
		width: auto;
		max-width: 220px;
	}
</style>
<?php if (!$edition) { ?>
	<script type="text/javascript">
        document.body.prepend(document.querySelector('#acym_fulldiv_<?php echo esc_html($form->form_tag_name); ?>'));
	</script>
    <?php include acym_getPartial('forms', 'cookie');
} ?>
