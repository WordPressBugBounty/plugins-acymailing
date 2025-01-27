<?php

namespace AcyMailing\Core;

use AcyMailing\Classes\ConfigurationClass;

class AcymObject
{
    public ConfigurationClass $config;

    public function __construct()
    {
        $this->config = ConfigurationClass::class === get_class($this) ? $this : acym_config();
    }
}
