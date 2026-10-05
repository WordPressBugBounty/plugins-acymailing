<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');
?>
<div class="cell grid-x acym__campaign__summary__preview__languages align-center margin-top-2">
    <?php
    $data['languages'] = array_merge([$data['main_language']], $data['languages']);

    foreach ($data['languages'] as $i => $language) {
        $class = $language->code == $data['main_language']->code ? 'language__selected' : '';

        if (empty($data['multilingual_mails'][$language->code]->body) && $language->code != $data['main_language']->code) {
            $class .= ' acym__campaign__summary__preview__languages-one__empty';
        }

        echo '<div data-acym-lang="'.esc_attr($language->code).'" class="cell shrink acym__campaign__summary__preview__languages-one '.esc_attr($class).'">';
        acym_tooltip(
            [
                'hoveredText' => '<img acym-data-lang="'.esc_attr($language->code).'" 
                						src="'.esc_url(acym_getFlagByCode($language->code)).'" 
                						alt="'.esc_attr($language->code).' flag">',
                'textShownInTooltip' => $language->name,
            ]
        );
        echo '</div>';

        if (empty($data['multilingual_mails'][$language->code])) {
            continue;
        }

        acym_mailContentInput(acym_absoluteURL($data['multilingual_mails'][$language->code]->body), 'acym__summary-body-'.$language->code);
        echo '<input type="hidden" id="acym__summary-subject-'.esc_attr($language->code).'" value="'.esc_attr($data['multilingual_mails'][$language->code]->subject).'">';
        echo '<input type="hidden" id="acym__summary-preview-'.esc_attr($language->code).'" value="'.esc_attr($data['multilingual_mails'][$language->code]->preheader).'">';
    }
    ?>
</div>
