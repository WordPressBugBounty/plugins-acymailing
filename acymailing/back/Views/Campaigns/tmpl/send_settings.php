<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View file, its variables are local to the include scope, not true globals.
defined('ABSPATH') || die('Restricted Access');
?>
<div id="acym__campaign__sendsettings">
	<form id="acym_form"
	      action="<?php echo esc_url(acym_completeLink(acym_getVar('cmd', 'ctrl'))); ?>"
	      method="post"
	      name="acyForm"
	      class="cell grid-x acym__form__campaign__edit <?php echo !empty($data['menuClass']) ? esc_attr($data['menuClass']) : ''; ?>"
	      data-abide>
		<input type="hidden" value="<?php echo esc_attr($data['currentCampaign']->id); ?>" name="campaignId">
		<input type="hidden" value="<?php echo esc_attr($data['from'] ?? ''); ?>" name="from">
		<input type="hidden" name="sending_type" value="<?php echo esc_attr($data['currentCampaign']->sending_type); ?>">
		<div class="large-auto"></div>
		<div id="acym__campaigns" class="cell <?php echo esc_attr($data['containerClass']); ?> grid-x grid-margin-x acym__content">

            <?php
            $this->addSegmentStep($data['displaySegmentTab']);
            $workflow = $data['workflowHelper'];
            $workflow->display($this->steps, $this->step, true, false, '', 'campaignId');
            include acym_getView('campaigns', 'send_settings_info', true);
            if (isset($data['currentCampaign']->sending_params['abtest'])) {
                include acym_getView('campaigns', 'send_settings_abtest');
            }
            include acym_getView('campaigns', 'send_settings_sending');
            include acym_getView('campaigns', 'send_settings_actions');
            ?>
		</div>
		<div class="large-auto"></div>
        <?php acym_formOptions(true, 'edit', 'sendSettings'); ?>
	</form>
</div>
