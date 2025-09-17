<?php

include_once __DIR__.DIRECTORY_SEPARATOR.'AcymJFormField.php';

class JFormFieldArchive extends AcymJFormField
{
    public function __construct($form = null)
    {
        $this->type = 'archive';
        parent::__construct($form);
    }

    public function getInput()
    {

        $value = empty($this->value) ? 0 : $this->value;

        return acym_select(
            [
                '5' => '5',
                '10' => '10',
                '15' => '15',
                '20' => '20',
                '30' => '30',
                '50' => '50',
                '100' => '100',
                '200' => '200',
            ],
            $this->name,
            $value
        );
    }
}
