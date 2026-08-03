<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');
?>
<div class="acym_front_page <?php echo esc_attr($data['paramsCMS']['suffix']); ?>">
    <?php
    if (!empty($data['paramsCMS']['show_page_heading'])) {
        echo '<h1 class="contentheading '.esc_attr($data['paramsCMS']['suffix']).'">'.esc_html($data['paramsCMS']['page_heading']).'</h1>';
    }
    ?>
	<div class="acym__front__archive">
		<form method="post" action="<?php
        echo esc_url($data['actionUrl']); ?>" id="acym_form" class="acym__archive__form">
			<h1 class="acym__front__archive__title"><?php echo esc_html(acym_translation('ACYM_NEWSLETTERS')); ?></h1>
			<div id="acym__front__archive__search" class="grid-x">
                <?php
                if (!empty($data['paramsCMS']['widget_id'])) {
                    echo '<input type="text" name="acym_search['.esc_attr($data['paramsCMS']['widget_id']).']" value="'.esc_attr($data['search']).'">';
                } else {
                    ?>
					<input type="text" name="acym_search" value="<?php echo esc_attr($data['search']); ?>">
                <?php }
                $disableSearch = '';
                if (isset($data['disableButtons']) && $data['disableButtons']) {
                    $disableSearch = 'disabled';
                }
                ?>
				<button class="button btn btn-primary subbutton" <?php echo esc_attr($disableSearch); ?>><?php echo esc_html(acym_translation('ACYM_SEARCH')); ?></button>
			</div>

            <?php
            if (empty($data['newsletters'])) {
                echo esc_html(acym_translation('ACYM_NOTHING_FOR_SEARCH'));
            } else {
                foreach ($data['newsletters'] as $oneNewsletter) {
                    $archiveURL = acym_frontendLink('archive&task=view&id='.$oneNewsletter->id.'&'.acym_noTemplate());

                    if ($data['popup']) {
                        $iframeClass = 'acym__modal__iframe';
                        if (empty($data['userId'])) $iframeClass .= ' acym__front__not_connected_user';
                        echo wp_kses(
                            acym_frontModal($archiveURL, $oneNewsletter->subject, false, $oneNewsletter->id, $iframeClass),
                            [
                                'link' => [
                                    'rel' => true,
                                    'href' => true,
                                    'type' => true,
                                ],
                                'a' => [
                                    'class' => true,
                                    'data-acym-modal' => true,
                                    'href' => true,
                                ],
                                'div' => [
                                    'class' => true,
                                    'id' => true,
                                    'style' => true,
                                ],
                                'span' => [
                                ],
                                'iframe' => [
                                    'class' => true,
                                    'src' => true,
                                ],
                            ]
                        );
                    } else {
                        echo '<p class="acym__front__archive__raw"><a href="'.esc_url($archiveURL).'" target="_blank">'.esc_html($oneNewsletter->subject).'</a></p>';
                    }
                    echo '<p class="acym__front__archive__newsletter_sending-date">';
                    echo esc_html(acym_translation('ACYM_SENDING_DATE').' : '.acym_date($oneNewsletter->sending_date, 'd M Y'));
                    echo '</p>';
                }

                $data['pagination']->display('archive', '', true);
            }

            acym_formOptions(true, 'listing', '', '', false);
            ?>

			<input type="hidden" name="acym_front_page" id="acym__front__archive__next-page" value="1">
		</form>
	</div>
</div>
