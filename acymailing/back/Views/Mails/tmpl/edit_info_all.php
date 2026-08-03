<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');
if ($data['mail']->type != $data['mailClass']::TYPE_OVERRIDE) { ?>
	<div class="cell medium-6">
		<label>
            <?php echo esc_html(acym_translation('ACYM_NAME')); ?>
			<input name="mail[name]" type="text" class="acy_required_field" value="<?php echo esc_attr($data['mail']->name); ?>" required>
		</label>
	</div>
<?php } ?>
<div class="cell medium-6 <?php echo $data['mail']->type == $data['mailClass']::TYPE_OVERRIDE ? '' : 'medium-6'; ?>">
	<label>
        <?php echo esc_html(acym_translation('ACYM_EMAIL_SUBJECT')); ?>
		<input name="mail[subject]" type="text" value="<?php echo esc_attr($data['mail']->subject); ?>" <?php echo in_array(
            $data['mail']->type,
            [$data['mailClass']::TYPE_WELCOME, $data['mailClass']::TYPE_UNSUBSCRIBE, $data['mailClass']::TYPE_AUTOMATION]
        ) ? 'required' : ''; ?>>
	</label>
</div>
<div class="cell xlarge-3 medium-6">
	<label>
        <?php
        echo esc_html(acym_translation('ACYM_FROM_NAME'));
        $fromName = esc_attr(empty($data['mail']->from_name) ? '' : $data['mail']->from_name);
        ?>
		<input name="mail[from_name]" type="text" placeholder="<?php echo esc_attr($this->config->get('from_name')); ?>" value="<?php echo esc_attr($fromName); ?>">
	</label>
</div>
<div class="cell xlarge-3 medium-6">
	<label>
        <?php
        echo esc_html(acym_translation('ACYM_FROM_EMAIL'));
        $fromEmail = esc_attr(empty($data['mail']->from_email) ? '' : $data['mail']->from_email);
        ?>
		<input name="mail[from_email]" type="text" placeholder="<?php echo esc_attr($this->config->get('from_email')); ?>" value="<?php echo esc_attr($fromEmail); ?>">
	</label>
</div>
<div class="cell xlarge-3 medium-6">
	<label>
        <?php
        echo esc_html(acym_translation('ACYM_REPLYTO_NAME'));
        $replyToNameValue = esc_attr(empty($data['mail']->reply_to_name) ? '' : $data['mail']->reply_to_name);
        ?>
		<input name="mail[reply_to_name]"
		       type="text"
		       placeholder="<?php echo esc_attr($this->config->get('replyto_name')); ?>"
		       value="<?php echo esc_attr($replyToNameValue); ?>">
	</label>
</div>
<div class="cell xlarge-3 medium-6">
	<label>
        <?php
        echo esc_html(acym_translation('ACYM_REPLYTO_EMAIL'));
        $replyToEmailValue = esc_attr(empty($data['mail']->reply_to_email) ? '' : $data['mail']->reply_to_email);
        ?>
		<input name="mail[reply_to_email]"
		       type="text"
		       placeholder="<?php echo esc_attr($this->config->get('replyto_email')); ?>"
		       value="<?php echo esc_attr($replyToEmailValue); ?>">
	</label>
</div>
<div class="cell xlarge-3 medium-6">
	<label>
        <?php
        echo esc_html(acym_translation('ACYM_BCC')).' ';
        acym_info(['textShownInTooltip' => 'ACYM_BCC_DESC']);
        $bccEmail = esc_attr(empty($data['mail']->bcc) ? '' : $data['mail']->bcc);
        ?>
		<input name="mail[bcc]" type="text" placeholder="bcc@example.com" value="<?php echo esc_attr($bccEmail); ?>">
	</label>
</div>

<?php if (!empty($data['langChoice'])) { ?>
	<div class="cell large-6 xlarge-3">
		<label class="cell">
            <?php
            echo esc_html(acym_translation('ACYM_EMAIL_LANGUAGE'));
            acym_info(['textShownInTooltip' => 'ACYM_EMAIL_LANGUAGE_DESC']);
            acym_languageOption($data['langChoice']['links'], $data['langChoice']['name']);
            ?>
		</label>
	</div>
<?php } ?>
