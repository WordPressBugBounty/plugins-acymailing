<?php

namespace AcyMailing\Classes;

use AcyMailing\Controllers\ConfigurationController;
use AcyMailing\Core\AcymClass;

class ConfigurationClass extends AcymClass
{
    private array $values = [];

    public function __construct()
    {
        parent::__construct();

        $this->table = 'configuration';
        $this->pkey = 'name';
    }

    public function load(): void
    {
        $this->values = acym_loadObjectList('SELECT * FROM #__acym_configuration', 'name');
    }

    public function get(string $namekey, $default = '')
    {
        if (isset($this->values[$namekey])) {
            return $this->values[$namekey]->value;
        }

        return $default;
    }

    public function save($element): ?int
    {
        $this->saveConfig((array)$element);

        return null;
    }

    public function saveConfig(array $newConfig, bool $escape = true): bool
    {
        $oldFollowupPriority = $this->get('followup_max_priority', 0) == 1;

        $previousCronSecurity = $this->get('cron_security', 0);
        $previousCronSecurityKey = $this->get('cron_key');
        $params = [];
        foreach ($newConfig as $name => $value) {
            if (!empty($value)) {
                if (strpos($name, 'password') !== false && trim($value, '*') === '') {
                    continue;
                }
                if (strpos($name, 'key') !== false && strpos($value, '**********') !== false) {
                    continue;
                }
            }

            if ($name === 'multilingual' && $value === '1') {
                $remindme = json_decode($this->get('remindme', '[]'), true);
                if (!in_array('multilingual', $remindme)) {
                    $remindme[] = 'multilingual';
                    $params[] = '("remindme",'.acym_escapeDB(json_encode($remindme)).')';
                }
            }

            if (is_array($value)) {
                $value = implode(',', $value);
            }

            if (empty($this->values[$name])) {
                $this->values[$name] = new \stdClass();
            }
            $this->values[$name]->value = $value;

            if ($escape && !is_null($value)) {
                $params[] = '('.acym_escapeDB(strip_tags($name)).','.acym_escapeDB(strip_tags($value)).')';
            } else {
                $params[] = '('.acym_escapeDB($name).','.acym_escapeDB($value).')';
            }
        }

        $activeCron = $this->get('active_cron', 0);
        $newCronSecurity = $this->get('cron_security', 0);
        $newCronSecurityKey = $this->get('cron_key');
        if (!empty($activeCron) && !empty($newCronSecurity) && (empty($previousCronSecurity) || $previousCronSecurityKey !== $newCronSecurityKey)) {
            $configurationController = new ConfigurationController();
            $deactivationResult = $configurationController->modifyCron('deactivateCron');
            if (!empty($deactivationResult)) {
                $configurationController->modifyCron('activateCron');
            }
        }

        if (empty($params)) {
            return true;
        }

        try {
            $status = acym_query('REPLACE INTO #__acym_configuration (`name`, `value`) VALUES '.implode(',', $params));
        } catch (\Exception $e) {
            $status = false;
        }

        if ($status === false) {
            acym_display(isset($e) ? $e->getMessage() : substr(strip_tags(acym_getDBError()), 0, 200).'...', 'error');
        }

        $newFollowupPriority = $this->get('followup_max_priority', 0) == 1;

        $mailClass = new MailClass();
        $mailClass->updateFollowupPriority($oldFollowupPriority, $newFollowupPriority);

        return (bool)$status;
    }
}
