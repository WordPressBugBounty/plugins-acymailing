<?php

if (!class_exists('AcymJFormField')) {
    if ('WordPress' === 'Joomla') {
        require_once __DIR__.DIRECTORY_SEPARATOR.'AcymJFormFieldJoomla.php';
        class_alias('AcymJFormFieldJoomla', 'AcymJFormField');
    } else {
        require_once __DIR__.DIRECTORY_SEPARATOR.'AcymJFormFieldWordPress.php';
        class_alias('AcymJFormFieldWordPress', 'AcymJFormField');
    }
}
