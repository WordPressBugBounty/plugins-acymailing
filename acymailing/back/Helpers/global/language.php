<?php
defined('ABSPATH') || die('Restricted Access');

function acym_translationExists(string $key): bool
{
    return $key !== acym_translation($key);
}

function acym_loadLanguage(?string $lang = null): void
{
    acym_loadLanguageFile(ACYM_LANGUAGE_FILE, ACYM_ROOT, $lang, true);
    acym_loadLanguageFile(ACYM_LANGUAGE_FILE.'_custom', ACYM_ROOT, $lang, true);
}

function acym_isMultilingual(): bool
{
    if (!acym_level(ACYM_ESSENTIAL)) {
        return false;
    }

    $config = acym_config();
    $mainLanguage = $config->get('multilingual_default');
    $languages = $config->get('multilingual_languages');
    $isMultilingual = !empty($config->get('multilingual', '0'));

    if (!$isMultilingual || empty($mainLanguage) || empty($languages)) {
        return false;
    }

    return true;
}

function acym_getDefaultNewsletterLanguage(): string
{
    $default = acym_config()->get('multilingual_default');

    return empty($default) ? acym_getSiteLanguageTag() : $default;
}

function acym_getDefaultUserLanguage(): string
{
    if (acym_isMultilingual()) {
        $configUserLanguage = acym_config()->get('multilingual_user_default', 'current_language');
        if (!empty($configUserLanguage) && $configUserLanguage !== 'current_language') {
            return $configUserLanguage;
        }
    }

    return acym_getDefaultNewsletterLanguage();
}

function acym_getMultilingualLanguages(): array
{
    $allLanguages = acym_getLanguages();

    $config = acym_config();
    $languageCodes = array_merge(
        [
            $config->get('multilingual_default'),
        ],
        explode(',', $config->get('multilingual_languages'))
    );

    $languages = [];

    foreach ($languageCodes as $languageCode) {
        if (empty($allLanguages[$languageCode])) {
            continue;
        }

        $languages[$languageCode] = $allLanguages[$languageCode];
    }

    return $languages;
}

function acym_displayLanguageRadio(array $languages, string $name, $translation, string $info, $default = '', string $type = ''): void
{
    $config = acym_config();
    $defaultLanguage = $config->get('multilingual_default');

    if (is_array($translation)) {
        $translation = json_encode($translation);
    }
    if (is_array($default)) {
        $default = json_encode($default);
    }
    ?>

	<div class="cell grid-x grid-margin-x acym__multilingual__selection" id="acym__multilingual__selection-<?php echo esc_attr($type); ?>">
		<input type="hidden" class="acym__multilingual__selection__translation" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($translation ?? ''); ?>">
		<input type="hidden" class="acym__multilingual__selection__translation__default" value="<?php echo esc_attr($default ?? ''); ?>">
		<input type="hidden" class="acym__multilingual__selection__main-language" value="<?php echo esc_attr($defaultLanguage); ?>">
		<h4 class="cell shrink acym__title">
            <?php echo esc_html(acym_translation('ACYM_LANGUAGE')); ?>
            <?php acym_info(['textShownInTooltip' => $info]); ?>
		</h4>

        <?php foreach ($languages as $code => $language) { ?>
			<div class="cell shrink acym__multilingual__selection__one <?php echo esc_attr($defaultLanguage === $code ? 'acym__multilingual__selection__one__selected' : ''); ?>"
			     data-acym-code="<?php echo esc_attr($code); ?>"
			     data-acym-tooltip="<?php echo esc_attr($language->name); ?>">
				<img src="<?php echo esc_url(acym_getFlagByCode($code)); ?>" alt="<?php echo esc_attr($code); ?> flag">
			</div>
        <?php } ?>
	</div>
    <?php
}

/**
 * Display the according translation
 * Works like sprintf(), but accepts an array as an argument, instead of a list of arguments.
 */
function acym_translationVsprintf(string $key, array $messageData, bool $isKey = true): string
{
    if ($isKey) {
        return vsprintf(acym_translation($key), $messageData);
    } else {
        return vsprintf($key, $messageData);
    }
}
