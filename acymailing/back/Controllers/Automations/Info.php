<?php

namespace AcyMailing\Controllers\Automations;

use AcyMailing\Classes\ActionClass;
use AcyMailing\Classes\AutomationClass;
use AcyMailing\Classes\ConditionClass;
use AcyMailing\Classes\StepClass;
use AcyMailing\Helpers\WorkflowHelper;

trait Info
{
    public function info(): void
    {
        if (!acym_level(ACYM_ENTERPRISE)) {
            acym_redirect(acym_completeLink('dashboard&task=upgrade&version=enterprise', false, true));
        }

    }

    private function saveInfos(): array
    {
        $automationId = acym_getVar('int', 'id');
        $automation = acym_getVar('array', 'automation');
        $automationClass = new AutomationClass();

        $stepAutomationId = acym_getVar('int', 'stepAutomationId');
        $stepAutomation = acym_getVar('array', 'stepAutomation');
        $typeTrigger = acym_getVar('string', 'type_trigger');
        $stepClass = new StepClass();

        if (!empty($automationId)) {
            $automation['id'] = $automationId;
        }

        if (!empty($stepAutomationId)) {
            $stepAutomation['id'] = $stepAutomationId;
            $conditionClass = new ConditionClass();
            $actionClass = new ActionClass();
            $condition = $conditionClass->getOneByStepId($stepAutomationId);
            $step = $stepClass->getOneById($stepAutomationId);

            $triggerChanged = $typeTrigger === 'classic' && json_decode($step->triggers)->type_trigger === 'user';
            if ($triggerChanged && !empty($condition)) {
                $action = $actionClass->getOneByConditionId($condition->id);
                if (json_decode($condition->conditions)->type_condition === 'user') {
                    $condition->conditions = null;
                    $conditionClass->save($condition);
                }
                if (!empty($action->filters) && json_decode($action->filters)->type_filter === 'user') {
                    $action->filters = null;
                    $actionClass->save($action);
                }
            }
        }

        if (empty($automation['admin'])) {
            if (empty($automation['name'])) {
                return [];
            }

            $automation['admin'] = 0;
        }

        if (empty($stepAutomation['triggers'][$typeTrigger])) {
            acym_enqueueMessage(acym_translation('ACYM_PLEASE_SELECT_ONE_TRIGGER'), 'error');

            $this->info();

            return [];
        }

        $stepAutomation['triggers'][$typeTrigger]['type_trigger'] = $typeTrigger;
        $stepAutomation['triggers'] = json_encode($stepAutomation['triggers'][$typeTrigger]);

        $stepAutomation['automation_id'] = $automationId;

        foreach ($automation as $column => $value) {
            acym_secureDBColumn($column);
        }

        foreach ($stepAutomation as $stepColumn => $stepValue) {
            acym_secureDBColumn($stepColumn);
        }

        $automation = (object)$automation;
        $stepAutomation = (object)$stepAutomation;

        $automation->id = $automationClass->save($automation);
        $stepAutomation->automation_id = $automation->id;
        $stepAutomation->id = $stepClass->save($stepAutomation);

        $returnIds = [
            'automationId' => $automation->id,
            'stepId' => $stepAutomation->id,
            'typeTrigger' => $typeTrigger,
        ];

        if (!empty($returnIds['automationId']) && !empty($returnIds['stepId'])) {
            return $returnIds;
        } else {
            return [];
        }
    }

    public function saveExitInfo(): void
    {
        $ids = $this->saveInfos();

        if (empty($ids)) {
            return;
        }

        acym_enqueueMessage(acym_translation('ACYM_SUCCESSFULLY_SAVED'));

        acym_setVar('id', $ids['automationId']);
        acym_setVar('stepId', $ids['stepId']);
        $this->listing();
    }

    public function saveInfo(): void
    {
        $ids = $this->saveInfos();

        if (empty($ids)) {
            return;
        }

        acym_setVar('id', $ids['automationId']);
        acym_setVar('stepId', $ids['stepId']);
        $this->condition();
    }
}
