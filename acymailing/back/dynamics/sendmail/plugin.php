<?php

use AcyMailing\Core\AcymPlugin;

class plgAcymSendmail extends AcymPlugin
{
    const SENDING_METHOD_ID = 'sendmail';
    const SENDING_METHOD_NAME = 'SendMail';

    public function __construct()
    {
        parent::__construct();
        $this->pluginDescription->name = self::SENDING_METHOD_NAME;
    }

    public function onAcymGetSendingMethods(&$data, $isMailer = false)
    {
        $data['sendingMethods'][self::SENDING_METHOD_ID] = [
            'name' => $this->pluginDescription->name,
            'image' => ACYM_IMAGES.'mailers/sendmail.png',
            'image_class' => 'acym__selection__card__image__smaller',
        ];
    }

    public function onAcymGetSendingMethodsHtmlSetting(&$data)
    {
        ?>
		<div class="send_settings grid-x cell" id="<?php echo esc_attr(self::SENDING_METHOD_ID); ?>_settings">
			<div class="cell grid-x acym_vcenter acym__sending__methods__one__settings">
				<label for="<?php echo esc_attr(self::SENDING_METHOD_ID); ?>_path" class="cell">
                    <?php echo esc_html(acym_translation('ACYM_SENDMAIL_PATH')); ?>
				</label>
				<input id="<?php echo esc_attr(self::SENDING_METHOD_ID); ?>_path"
				       class="cell"
				       type="text"
				       name="config[<?php echo esc_attr(self::SENDING_METHOD_ID); ?>_path]"
				       value="<?php echo esc_attr($this->config->get(self::SENDING_METHOD_ID.'_path', '/usr/sbin/sendmail')); ?>">
			</div>
		</div>
        <?php
    }
}
