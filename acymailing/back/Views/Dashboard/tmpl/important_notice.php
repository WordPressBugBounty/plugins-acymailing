<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');

use AcyMailing\Helpers\SecurityHelper;

if (!empty($data['dashboardNotifications'])) { ?>
	<div class="acym__content cell margin-bottom-1" id="acym__dashboard__notifications">
		<div class="cell acym__title acym__dashboard__title"><?php echo esc_html(acym_translation('ACYM_IMPORTANT_NOTICE')); ?></div>
		<span class="separator"></span>
		<div class="acym__dashboard__notifications">
            <?php foreach ($data['dashboardNotifications'] as $notification) { ?>
				<div class="cell grid-x acym__dashboard__notification acym__dashboard__notification__<?php echo esc_attr($notification['level']); ?>">
					<div class="cell shrink small-1 align-center grid-x acym__dashboard__notification__icon">
						<i class="cell shrink <?php echo esc_attr(
                            $notification['level'] === 'warning' ? 'acymicon-exclamation-triangle' : 'acymicon-exclamation-circle'
                        ); ?>"></i>
					</div>
					<div class="cell grid-x small-10">
						<p class="cell acym__dashboard__notification__message">
                            <?php echo wp_kses($notification['message'], SecurityHelper::ALLOWED_HTML_NOTIFICATION); ?>
						</p>
						<div class="cell acym__dashboard__notification__date">
                            <?php echo esc_attr($notification['date']); ?>
						</div>
					</div>
                    <?php if ($notification['removable']) { ?>
						<i class="cell shrink small-1 acym__dashboard__notification__delete acymicon-close" data-id="<?php echo esc_attr($notification['name']); ?>"></i>
                    <?php } ?>
				</div>
				<span class="acym__dashboard__light__separator separator"></span>
            <?php } ?>
		</div>
	</div>
<?php } ?>
