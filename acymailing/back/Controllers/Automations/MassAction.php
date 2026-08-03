<?php

namespace AcyMailing\Controllers\Automations;

use AcyMailing\Classes\AutomationClass;

trait MassAction
{
    public function setFilterMassAction(): void
    {
        wp_verify_nonce(acym_getVar('cmd', '_wpnonce'), 'acymnonce') || die('Invalid Token');

        $this->setSaveFilters(true);
        $this->summary();
    }

    public function setActionMassAction(): void
    {
        wp_verify_nonce(acym_getVar('cmd', '_wpnonce'), 'acymnonce') || die('Invalid Token');

        $this->getSaveActions(true);
        $this->filter();
    }

    public function processMassAction(): void
    {
        wp_verify_nonce(acym_getVar('cmd', '_wpnonce'), 'acymnonce') || die('Invalid Token');

        $automationClass = new AutomationClass();
        $massAction = acym_getVar('array', 'massAction', [], 'SESSION');
        if (!empty($massAction)) {
            $automation = new \stdClass();
            $automation->filters = json_encode($massAction['filters']);
            $automation->actions = json_encode($massAction['actions']);
            $automationClass->execute($automation);

            if (!empty($automationClass->report)) {
                foreach ($automationClass->report as $oneReport) {
                    acym_enqueueMessage($oneReport, 'info');
                }
            }
        }
        $this->listing();
    }
}
