<?php
defined('ABSPATH') || die('Restricted Access');
if (!empty($form->settings['image']['url'])) { ?>
	<div class="acym__subscription__form__image">
		<img src="<?php echo esc_url($form->settings['image']['url']); ?>"
		     alt=""
		     width="<?php echo esc_attr($form->settings['image']['size']['width']); ?>"
		     height="<?php echo esc_attr($form->settings['image']['size']['height']); ?>">
	</div>
	<style>
		<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name).' '; ?>.acym__subscription__form__image{
			text-align: center;
		}

		<?php echo '#acym_fulldiv_'.esc_html($form->form_tag_name).' '; ?>.acym__subscription__form__image img{
			display: inline-block;
			width: <?php echo esc_html($form->settings['image']['size']['width']); ?>px;
			height: <?php echo esc_html($form->settings['image']['size']['height']); ?>px;
			margin: 0 1rem;
		}
	</style>
<?php } ?>
