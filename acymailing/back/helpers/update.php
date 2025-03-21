<?php

namespace AcyMailing\Helpers;

use AcyMailing\Classes\PluginClass;
use AcyMailing\Libraries\acymObject;

class UpdateHelper extends acymObject
{
    use Update\Cms;
    use Update\Configuration;
    use Update\DefaultData;
    use Update\SQLPatch;
    use Update\Patchv6;
    use Update\Patchv7;
    use Update\Patchv8;
    use Update\Patchv9;

    const FIRST_EMAIL_NAME_KEY = 'ACYM_FIRST_EMAIL_NAME';
    const BOUNCE_VERSION = 5;

    private $level = 'starter';
    private $version = '9.11.1';
    private $previousVersion;
    private $isUpdating = false;

    public $firstInstallation = true;

    public function deleteNewSplashScreenInstall()
    {
        if (!$this->isUpdating || (!empty($this->previousVersion) && version_compare($this->previousVersion, $this->version, '='))) {
            $splashscreenJson = ACYM_BACK.'partial'.DS.'update'.DS.'changelogs_splashscreen.json';

            if (file_exists($splashscreenJson)) {
                @unlink($splashscreenJson);
            }
        }
    }

    public function updateAddons()
    {
        acym_checkPluginsVersion();

        $pluginClass = new PluginClass();
        $pluginsToUpdate = $pluginClass->getNotUptoDatePlugins();
        foreach ($pluginsToUpdate as $onePlugin) {
            $pluginClass->updateAddon($onePlugin);
        }
    }

    private function updateQuery(string $query, string $messageType = 'enqueue'): bool
    {
        try {
            $res = acym_query($query);
        } catch (\Exception $e) {
            $res = null;
        }

        if ($res === null) {
            $message = isset($e) ? $e->getMessage() : substr(strip_tags(acym_getDBError()), 0, 200).'...';

            if ($messageType === 'enqueue') {
                acym_enqueueMessage($message, 'error');
            } elseif ($messageType === 'display') {
                acym_display($message, 'error');
            }

            return false;
        }

        return true;
    }
}
