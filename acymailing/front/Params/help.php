<?php

include_once __DIR__.DIRECTORY_SEPARATOR.'AcymJFormField.php';

class JFormFieldHelp extends AcymJFormField
{
    public function __construct($form = null)
    {
        $this->type = 'help';
        parent::__construct($form);
    }

    public function getInput()
    {

        $config = acym_config();
        $level = $config->get('level');
        $link = ACYM_DOCUMENTATION;

        return '<a class="btn" target="_blank" href="'.$link.'">'.acym_translation('ACYM_HELP').'</a>';
    }
}
