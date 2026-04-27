<?php

namespace AcyMailing\WpInit;

use AcyMailing\Controllers\ConfigurationController;
use AcyMailing\Helpers\CronHelper;

class Cron
{
    public function __construct()
    {
        add_filter('cron_schedules', [$this, 'addCronIntervals'], 100);
        add_action(ConfigurationController::CRON_TASK_NAME, [$this, 'triggerAutomatedTasks']);
    }

    public function addCronIntervals(array $schedules): array
    {
        $schedules['every_minute'] = [
            'interval' => 60,
            'display' => 'Every minute',
        ];

        return $schedules;
    }

    public function triggerAutomatedTasks()
    {

        if (!acym_level(ACYM_ESSENTIAL)) {
            acym_deleteScheduledTask(['name' => ConfigurationController::CRON_TASK_NAME]);

            return;
        }

        if (!acym_isLicenseValidWeekly() && (empty($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'api.acymailing.com') === false)) {
            acym_deleteScheduledTask(['name' => ConfigurationController::CRON_TASK_NAME]);

            return;
        }

        $cronHelper = new CronHelper();
        $cronHelper->cron();
    }
}
