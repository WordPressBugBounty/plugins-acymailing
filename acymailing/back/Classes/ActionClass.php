<?php

namespace AcyMailing\Classes;

use AcyMailing\Core\AcymClass;

class ActionClass extends AcymClass
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'action';
        $this->pkey = 'id';
    }

    public function getActionsByStepId($stepId): array
    {
        return acym_loadObjectList(
            'SELECT `action`.* 
            FROM #__acym_action AS `action` 
            LEFT JOIN #__acym_condition AS `conditionT` ON `action`.condition_id = `conditionT`.id 
            WHERE `conditionT`.step_id = '.intval($stepId).' 
            ORDER BY `action`.order'
        );
    }

    public function getActionsByConditionId($id): array
    {
        return acym_loadObjectList(
            'SELECT action.* 
            FROM #__acym_action as action 
            WHERE action.condition_id = '.intval($id)
        );
    }

    public function getOneByConditionId($id)
    {
        return acym_loadObject(
            'SELECT `action`.* 
            FROM #__acym_action AS `action` 
            WHERE `action`.`condition_id` = '.intval($id)
        );
    }

    public function getAllActionsIdByConditionsId($elements)
    {
        acym_arrayToInteger($elements);

        return acym_loadResultArray('SELECT id FROM #__acym_action WHERE condition_id IN ('.implode(',', $elements).')');
    }

    public function delete($elements)
    {
        acym_arrayToInteger($elements);
        if (empty($elements)) return 0;
        $actions = acym_loadObjectList('SELECT * FROM #__acym_action WHERE id IN ('.implode(',', $elements).')');
        if (empty($actions)) return 0;

        $mailClass = new MailClass();

        foreach ($actions as $action) {
            $action->actions = json_decode($action->actions, true);
            if (!empty($action->actions)) {
                foreach ($action->actions as $innerAction) {
                    if (!empty($innerAction['acy_add_queue']) && !empty($innerAction['acy_add_queue']['mail_id'])) $mailClass->delete($innerAction['acy_add_queue']['mail_id']);
                }
            }
        }

        return parent::delete($elements);
    }

    public function save($element)
    {
        if (!isset($element->order)) $element->order = 1;
        parent::save($element);
    }
}
