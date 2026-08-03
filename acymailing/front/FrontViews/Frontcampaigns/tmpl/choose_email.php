<?php
defined('ABSPATH') || die('Restricted Access');
?>
<form id="acym_form" action="<?php echo esc_url(acym_completeLink(acym_getVar('cmd', 'ctrl'))); ?>" method="post" name="acyForm"
    <?php echo !empty($data['menuClass']) ? 'class="'.esc_attr($data['menuClass']).'"' : ''; ?> >

	<input type="hidden" value="<?php echo esc_attr($data['campaignID']); ?>" name="campaignId" id="acym__campaign__choose__campaign">
	<input type="hidden" name="mail[id]" value="<?php echo empty($data['mailInformation']->id) ? '' : intval($data['mailInformation']->id); ?>" />
	<div id="acym__templates__choose" class="acym__content">
        <?php
        if (empty($data['campaignID'])) $data['workflowHelper']->disabledAfter = 'chooseTemplate';
        $data['workflowHelper']->display($this->steps, $this->step);

        include acym_getView('frontcampaigns', 'choose_template');
        ?>
	</div>
    <?php acym_formOptions(false, 'edit', 'chooseTemplate'); ?>
</form>
