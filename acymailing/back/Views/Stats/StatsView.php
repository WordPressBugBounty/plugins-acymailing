<?php

namespace AcyMailing\Views\Stats;

use AcyMailing\Core\AcymView;

class StatsView extends AcymView
{
    public function __construct()
    {
        parent::__construct();

        $this->tabs = [
            'globalStats' => 'ACYM_OVERVIEW',
        ];
    }

    public function isMailSelected($mailId, $clickMap)
    {

        $this->config->save(['mail_stats_checked_once' => 1]);
    }
}
