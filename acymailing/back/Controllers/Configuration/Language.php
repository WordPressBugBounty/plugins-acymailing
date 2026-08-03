<?php

namespace AcyMailing\Controllers\Configuration;

trait Language
{
    public function multilingual(): void
    {
        wp_verify_nonce(acym_getVar('cmd', '_wpnonce'), 'acymnonce') || die('Invalid Token');

        $remindme = json_decode($this->config->get('remindme', '[]'), true);
        $remindme[] = 'multilingual';
        $this->config->saveConfig(['remindme' => json_encode($remindme)]);

        $this->listing();
    }
}
