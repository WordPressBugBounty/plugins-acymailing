<?php

namespace AcyMailing\Controllers;

use AcyMailing\Core\AcymController;
use AcyMailing\Controllers\Segments\Listing;
use AcyMailing\Controllers\Segments\Edition;
use AcyMailing\Controllers\Segments\Campaign;

class SegmentsController extends AcymController
{
    use Listing;
    use Edition;
    use Campaign;

    public const FLAG_USERS = -1;
    public const FLAG_EXPORT_USERS = -2;
    public const FLAG_COUNT = -3;

    public function __construct()
    {
        parent::__construct();

        $this->breadcrumb[acym_translation('ACYM_SEGMENTS')] = acym_completeLink('segments');
        $this->loadScripts = [
            'edit' => ['datepicker', 'vue-applications' => ['modal_users_summary']],
        ];
    }
}
