<?php

namespace AcyMailing\Params;

use AcyMailing\Classes\ListClass;

include_once __DIR__.DIRECTORY_SEPARATOR.'AcymJFormField.php';

class JFormFieldLists extends AcymJFormField
{
    var $type = 'lists';

    public $value;

    public function getInput()
    {

        $listClass = new ListClass();
        $lists = $listClass->getAllWithoutManagement();
        foreach ($lists as $i => $oneList) {
            if ($oneList->active == 0) {
                unset($lists[$i]);
            }
        }

        if (ACYM_CMS == 'joomla' && $this->value == 'All' && !empty($this->form)) {
            $formId = $this->form->getData()->get('id');
            if (!empty($formId)) {
                $this->value = '';
            }
        }

        if (is_string($this->value)) {
            $this->value = explode(',', $this->value);
        }

        if (in_array('None', $this->value)) {
            $this->value = [];
        }
        if (in_array('All', $this->value)) {
            $visibleLists = [];
            foreach ($lists as $listId => $oneList) {
                if ($oneList->visible == 0) continue;

                $visibleLists[] = $listId;
            }
            $this->value = $visibleLists;
        }

        return acym_selectMultiple(
            $lists,
            $this->name,
            $this->value,
            [
                'class' => 'acym_simple_select2',
                'id' => $this->name,
            ],
            'id',
            'name'
        );
    }
}
