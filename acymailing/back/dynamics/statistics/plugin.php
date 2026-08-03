<?php
defined('ABSPATH') || die('Restricted Access');

use AcyMailing\Core\AcymPlugin;

require_once __DIR__.DIRECTORY_SEPARATOR.'StatisticsAutomationFilters.php';

class plgAcymStatistics extends AcymPlugin
{
    use StatisticsAutomationFilters;
}
