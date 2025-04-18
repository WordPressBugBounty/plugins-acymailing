<?php

namespace AcyMailing\Classes;

use AcyMailing\Core\AcymClass;

class RuleClass extends AcymClass
{
    public const FINAL_RULE_ID = 17;

    public function __construct()
    {
        parent::__construct();

        $this->table = 'rule';
        $this->pkey = 'id';
    }

    public function getAll(?string $key = null): array
    {
        $rules = acym_loadObjectList('SELECT * FROM `#__acym_rule` ORDER BY `ordering` ASC');

        foreach ($rules as $i => $rule) {
            $rules[$i] = $this->_prepareRule($rule);
        }

        return $rules;
    }

    public function getOneById($id)
    {
        $rule = acym_loadObject('SELECT * FROM `#__acym_rule` WHERE `id` = '.intval($id));

        return $this->_prepareRule($rule);
    }

    private function _prepareRule($rule)
    {
        $columns = ['executed_on', 'action_message', 'action_user'];
        foreach ($columns as $oneColumn) {
            if (!empty($rule->$oneColumn)) {
                $rule->$oneColumn = json_decode($rule->$oneColumn, true);
            }
        }

        return $rule;
    }

    public function save($rule)
    {
        if (empty($rule)) {
            return false;
        }

        return parent::save($rule);
    }

    public function getOrderingNumber()
    {
        return acym_loadResult('SELECT COUNT(`id`) FROM #__acym_rule');
    }

    public function cleanTable()
    {
        acym_query('TRUNCATE TABLE `#__acym_rule`');
    }
}
