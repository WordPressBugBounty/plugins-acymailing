<?php

namespace AcyMailing\Views\Queue;

use AcyMailing\Core\AcymView;

class QueueView extends AcymView
{
    public function __construct()
    {
        parent::__construct();

        $this->steps = [
            'campaigns' => 'ACYM_MAILS',
        ];


        $this->steps['detailed'] = 'ACYM_QUEUE_DETAILED';
    }
}
