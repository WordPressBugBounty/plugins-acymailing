<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');
?>
<form id="acym_form" action="<?php echo esc_url(acym_completeLink(acym_getVar('cmd', 'ctrl'))); ?>" method="post" name="acySegments" data-abide novalidate>
    <?php
    foreach ($data['filter_option'] as $key => $filterHtml) {
        if (empty($key)) {
            continue;
        }
        ?>
		<template id="acym__segments__edit__info__template__<?php echo esc_attr($key); ?>">
            <?php echo $filterHtml; ?>
		</template>
    <?php } ?>
	<input type="hidden" value="<?php echo empty($data['segment']->id) ? '' : esc_attr($data['segment']->id); ?>" name="segmentId">
	<input type="hidden" id="acym__segments__filters" value="<?php echo esc_attr(empty($data['segment']->filters) ? '' : json_encode($data['segment']->filters)); ?>">
	<input type="hidden" id="acym__segments__filters__count__and" value="0">
	<div class="cell grid-x grid-margin-x acym__segments__edit__info acym__content margin-bottom-1 padding-bottom-0 margin-y margin-left-0 margin-right-0">
		<div class="cell large-4 medium-6 grid-x grid-margin-x acym_vcenter">
			<label for="acym__segments__edit__info__name" class="cell shrink"><?php echo esc_html(acym_translation('ACYM_SEGMENT_NAME')); ?></label>
			<input required
			       type="text"
			       name="segment[name]"
			       class="cell auto"
			       id="acym__segments__edit__info__name"
			       value="<?php echo empty($data['segment']->name) ? '' : esc_attr($data['segment']->name); ?>">
		</div>
		<div class="cell large-3 medium-6 grid-x grid-margin-x acym_vcenter">
            <?php acym_switch([
                'name' => 'segment[active]',
                'value' => $data['segment']->active,
                'label' => acym_translation('ACYM_ACTIVE'),
                'labelClass' => 'shrink',
            ]); ?>
		</div>
		<div class="cell large-5 align-right grid-x grid-margin-x acym__segments__edit__info__actions margin-y margin-bottom-0">
            <?php acym_cancelButton(); ?>
			<button data-before-action="segmentSave" class="cell large-shrink medium-6 button acy_button_submit button-secondary" data-task="apply">
                <?php echo esc_html(acym_translation('ACYM_SAVE')); ?>
			</button>
			<button data-before-action="segmentSave" class="cell large-shrink medium-6 button acy_button_submit" data-task="save">
                <?php echo esc_html(acym_translation('ACYM_SAVE_EXIT')); ?>
			</button>
		</div>
	</div>
	<div class="cell grid-x acym__segments__edit__filters acym__content">
        <?php include acym_getView('segments', 'edit_filters'); ?>
	</div>
    <?php acym_formOptions(); ?>
</form>
