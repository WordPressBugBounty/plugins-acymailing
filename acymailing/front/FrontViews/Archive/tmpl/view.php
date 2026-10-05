<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');
?>
<div id="archive_view">
    <?php
    if (empty($data['receiver']->id)) {
        echo '<p class="acym_front_message_warning">'.esc_html(acym_translation('ACYM_FRONT_ARCHIVE_NOT_CONNECTED')).'</p>';
    }
    ?>
	<h1 class="contentheading"><?php echo esc_html($data['mail']->subject); ?></h1>

    <?php acym_mailContentInput($data['mail']->body, 'archive_view__content'); ?>
	<div style="min-width:80%" id="archive_view__preview">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Campaign content, built by admin and cannot be escaped.
		echo $data['mail']->body;
		?>
	</div>

    <?php
    $attachments = json_decode(!empty($data['mail']->attachments) ? $data['mail']->attachments : '[]', true);

    if (!empty($attachments)) {
        ?>
		<fieldset class="newsletter_attachments">
			<legend><?php echo esc_html(acym_translation('ACYM_ATTACHMENTS')); ?></legend>
			<table>
                <?php
                foreach ($attachments as $attachment) {
                    $onlyFilename = explode('/', $attachment['filename']);
                    $onlyFilename = end($onlyFilename);

                    echo '<tr><td><a href="'.esc_url(acym_rootURI().$attachment['filename']).'" target="_blank">'.esc_html($onlyFilename).'</a></td></tr>';
                }
                ?>
			</table>
		</fieldset>
    <?php } ?>
</div>
