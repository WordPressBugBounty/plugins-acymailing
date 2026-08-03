<?php

namespace AcyMailing\Controllers\Dashboard;

use AcyMailing\Helpers\MigrationHelper;
use AcyMailing\Helpers\UpdateHelper;

trait Migration
{
    public function preMigration(): void
    {
        wp_verify_nonce(acym_getVar('cmd', '_wpnonce'), 'acymnonce') || die('Invalid Token');

        if (!acym_isAllowed('configuration') || !acym_existsAcyMailing59()) {
            return;
        }

        $elementToMigrate = acym_getVar('string', 'element', '');
        $helperMigration = new MigrationHelper();

        $result = $helperMigration->preMigration($elementToMigrate);

        if (!empty($result['isOk'])) {
            echo esc_html($result['count']);
        } else {
            $this->errorHandling($result);
        }
        exit;
    }

    public function migrate(): void
    {
        wp_verify_nonce(acym_getVar('cmd', '_wpnonce'), 'acymnonce') || die('Invalid Token');

        if (!acym_isAllowed('configuration')) {
            return;
        }

        $elementToMigrate = acym_getVar('string', 'element', '');
        $helperMigration = new MigrationHelper();
        $functionName = 'do'.ucfirst($elementToMigrate).'Migration';

        if (!is_callable([$helperMigration, $functionName])) {
            echo 'ERROR : '.esc_html(acym_translation('ACYM_NON_EXISTING_PAGE'));
            exit;
        }

        $result = $helperMigration->$functionName($elementToMigrate);

        if (!empty($result['isOk'])) {
            echo json_encode($result);
        } else {
            $this->errorHandling($result);
        }
        exit;
    }

    public function migrationDone(): void
    {
        wp_verify_nonce(acym_getVar('cmd', '_wpnonce'), 'acymnonce') || die('Invalid Token');

        if (!acym_isAllowed('configuration')) {
            return;
        }

        $this->config->saveConfig(['migration' => 1]);

        $updateHelper = new UpdateHelper();
        $updateHelper->installNotifications();
        $updateHelper->installTemplates();
        $updateHelper->installOverrideEmails();

        $this->listing();
    }

    private function errorHandling(array $result): void
    {
        echo 'ERROR : ';
        if (!empty($result['errorInsert'])) {
            echo esc_html(strtoupper(acym_translation('ACYM_INSERT_ERROR')));
        }
        if (!empty($result['errorClean'])) {
            echo esc_html(strtoupper(acym_translation('ACYM_CLEAN_ERROR')));
        }

        if (!empty($result['errors'])) {
            echo '<br>';

            foreach ($result['errors'] as $key => $oneError) {
                echo '<br>'.esc_html($key.' : '.$oneError);
            }
        }
    }

    private function migration(): bool
    {
        if (!acym_isAllowed('configuration')) {
            return false;
        }

        if ($this->config->get('migration') == 0 && acym_existsAcyMailing59()) {
            acym_setVar('layout', 'migrate');
            parent::display();

            return true;
        }

        $this->config->saveConfig(['migration' => 1]);

        return false;
    }
}
