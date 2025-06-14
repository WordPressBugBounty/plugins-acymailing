<?php

namespace AcyMailing\Helpers;

use AcyMailing\Classes\PluginClass;
use AcyMailing\Core\AcymObject;

class UpdateHelper extends AcymObject
{
    use Update\Cms;
    use Update\Configuration;
    use Update\DefaultData;
    use Update\SQLPatch;
    use Update\Patchv6;
    use Update\Patchv7;
    use Update\Patchv8;
    use Update\Patchv9;
    use Update\Patchv10;

    const FIRST_EMAIL_NAME_KEY = 'ACYM_FIRST_EMAIL_NAME';
    const BOUNCE_VERSION = 5;

    private string $level = 'starter';
    private string $version = '10.3.0';
    private string $previousVersion;
    private bool $isUpdating = false;

    public bool $firstInstallation = true;

    public function deleteNewSplashScreenInstall(): void
    {
        if (!$this->isUpdating || (!empty($this->previousVersion) && version_compare($this->previousVersion, $this->version, '='))) {
            $splashscreenJson = ACYM_PARTIAL.'update'.DS.'changelogs_splashscreen.json';

            if (file_exists($splashscreenJson)) {
                @unlink($splashscreenJson);
            }
        }
    }

    public function updateAddons(): void
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
