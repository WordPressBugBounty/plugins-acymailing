<div id="acym__wysid__right__toolbar__settings" style="display: none;" class="cell grid-padding-x acym__wysid__right__toolbar--menu">
	<p class="acym__wysid__right__toolbar__p__open acym__wysid__right__toolbar__p acym__title">
        <?php echo acym_translation('ACYM_TEMPLATE_DESIGN'); ?><i class="acymicon-keyboard-arrow-up"></i>
	</p>
	<div class="grid-y acym__wysid__right__toolbar__design--show acym__wysid__context__modal__container">
		<div class="grid-x margin-bottom-1 small-12 cell">
			<label for="acym__wysid__background-colorpicker" class="cell large-6 small-9" for="acym__wysid__background-colorpicker">
                <?php echo acym_translation('ACYM_BACKGROUND_COLOR'); ?>
			</label>
			<i class="acymicon-insert-photo small-1 acym_vcenter text-center cell acym__color__light-blue cursor-pointer" id="acym__wysid__background-image__template"></i>
			<i class="acymicon-close acym_vcenter acym__color__red" id="acym__wysid__background-image__template-delete"></i>
			<div class="small-2 text-center cell" style="margin:auto 0;">
				<input type="text" id="acym__wysid__background-colorpicker" class="cell medium-shrink small-4" />
			</div>
		</div>
		<div class="grid-x margin-bottom-1 small-12 cell">
			<label class="middle large-6 cell" for="acym__wysid__background-size"><?php echo acym_translation('ACYM_BACKGROUND_SIZE'); ?></label>
			<div class="cell large-6">
                <?php
                $backgroundSize = [
                    'ACYM_DEFAULT' => [
                        'cover' => acym_translation('ACYM_BG_COVER'),
                        'auto' => acym_translation('ACYM_AUTO'),
                        'contain' => acym_translation('ACYM_BG_CONTAIN'),
                    ],
                    'ACYM_CUSTOM' => [
                        '10%' => '10%',
                        '15%' => '15%',
                        '20%' => '20%',
                        '25%' => '25%',
                        '30%' => '30%',
                        '35%' => '35%',
                        '40%' => '40%',
                        '45%' => '45%',
                        '50%' => '50%',
                        '55%' => '55%',
                        '60%' => '60%',
                        '65%' => '65%',
                        '70%' => '70%',
                        '75%' => '75%',
                        '80%' => '80%',
                        '85%' => '85%',
                        '90%' => '90%',
                        '95%' => '95%',
                        '100%' => '100%',
                    ],
                ];

                $defaultBackgroundSize = 'cover';

                if (!empty($data['mail']->settings)) {
                    if (!is_array($data['mail']->settings)) $data['mail']->settings = json_decode($data['mail']->settings, true);

                    if (!empty($data['mail']->settings['default']['background-size'])) $defaultSize = $data['mail']->settings['default']['background-size'];
                }

                echo '<select name="acym__wysid__background-size" id="acym__wysid__background-size" class="acym__select">';

                foreach ($backgroundSize as $groupName => $values) {
                    echo '<optgroup label="'.acym_translation($groupName).'">';
                    foreach ($values as $value => $label) {
                        $selected = ($defaultBackgroundSize === $value) ? 'selected' : '';
                        echo '<option value="'.$value.'" '.$selected.'>'.$label.'</option>';
                    }
                    echo '</optgroup>';
                }

                echo '</select>';
                ?>
			</div>
		</div>
		<div class="grid-x margin-bottom-1 small-12 cell">
			<label class="middle large-6 cell" for="acym__wysid__background-repeat"><?php echo acym_translation('ACYM_BACKGROUND_REPEAT'); ?></label>
			<div class="cell large-6">
                <?php
                $repeat = [
                    'no-repeat' => acym_translation('ACYM_DISABLE'),
                    'repeat' => acym_translation('ACYM_ENABLE'),
                ];

                $defaultRepeat = 'no-repeat';

                if (!empty($data['mail']->settings)) {
                    if (!is_array($data['mail']->settings)) $data['mail']->settings = json_decode($data['mail']->settings, true);

                    if (!empty($data['mail']->settings['default']['background-repeat'])) $defaultSize = $data['mail']->settings['default']['background-repeat'];
                }

                echo acym_select(
                    $repeat,
                    'acym__wysid__background-repeat',
                    $defaultRepeat,
                    ['class' => 'acym__select']
                );
                ?>
			</div>
		</div>
		<div class="grid-x margin-bottom-1 small-12 cell">
			<label class="middle large-6 cell" for="acym__wysid__background-position"><?php echo acym_translation('ACYM_BACKGROUND_POSITION'); ?></label>
			<div class="cell large-6">
                <?php
                $position = [
                    'top' => acym_translation('ACYM_POSITION_TOP'),
                    'center' => acym_translation('ACYM_CENTER'),
                    'bottom' => acym_translation('ACYM_POSITION_BOTTOM'),
                    'left' => acym_translation('ACYM_LEFT'),
                    'right' => acym_translation('ACYM_RIGHT'),
                ];

                $defaultPosition = 'top';

                if (!empty($data['mail']->settings)) {
                    if (!is_array($data['mail']->settings)) $data['mail']->settings = json_decode($data['mail']->settings, true);

                    if (!empty($data['mail']->settings['default']['background-position'])) $defaultPosition = $data['mail']->settings['default']['background-position'];
                }

                echo acym_select(
                    $position,
                    'acym__wysid__background-position',
                    $defaultPosition,
                    ['class' => 'acym__select']
                );
                ?>
			</div>
		</div>
		<div class="grid-x margin-bottom-1 small-12 cell">
			<label for="acym__wysid__maincolor-colorpicker1" class="cell large-6 small-9" for="acym__wysid__maincolor-colorpicker1">
                <?php echo acym_translation('ACYM_MAIN_COLORS').acym_info('ACYM_MAIN_COLORS_DESC'); ?>
			</label>
			<div class="small-1 cell" style="margin:auto 0;">
				<input type="text" id="acym__wysid__maincolor-colorpicker1" class="cell medium-shrink small-4" />
			</div>
			<div class="small-1 cell" style="margin:auto 0;">
				<input type="text" id="acym__wysid__maincolor-colorpicker2" class="cell medium-shrink small-4" />
			</div>
			<div class="small-1 cell" style="margin:auto 0;">
				<input type="text" id="acym__wysid__maincolor-colorpicker3" class="cell medium-shrink small-4" />
			</div>
		</div>
		<div class="grid-x margin-bottom-1 small-12 cell">
			<label class="middle large-6 cell" for="acym__wysid__padding__top__content"><?php echo acym_translation('ACYM_MARGIN_TOP_CONTENT'); ?></label>
			<div class="cell large-6">
				<input type="number" min="0" value="20" id="acym__wysid__padding__top__content" class="cell small-4">
			</div>
		</div>
		<div class="grid-x margin-bottom-1 small-12 cell">
			<label class="middle large-6 cell" for="acym__wysid__padding__bottom__content"><?php echo acym_translation('ACYM_MARGIN_BOTTOM_CONTENT'); ?></label>
			<div class="cell large-6">
				<input type="number" min="0" value="20" id="acym__wysid__padding__bottom__content" class="cell small-4">
			</div>
		</div>
		<div class="grid-x margin-bottom-1 small-12 cell">
			<label class="middle large-6 cell" for="default_font"><?php echo acym_translation('ACYM_DEFAULT_FONT'); ?></label>
			<div class="cell large-6">
                <?php
                $fonts = [
                    'Andale Mono' => 'Andale Mono',
                    'Arial' => 'Arial',
                    'Book Antiqua' => 'Book Antiqua',
                    'Comic Sans MS' => 'Comic Sans MS',
                    'Courier New' => 'Courier New',
                    'Georgia' => 'Georgia',
                    'Helvetica' => 'Helvetica',
                    'Impact' => 'Impact',
                    'Times New Roman' => 'Times New Roman',
                    'Trebuchet MS' => 'Trebuchet MS',
                    'Verdana' => 'Verdana',
                ];

                $defaultFont = 'Helvetica';

                if (!empty($data['mail']->settings)) {
                    if (!is_array($data['mail']->settings)) $data['mail']->settings = json_decode($data['mail']->settings, true);

                    if (!empty($data['mail']->settings['default']['font-family'])) $defaultFont = $data['mail']->settings['default']['font-family'];
                }

                echo acym_select(
                    $fonts,
                    'default_font',
                    $defaultFont,
                    ['class' => 'acym__select']
                );
                ?>
			</div>

		</div>
	</div>
	<p class="acym__wysid__right__toolbar__p__open acym__wysid__right__toolbar__p acym__title">
        <?php echo acym_translation('ACYM_DESIGN'); ?>
		<i class="acymicon-keyboard-arrow-up"></i>
        <?php echo acym_info('ACYM_INTRO_CUSTOMIZE_FONT'); ?>
	</p>
	<div class="grid-y acym__wysid__right__toolbar__design--show acym__wysid__right__toolbar__design acym__wysid__context__modal__container">
		<div class="grid-x margin-bottom-1 small-12 cell">
			<label class="middle large-6 cell" for="acym__wysid__right__toolbar__settings__font--select"><?php echo acym_translation('ACYM_HTML_TAG'); ?></label>
			<div class="cell large-6">
				<select id="acym__wysid__right__toolbar__settings__font--select" class="small-8 large-4 cell acym__select">
					<option value="p">p - <?php echo acym_translation('ACYM_PARAGRAPH'); ?></option>
					<option value="a">a - <?php echo acym_translation('ACYM_LINKS'); ?></option>
					<option value="span.acym_link"><?php echo acym_translation('ACYM_DYNAMIC_TEXTS_LINKS'); ?></option>
					<option value="li">li - <?php echo acym_translation('ACYM_BULLET_LIST'); ?></option>
					<option value="h1">h1</option>
					<option value="h2">h2</option>
					<option value="h3">h3</option>
					<option value="h4">h4</option>
					<option value="h5">h5</option>
					<option value="h6">h6</option>
				</select>
			</div>
		</div>
		<div class="grid-x margin-bottom-1 small-12 cell">
			<label class="middle large-6 cell" for="acym__wysid__right__toolbar__settings__font-family"><?php echo acym_translation('ACYM_FAMILY'); ?></label>
			<div class="cell large-6">
				<select id="acym__wysid__right__toolbar__settings__font-family" class="auto cell acym__select">
					<option style="font-family: 'Andale Mono'">Andale Mono</option>
					<option style="font-family: 'Arial'">Arial</option>
					<option style="font-family: 'Book Antiqua'">Book Antiqua</option>
					<option style="font-family: 'Comic Sans MS'">Comic Sans MS</option>
					<option style="font-family: 'Courier New'">Courier New</option>
					<option style="font-family: 'Georgia'">Georgia</option>
					<option style="font-family: 'Helvetica'">Helvetica</option>
					<option style="font-family: 'Impact'">Impact</option>
					<option style="font-family: 'Times New Roman'">Times New Roman</option>
					<option style="font-family: 'Trebuchet MS'">Trebuchet MS</option>
					<option style="font-family: 'Verdana'">Verdana</option>
				</select>
			</div>
		</div>
		<div class="grid-x margin-bottom-1 small-12 cell">
			<label class="middle large-6 cell" for="acym__wysid__right__toolbar__settings__line-height"><?php echo acym_translation('ACYM_LINE_HEIGHT'); ?></label>
			<div class="cell large-6">
				<select id="acym__wysid__right__toolbar__settings__line-height" class="auto cell acym__select">
					<option value="inherit"><?php echo acym_translation('ACYM_DEFAULT'); ?></option>
					<option value="100%">100%</option>
					<option value="110%">110%</option>
					<option value="120%">120%</option>
					<option value="130%">130%</option>
					<option value="140%">140%</option>
					<option value="150%">150%</option>
					<option value="160%">160%</option>
					<option value="170%">170%</option>
					<option value="180%">180%</option>
					<option value="190%">190%</option>
					<option value="200%">200%</option>
					<option value="210%">210%</option>
					<option value="220%">220%</option>
					<option value="230%">230%</option>
					<option value="240%">240%</option>
				</select>
			</div>
		</div>
		<div class="grid-x margin-bottom-1 small-12 cell">
			<label class="middle large-6 cell" for="acym__wysid__right__toolbar__settings__font-size"><?php echo acym_translation('ACYM_SIZE'); ?></label>
			<div class="cell large-6">
				<select id="acym__wysid__right__toolbar__settings__font-size" class="auto cell acym__select">
					<option>10px</option>
					<option>12px</option>
					<option>14px</option>
					<option>16px</option>
					<option>18px</option>
					<option>20px</option>
					<option>22px</option>
					<option>24px</option>
					<option>26px</option>
					<option>28px</option>
					<option>30px</option>
					<option>32px</option>
					<option>34px</option>
					<option>36px</option>
				</select>
			</div>
		</div>
		<div class="grid-x margin-bottom-1 small-12 cell">
			<label class="middle large-6 cell"><?php echo acym_translation('ACYM_STYLE'); ?></label>
			<i id="acym__wysid__right__toolbar__settings__bold" class="acymicon-format-bold text-center small-3 large-auto cell" style="line-height: 39px"></i>
			<i id="acym__wysid__right__toolbar__settings__italic" class="acymicon-format-italic text-center small-3 large-auto cell" style="line-height: 39px"></i>
			<div class="small-2 text-center cell" style="margin:auto"><input type="text" id="acym__wysid__right__toolbar__settings__color" style="display: none;"></div>
		</div>
		<div class="grid-x margin-bottom-1 small-12 cell">
			<div class="cell hide-for-small-only medium-3"></div>
            <?php
            $dataStyleSheet = '<div class="grid-x acym__wysid__right__toolbar__settings__stylesheet">
				<h6 class="acym__title acym__title__secondary cell text-center margin-top-1">'.acym_translation('ACYM_HERE_PASTE_YOUR_STYLESHEET').'</h6>
				<textarea id="acym__wysid__right__toolbar__settings__stylesheet__textarea" class="margin-top-1 margin-bottom-1" rows="15"></textarea>
				<button type="button" id="acym__wysid__right__toolbar__settings__stylesheet__cancel" class="button cell medium-4">'.acym_translation('ACYM_CANCEL').'</button>
				<div class="medium-4 cell"></div>
				<button type="button" id="acym__wysid__right__toolbar__settings__stylesheet__apply" class="button cell medium-4">'.acym_translation('ACYM_LOAD_STYLESHEET').'</button>
		   </div>';
            echo acym_modal(
                acym_translation('ACYM_CUSTOM_ADD_STYLESHEET'),
                $dataStyleSheet,
                'acym__wysid__right__toolbar__settings__stylesheet__modal',
                '',
                'class="button cell medium-6 margin-top-2" id="acym__wysid__right__toolbar__settings__stylesheet__open"'
            ); ?>
		</div>
	</div>
	<p class="acym__wysid__right__toolbar__p__open acym__wysid__right__toolbar__p acym__title">
        <?php echo acym_translation('ACYM_CUSTOM_SOCIAL_ICONS'); ?><i class="acymicon-keyboard-arrow-up"></i></p>
	<div class="grid-y acym__wysid__right__toolbar__design--show acym__wysid__right__toolbar__design acym__wysid__right__toolbar__design__social__icons acym__wysid__context__modal__container">
        <?php
        $config = acym_config();
        $socialIcons = json_decode($config->get('social_icons', '{}'), true);
        foreach ($socialIcons as $social => $iconUrl) {
            echo '<div class="cell grid-x margin-bottom-2 acym_vcenter acym__wysid__right__toolbar__design__social__icons__one">
                        				<img class="cell shrink" src="'.acym_escape($iconUrl).'" alt="icon '.acym_escape($social).'">
                        				<input type="file" name="icon_'.acym_escape($social).'" class="auto cell" accept="image/png, image/jpeg">
                        				<div class="auto cell grid-x text-center align-center acym_vcenter"><span class="shrink cell acym__wysid__social__icons__import__text">'.acym_translation(
                    'ACYM_SELECT_NEW_ICON'
                ).'</span></div>
                        				<button disabled type="button" class="button cell shrink acym__wysid__social__icons__import">'.acym_translation('ACYM_IMPORT').'</button>
                        			 </div>';
        }
        ?>
	</div>
</div>
