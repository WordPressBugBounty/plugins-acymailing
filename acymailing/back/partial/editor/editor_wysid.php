<input type="hidden" id="default_template" value="<?php echo acym_escape($this->defaultTemplate); ?>" />
<input type="hidden" class="acym__wysid__hidden__save__content" id="editor_content" name="editor_content" value="" />
<input type="hidden" class="acym__wysid__hidden__save__content__template" id="editor_content_template" name="editor_content_template" value="" />
<?php $wysidStylesheet = $this->getWYSIDStylesheet(); ?>
<input type="hidden"
	   class="acym__wysid__hidden__save__stylesheet"
	   id="editor_stylesheet"
	   name="editor_stylesheet"
	   value="<?php echo acym_escape($wysidStylesheet); ?>" />
<input type="hidden"
	   class="acym__wysid__hidden__save__stylesheet__template"
	   id="editor_stylesheet_template"
	   name="editor_stylesheet_template"
	   value="<?php echo acym_escape($wysidStylesheet); ?>" />
<?php $wysidSettings = $this->getWYSIDSettings();
$mainColors = $this->getDefaultColors(); ?>
<input type="hidden" class="acym__wysid__hidden__save__settings" id="editor_settings" name="editor_settings" value="<?php echo acym_escape($wysidSettings); ?>" />
<input type="hidden"
	   class="acym__wysid__hidden__save__settings__template"
	   id="editor_settings_template"
	   name="editor_settings_template"
	   value="<?php echo acym_escape($wysidSettings); ?>" />
<input type="hidden" class="acym__wysid__hidden__save__colors" id="main_colors" name="main_colors" value="<?php echo acym_escape($mainColors); ?>" />
<input type="hidden" class="acym__wysid__hidden__save__colors__template" id="main_colors_template" name="main_colors_template" value="<?php echo acym_escape($mainColors); ?>" />
<input type="hidden" id="acym__wysid__session--lifetime" name="acym_session_lifetime" value="<?php echo acym_escape(ini_get('session.gc_maxlifetime')); ?>" />
<input type="hidden" class="acym__wysid__hidden__mailId" id="editor_mailid" name="editor_autoSave" value="<?php echo intval($this->mailId); ?>" />
<input type="hidden" class="acym__wysid__hidden__save__auto" id="editor_autoSave" value="<?php echo acym_escape($this->autoSave); ?>">
<input type="hidden" id="acym__template__preview">
<input type="hidden" id="acym__wysid__block__html__content">

<div id="acym__wysid__edit" class="cell grid-x margin-top-1">
	<div class="cell medium-auto"></div>
	<div class="cell <?php echo acym_isAdmin() ? 'xxlarge-9' : ''; ?> grid-x grid-margin-x acym__content">
        <?php
        if (!empty($data['multilingual']) || !empty($data['abtest'])) {
            include acym_getPartial('editor', 'versions');
            $preheaderSize = 'large-6';
            include acym_getView('campaigns', 'edit_email_info_content', true);
        }
        ?>

		<div class="cell grid-x align-center padding-1 padding-bottom-0">
			<div class="cell medium-auto hide-for-small-only"></div>
			<button id="acym__wysid__edit__button" type="button" class="cell button xlarge-3 large-4 medium-5 margin-bottom-0">
				<i class="acymicon-edit"></i>
                <?php
                $ctrl = acym_getVar('string', 'ctrl');
                if (in_array(acym_getVar('string', 'ctrl'), ['campaigns', 'frontcampaigns']) || !empty(acym_getVar('cmd', 'notification'))) {
                    echo acym_translation('ACYM_EDIT_MAIL');
                } elseif ($this->walkThrough || !empty(acym_getVar('cmd', 'return'))) {
                    echo acym_translation('ACYM_EDIT');
                } else {
                    echo acym_translation('ACYM_EDIT_TEMPLATE');
                }
                ?>
			</button>
			<div class="cell medium-auto hide-for-small-only"></div>
		</div>

		<div class="cell grid-x" id="acym__wysid__edit__preview">
            <?php
            if (!empty($data['multilingual'])) {
                echo acym_tooltip(
                    [
                        'hoveredText' => '<div id="acym__wysid__edit__preview__reset__content"><i class="acymicon-rotate-left"></i></div>',
                        'textShownInTooltip' => acym_translation('ACYM_REMOVE_TRANSLATION_DESC'),
                        'classContainer' => 'acym__wysid__edit__preview__reset is-hidden',
                    ]
                );
            }
            ?>
			<div class="cell medium-auto hide-for-small-only"></div>
            <?php
            $classes = '';
            if (acym_isAdmin()) {
                if (!$this->walkThrough) {
                    $classes = 'large-10 xxlarge-9';
                }
            }
            ?>
			<div id="acym__wysid__email__preview" class="acym__email__preview grid-x cell <?php echo $classes; ?> margin-top-1"></div>
			<div class="cell medium-auto hide-for-small-only"></div>
		</div>
	</div>
	<div class="cell medium-auto"></div>
