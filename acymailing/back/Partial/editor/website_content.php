<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');
?>
<div class="acym__popup__listing text-center grid-x">
	<h1 class="acym__title acym__title__secondary text-center cell"><?php echo esc_html(acym_translation('ACYM_LINKS')); ?></h1>
	<div class="grid-x medium-12 cell acym__row__no-listing text-left acym_vcenter">
		<div class="grid-x cell medium-5 small-12 acym__listing__title acym__listing__title__dynamics acym__online__link acym_vcenter">
			<label class="small-3 margin-bottom-0" for="acym__popup__online__tagtext"><?php echo esc_html(acym_translation('ACYM_TEXT')); ?>: </label>
			<input class="small-9" type="text" name="tagtext" id="acym__popup__online__tagtext" readonly onchange="setOnlineTag();">
		</div>
	</div>
	<div class="cell grid-x">
        <?php foreach ($links as $tagname => $tag) {
            $disabledClass = !empty($tag['disabled']) ? ' acym__listing__row__popup--disabled' : '';
            $onclick = empty($tag['disabled']) ? "changeOnlineTag('".$tagname."');" : ''; ?>
			<div class="grid-x small-12 cell acym__row__no-listing acym__listing__row__popup text-left<?php echo esc_attr($disabledClass); ?>" <?php echo empty($onclick) ? ''
                : 'onclick="'.esc_attr($onclick).'"'; ?>
			     id="tr_<?php echo esc_attr($tagname); ?>">
                <?php if (!empty($tag['disabled'])) {
                    acym_tooltip(['hoveredText' => $tag['desc'], 'textShownInTooltip' => $tag['tooltip'], 'classContainer' => 'cell']);
                } else { ?>
					<div class="cell small-12 acym__listing__title acym__listing__title__dynamics">
                        <?php
                        echo wp_kses(
                            $tag['desc'],
                            [
                                'b' => [],
                            ]
                        );
                        ?>
					</div>
                <?php } ?>
			</div>
        <?php } ?>
	</div>
</div>

<div class="acym__popup__listing text-center grid-x">
	<h1 class="acym__title acym__title__secondary text-center cell"><?php echo esc_html(acym_translation('ACYM_INFORMATION')); ?></h1>
	<div class="cell grid-x">
        <?php foreach ($information as $infoKey => $info) {
            $disabledClass = empty($info['value']) ? ' acym__listing__row__popup--disabled' : '';
            $onclick = empty($info['value']) ? '' : "changeOnlineTag('info', '".$infoKey."', true); setTag('{info:".$infoKey."}', jQuery(this));";
            ?>
			<div class="grid-x medium-12 cell acym__row__no-listing acym__listing__row__popup text-left<?php echo esc_attr($disabledClass); ?>"
                <?php echo empty($onclick) ? '' : 'onclick="'.esc_attr($onclick).'"'; ?>
				 id="tr_<?php echo esc_attr($infoKey); ?>">
				<div class="cell medium-12 small-12 acym__listing__title acym__listing__title__dynamics">
                    <?php echo esc_html($info['label']); ?> :
                    <?php if (acym_isImageUrl($info['value'])) { ?>
						<img src="<?php echo esc_url($info['value']); ?>"
						     alt="Image"
						     style="display: inline-block; max-width: 25px; max-height: 25px; vertical-align: middle;" />
                    <?php } else { ?>
                        <?php echo esc_html($info['value']); ?>
                    <?php } ?>
				</div>
			</div>
        <?php } ?>
	</div>
</div>

