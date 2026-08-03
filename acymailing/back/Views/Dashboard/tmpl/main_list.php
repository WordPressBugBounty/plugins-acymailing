<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');
?>
<div class="acym__content cell">
	<div class="cell acym__title acym__dashboard__title"><?php echo esc_html(acym_translation('ACYM_MAIN_LISTS')); ?></div>
	<span class="separator"></span>
	<div class="grid-x acym__listing cell">
		<div class="grid-x cell acym__listing__header">
			<div class="grid-x medium-auto small-12">
				<div class="cell small-6 acym__listing__header__title">
                    <?php echo esc_html(acym_translation('ACYM_NAME_SUMMARY')); ?>
				</div>
				<div class="cell small-3 acym__listing__header__title">
                    <?php echo esc_html(acym_translation('ACYM_ACTION_SUBSCRIBED')); ?>
				</div>
				<div class="cell small-3 acym__listing__header__title">
                    <?php echo esc_html(acym_translation('ACYM_ACTION_UNSUBSCRIBED')); ?>
				</div>
			</div>
		</div>

        <?php
        for ($i = 1; $i <= 5; $i++) { ?>
			<div class="grid-x cell align-middle acym__listing__row main-list-row-<?php echo esc_attr($i); ?>">
				<div class="cell small-6">
					<div class="skeleton <?php echo 'name_'.esc_attr($i); ?>"></div>
				</div>
				<div class="cell small-3 large">
					<span class="skeleton <?php echo 'sub_'.esc_attr($i); ?>"></span>
					<span class="<?php echo 'new_sub_'.esc_attr($i); ?> acym__color__green"></span>
				</div>
				<div class="cell small-3 ">
					<span class="skeleton <?php echo 'unsub_'.esc_attr($i); ?>"></span>
					<span class="<?php echo 'new_unsub_'.esc_attr($i); ?> acym__color__red"></span>
				</div>
			</div>
        <?php } ?>
	</div>
</div>