</div>

<div class="grid-x grid-margin-x">
	<div id="acym__wysid" class="grid-x margin-0 grid-margin-x cell" style="display: none;">
		<!--Template & top toolbar-->
		<div id="acym__wysid__wrap" class="grid-y auto cell grid-padding-x grid-padding-y">
			<!--Warning area when generating thumbnail-->
			<div id="acym__wysid__warning__thumbnail" class="grid-x align-center" style="display: none;">
				<div class="cell align-center acym_vcenter">
					<h3><?php echo acym_translation('ACYM_SAVING_EMAIL'); ?></h3>
				</div>
                <?php echo acym_loaderLogo(); ?>
			</div>
			<!--Top toolbar-->
            <?php
            include acym_getPartial('editor', 'top_toolbar');
            include acym_getPartial('editor', 'source');
            include acym_getPartial('editor', 'template');
            ?>
		</div>

		<div class="grid-y large-4 small-3 cell" id="acym__wysid__right">
			<!--Send test-->
            <?php
            include acym_getPartial('editor', 'test');
            ?>

			<!--Right toolbar-->
			<div id="acym__wysid__right-toolbar" class="grid-y cell">
				<div id="acym__wysid__right-toolbar__overlay"></div>
				<div class="acym__wysid__right-toolbar__content grid-y grid-padding-x small-12 cell" style="max-height: 829px;">
					<div class="cell grid-x text-center">
						<p data-attr-show="acym__wysid__right__toolbar__design"
						   id="acym__wysid__right__toolbar__design__tab"
						   class="large-4 small-4 cell acym__wysid__right__toolbar__selected acym__wysid__right__toolbar__tabs">
							<i class="acymicon-th"></i>
						</p>
						<p data-attr-show="acym__wysid__right__toolbar__current-block"
						   id="acym__wysid__right__toolbar__block__tab"
						   class="large-4 small-4 cell acym__wysid__right__toolbar__tabs">
							<i class="acymicon-edit"></i>
						</p>
						<p data-attr-show="acym__wysid__right__toolbar__settings"
						   id="acym__wysid__right__toolbar__settings__tab"
						   class="large-4 small-4 cell acym__wysid__right__toolbar__tabs">
							<i class="acymicon-cog"></i>
						</p>
					</div>

                    <?php
                    include acym_getPartial('editor', 'design');
                    include acym_getPartial('editor', 'settings');
                    include acym_getPartial('editor', 'context');
                    ?>
				</div>
			</div>
		</div>

		<!--Modal-->
		<div id="acym__wysid__modal" class="acym__wysid__modal">
			<div class="acym__wysid__modal__bg acym__wysid__modal--close"></div>
			<div class="acym__wysid__modal__ui float-center cell">
				<div id="acym__wysid__modal__ui__fields"></div>
				<div id="acym__wysid__modal__ui__display"></div>
				<div id="acym__wysid__modal__ui__search" class="margin-bottom-1"></div>
				<button class="close-button acym__wysid__modal--close" aria-label="Dismiss alert" type="button" data-close="">
					<span aria-hidden="true">×</span>
				</button>
			</div>
		</div>

        <?php if ('joomla' === ACYM_CMS) {
            include acym_getPartial('joomla', 'media_modal');
        } ?>
	</div>
</div>
<div id="acym__wysid__fullscreen__modal" class="grid-x align-center">
	<div class="acym__imac cell medium-8 acym__wysid__fullscreen__modal__content__container" style="display: none">
		<div id="acym__wysid__fullscreen__modal__content__desktop" class=acym__imac__screen></div>
		<div class="acym__imac__stand"></div>
	</div>
	<div class="cell medium-4 acym__iphone acym__wysid__fullscreen__modal__content__container" style="display: none">
		<div id="acym__wysid__fullscreen__modal__content__smartphone" class="acym__iphone__screen"></div>
	</div>
	<div class="grid-x cell small-12"></div>
	<button id="acym__wysid__fullscreen__modal__close" class="close-button padding-1" aria-label="Dismiss alert" type="button" data-close="">
		<span aria-hidden="true">×</span>
	</button>
</div>
