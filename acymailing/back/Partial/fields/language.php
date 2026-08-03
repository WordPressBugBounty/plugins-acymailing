<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');
$multilingualLanguages = new \stdClass();
$currentLanguageTag = acym_getLanguageTag();
foreach (acym_getMultilingualLanguages() as $key => $languages) {
    $multilingualLanguages->$key = $languages->name;
}
?>
<div class="cell">
	<multi-language :languageforselect2="'<?php echo esc_attr(json_encode($multilingualLanguages)); ?>'"
	                :currentlangue="'<?php echo esc_attr($currentLanguageTag); ?>'"
	                :place="'<?php echo esc_attr(acym_translation('ACYM_SUBSCRIBE')); ?>'"
	                :value="<?php echo esc_attr(json_encode($value)); ?>"
	                v-model="<?php echo esc_attr($vModel); ?>">
</div>

