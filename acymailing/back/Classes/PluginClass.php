<?php

namespace AcyMailing\Classes;

use AcyMailing\Core\AcymClass;

class PluginClass extends AcymClass
{
    private array $plugins;

    public function __construct()
    {
        parent::__construct();

        $this->table = 'plugin';
        $this->pkey = 'id';

        global $acymPluginByFolderName;
        if (empty($acymPluginByFolderName)) {
            $acymPluginByFolderName = $this->getAll('folder_name');
        }

        $this->plugins = $acymPluginByFolderName;
    }

    public function getPlugins(): array
    {
        return $this->plugins;
    }

    public function getOnePluginByFolderName(string $folderName): ?object
    {
        return $this->plugins[$folderName] ?? null;
    }

    public function getNotUptoDatePlugins(): array
    {
        $result = acym_loadResult('SHOW TABLES LIKE "%_acym_plugin"');
        if (empty($result)) {
            return [];
        }

        return acym_loadResultArray('SELECT folder_name FROM #__acym_plugin WHERE uptodate = 0');
    }

    public function addIntegrationIfMissing(object $plugin): void
    {
        if (empty($plugin->pluginDescription->name)) {
            return;
        }

        $data = $this->plugins[$plugin->name] ?? null;

        $newPlugin = new \stdClass();
        $newPlugin->title = $plugin->pluginDescription->name;
        $newPlugin->folder_name = $plugin->name;
        $newPlugin->version = '1.0';
        $newPlugin->active = 1;
        $newPlugin->category = $plugin->pluginDescription->category;
        $newPlugin->level = 'starter';
        $newPlugin->uptodate = 1;
        $newPlugin->description = $plugin->pluginDescription->description;
        $newPlugin->latest_version = '1.0';
        $newPlugin->type = 'PLUGIN';

        if (!empty($data)) {
            $newPlugin->id = $data->id;
            $newPlugin->settings = $data->settings;

            if ($data->type !== 'ADDON') {
                return;
            }


            if (file_exists(ACYM_ADDONS_FOLDER_PATH.$plugin->name)) {
                acym_deleteFolder(ACYM_ADDONS_FOLDER_PATH.$plugin->name);
            }
        }

        $this->save($newPlugin);
    }

    public function enable(string $folderName): void
    {
        $plugin = $this->getOnePluginByFolderName($folderName);
        if (empty($plugin)) return;

        $plugin->active = 1;
        $this->save($plugin);
    }

    public function disable(string $folderName): void
    {
        $plugin = $this->getOnePluginByFolderName($folderName);
        if (empty($plugin)) return;

        $plugin->active = 0;
        $this->save($plugin);
    }

    public function deleteByFolderName(string $folderName): void
    {
        $plugin = $this->getOnePluginByFolderName($folderName);
        if (empty($plugin)) return;

        parent::delete($plugin->id);
    }

    public function updateAddon(string $addon): ?int
    {
        $plugin = $this->getOnePluginByFolderName($addon);

        if (empty($plugin)) {
            return null;
        }

        $pluginClass = new PluginClass();
        $pluginClass->downloadAddon($addon);

        $pluginToSave = new \stdClass();
        $pluginToSave->id = $plugin->id;
        $pluginToSave->version = $plugin->latest_version;
        $pluginToSave->uptodate = 1;

        return (int)$this->save($pluginToSave);
    }

    public function downloadAddon(string $name, bool $ajax = true): ?string
    {

        return null;
    }

    private function handleError(string $error, bool $ajax): string
    {
        if ($ajax) {
            acym_sendAjaxResponse(acym_translation($error), [], false);
        }

        return acym_translation($error);
    }
}
