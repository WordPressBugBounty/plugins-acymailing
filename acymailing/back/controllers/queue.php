<?php

namespace AcyMailing\Controllers;

use AcyMailing\Classes\QueueClass;
use AcyMailing\Helpers\QueueHelper;
use AcyMailing\Libraries\acymController;
use AcyMailing\Controllers\Queue\Campaigns;
use AcyMailing\Controllers\Queue\Scheduled;
use AcyMailing\Controllers\Queue\Detailed;

class QueueController extends acymController
{
    use Campaigns;
    use Scheduled;
    use Detailed;

    public function __construct()
    {
        parent::__construct();
        $this->breadcrumb[acym_translation('ACYM_QUEUE')] = acym_completeLink('queue');
        $this->setDefaultTask('campaigns');
    }

    public function scheduleReady()
    {
        $queueClass = new QueueClass();
        $queueClass->scheduleReady();
    }

    public function continuesend()
    {
        if ($this->config->get('queue_type') == 'onlyauto') {
            acym_setNoTemplate();
            acym_display(acym_translation('ACYM_ONLYAUTOPROCESS'), 'warning');

            exit;
        }

        $newcrontime = time() + 120;
        if ($this->config->get('cron_next') < $newcrontime) {
            $newValue = new \stdClass();
            $newValue->cron_next = $newcrontime;
            $this->config->save($newValue);
        }

        $mailid = acym_getCID('id');

        $totalSend = acym_getVar('int', 'totalsend', 0);
        if (empty($totalSend)) {
            $query = 'SELECT COUNT(queue.user_id) FROM #__acym_queue AS queue LEFT JOIN #__acym_campaign AS campaign ON queue.mail_id = campaign.mail_id WHERE (campaign.id IS NULL OR campaign.active = 1) AND queue.sending_date < '.acym_escapeDB(
                    acym_date('now', 'Y-m-d H:i:s', false)
                );
            if (!empty($mailid)) {
                $query .= ' AND queue.mail_id = '.intval($mailid);
            }
            $totalSend = acym_loadResult($query);
        }

        $alreadySent = acym_getVar('int', 'alreadysent', 0);

        $helperQueue = new QueueHelper();
        $helperQueue->id = $mailid;
        $helperQueue->report = true;
        $helperQueue->total = $totalSend;
        $helperQueue->start = $alreadySent;
        $helperQueue->pause = $this->config->get('queue_pause');
        $helperQueue->fromManual = true;
        $helperQueue->process();

        acym_setNoTemplate();
        exit;
    }
}
