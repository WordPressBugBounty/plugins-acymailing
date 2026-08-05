<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');

use AcyMailing\Classes\UserClass;

if (($form->settings['termspolicy']['tracking_consent'] ?? 'no') !== 'yes') {
    return;
}

$trackingChecked = false;
$currentEmail = acym_currentUserEmail();
if (!empty($currentEmail)) {
    $existingUser = (new UserClass())->getOneByEmail($currentEmail);
    if (!empty($existingUser->id)) {
        $trackingChecked = (int)$existingUser->tracking === 1;
    }
}

echo '<div class="onefield fieldacytracking" id="field_tracking_'.esc_attr($form->form_tag_name).'">';
echo '<label for="mailingdata_tracking_'.esc_attr($form->form_tag_name).'">';
echo '<input type="hidden" name="user[tracking]" value="0"/>';
echo '<input id="mailingdata_tracking_'.esc_attr($form->form_tag_name).'" class="checkbox" type="checkbox" name="user[tracking]" value="1" '.acym_checked($trackingChecked, true, false).'/> '.esc_html(
        acym_translation('ACYM_TRACKING_CONSENT')
    );
echo '</label>';
echo '</div>';
