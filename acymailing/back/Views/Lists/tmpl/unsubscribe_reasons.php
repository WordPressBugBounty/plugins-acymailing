<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');
?>
	<input type="hidden" name="list[name]" value="<?php echo esc_attr($data['listInformation']->name); ?>" />
<?php if (empty($data['unsubReasons'])) { ?>
	<h5 class="cell acym__title acym__title__secondary"><?php echo esc_html(acym_translation('ACYM_UNSUBSCRIBE_REASONS')); ?></h5>
	<p class="cell text-center"><?php echo esc_html(acym_translation('ACYM_NO_UNSUBSCRIBE_REASON')); ?></p>
<?php } else {
    ?>
	<h5 class="cell acym__title acym__title__secondary"><?php echo esc_html(acym_translation('ACYM_UNSUBSCRIBE_REASONS')); ?></h5>
	<div class="cell grid-x acym__listing">
		<div class="grid-x cell acym__listing__header">
			<div class="medium-auto small-11 cell acym__listing__header__title">
                <?php echo esc_html(acym_translation('ACYM_UNSUBSCRIBE_REASONS')); ?>
			</div>
			<div class="medium-auto small-11 cell acym__listing__header__title">
                <?php echo esc_html(acym_translation('ACYM_QUANTITY')); ?>
			</div>
		</div>
        <?php foreach ($data['unsubReasons'] as $reason => $count) { ?>
			<div class="grid-x cell align-middle acym__listing__row">
				<div class="grid-x medium-auto small-11 cell align-middle acym__campaign__listing acym__listing__title__container">
					<div class="cell medium-auto small-7 acym__listing__title acym__campaign__title">
						<h6 class="acym__listing__title__primary acym_text_ellipsis">
                            <?php
                            if (is_numeric($reason)) {
                                $index = $reason - 1;
                                $reason = $data['unsubReasons'][$index] ?? $reason;
                            }
                            echo esc_html($reason); ?>
						</h6>
					</div>
					<div class="cell medium-auto small-7 acym__listing__title acym__campaign__title">
						<h6 class="acym__listing__title__primary acym_text_ellipsis">
                            <?php echo esc_html($count); ?>
						</h6>
					</div>
				</div>
			</div>
        <?php } ?>
	</div>
<?php }
