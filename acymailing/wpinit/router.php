<?php

namespace AcyMailing\Init;

use AcyMailing\Classes\PluginClass;

class acyRouter
{
    var $activation;

    public function __construct()
    {
        add_action('wp_ajax_acymailing_router', [$this, 'router']);
        if (!acym_isAdmin()) {
            add_action('wp_loaded', [$this, 'frontRouter']);
        }

        $pages = [
            'automation',
            'bounces',
            'campaigns',
            'configuration',
            'dashboard',
            'dynamics',
            'fields',
            'file',
            'followups',
            'forms',
            'language',
            'lists',
            'mails',
            'override',
            'queue',
            'segments',
            'stats',
            'users',
        ];
        $headerPages = [
            'automation',
            'bounces',
            'campaigns',
            'configuration',
            'dashboard',
            'fields',
            'followups',
            'forms',
            'lists',
            'mails',
            'override',
            'queue',
            'segments',
            'stats',
            'users',
        ];
        foreach ($pages as $page) {
            if (in_array($page, $headerPages)) {
                add_action('load-acymailing_page_acymailing_'.$page, [$this, 'waitHeaders']);
            }
            add_action('admin_print_scripts-acymailing_page_acymailing_'.$page, [$this, 'disableJsBreakingPages']);
            add_action('admin_print_styles-acymailing_page_acymailing_'.$page, [$this, 'removeCssBreakingPages']);
        }
        add_action('admin_print_scripts-toplevel_page_acymailing_dashboard', [$this, 'disableJsBreakingPages']);
        add_action('admin_print_styles-toplevel_page_acymailing_dashboard', [$this, 'removeCssBreakingPages']);
        add_action('wp_enqueue_media', [$this, 'protectAcyMailingPages'], 100);
        add_action('init', [$this, 'autologin']);
    }

    public function protectAcyMailingPages()
    {
        $page = isset($_REQUEST['page']) ? sanitize_text_field(wp_unslash($_REQUEST['page'])) : '';
        if (empty($page) || strpos($page, 'acymailing_') === false) return;

        wp_dequeue_script('responsive-lightbox-admin-select2');
        wp_dequeue_style('responsive-lightbox-admin-select2');

        wp_dequeue_script('dsm-select-two');
        wp_dequeue_style('dsm-select-two');
    }

    public function waitHeaders()
    {
        ob_start();
    }

    public function disableJsBreakingPages()
    {
        remove_action('admin_print_scripts', 'print_emoji_detection_script');

        remove_action('wp_enqueue_media', '\\Slideshowck\\Tinymce\\register_scripts_styles');

        remove_action('wp_enqueue_media', '\\Sgdg\\Admin\\TinyMCE\\register_scripts_styles');

        wp_dequeue_script('select2.js');

        wp_dequeue_script('checkout_fields_js');

        wp_dequeue_script('wp-optimize-minify-admin-purge');

        wp_dequeue_script('happy-elementor-addons-select2');
        wp_dequeue_script('select2');
    }

    public function removeCssBreakingPages()
    {
        wp_dequeue_style('saswp-main-css');
        wp_dequeue_style('WP REST API Controller');
        wp_dequeue_style('wpml-select-2');
        wp_dequeue_style('swcfpc_admin_css');

        wp_dequeue_style('happy-elementor-addons-select2');
        wp_dequeue_style('select2');

        wp_dequeue_style('qlwapp-admin-menu');
    }

    public function frontRouter()
    {
        $page = acym_getVar('string', 'page');
        if (empty($page) || $page !== ACYM_COMPONENT.'_front') return;

        $this->router(true);
    }

    public function router($front = false)
    {
        displayFreeTrialMessage();


        if (!$front) auth_redirect();

        $acyActivation = new acyActivation();
        if (is_multisite()) {
            $currentBlog = get_current_blog_id();
            $sites = function_exists('get_sites') ? get_sites() : wp_get_sites();

            foreach ($sites as $site) {
                if (is_object($site)) {
                    $site = get_object_vars($site);
                }
                switch_to_blog($site['blog_id']);
                acym_config(true);
                $acyActivation->updateAcym();
            }

            switch_to_blog($currentBlog);
        } else {
            $acyActivation->updateAcym();
        }

        if (file_exists(ACYM_FOLDER.'update.php')) {
            unlink(ACYM_FOLDER.'update.php');
        }

        $config = acym_config(true);

        $ctrl = acym_getVar('cmd', 'ctrl', '');
        $task = acym_getVar('cmd', 'task', '');

        if (!$front && acym_isAdmin() && file_exists(ACYM_NEW_FEATURES_SPLASHSCREEN_JSON) && is_writable(ACYM_NEW_FEATURES_SPLASHSCREEN_JSON)) {
            $ctrl = 'dashboard';
            $task = 'features';
            acym_setVar('ctrl', $ctrl);
            acym_setVar('task', $task);
        }

        $needToMigrate = $config->get('migration') == 0 && acym_existsAcyMailing59() && acym_getVar('string', 'task') !== 'migrationDone';
        $forceDashboard = !$front && ($needToMigrate || $config->get('walk_through', 0) == 1) && !(defined('DOING_AJAX') && DOING_AJAX) && 'dynamics' !== $ctrl;
        if ($forceDashboard) {
            $ctrl = 'dashboard';
            acym_setVar('ctrl', $ctrl);
        }

        if (empty($ctrl)) {
            $ctrl = str_replace(ACYM_COMPONENT.'_', '', acym_getVar('cmd', 'page', ''));

            if (empty($ctrl)) {
                echo acym_translation('ACYM_PAGE_NOT_FOUND');

                $noCache = acym_getVar('int', 'nocache', 0);
                if (!empty($noCache)) {
                    acym_redirect(acym_rootURI());
                }

                return;
            }

            acym_setVar('ctrl', $ctrl);
        }

        $this->deactivateHookAdminFooter();
        $subNamespace = $front ? 'Front' : '';
        $controllerNamespace = 'AcyMailing\\'.$subNamespace.'Controllers\\'.ucfirst($ctrl).'Controller';

        if (!class_exists($controllerNamespace)) {
            echo acym_translation('ACYM_PAGE_NOT_FOUND').': '.$ctrl;

            return;
        }

        if (!$front && acym_isAdmin() && $task != 'edit' && !(defined('DOING_AJAX') && DOING_AJAX)) {
            $pluginClass = new PluginClass();
            $installedPlugins = $pluginClass->getAll('title');
            $newPlugins = json_decode(ACYM_AVAILABLE_PLUGINS);
            foreach ($newPlugins as $onePlugin) {
                if (empty($installedPlugins[$onePlugin->name])) continue;
                if ($installedPlugins[$onePlugin->name]->type !== 'ADDON') continue;

                acym_enqueueMessage(
                    acym_translationSprintf(
                        'ACYM_NEW_PLUGIN_FORMAT',
                        $onePlugin->name,
                        '<a target="_blank" style="color: #00a5ff;" href="'.$onePlugin->downloadlink.'">'.acym_translation('ACYM_CLICK_HERE').'</a>'
                    ),
                    'error'
                );
            }
        }

        $controller = new $controllerNamespace();
        if (empty($task)) {
            $task = acym_getVar('cmd', 'defaulttask', $controller->defaulttask);
        }

        if ($forceDashboard && !method_exists($controller, $task)) {
            $task = 'listing';
            acym_setVar('task', $task);
        }

        $controller->call($task);
    }

    public function autologin()
    {
        $subId = acym_getVar('int', 'autoSubId');
        $subKey = acym_getVar('string', 'subKey');

        if (empty($subId) || empty($subKey)) {
            return;
        }

        $currentUrl = acym_currentURL();
        $cleanedUrl = acym_cleanUrl($currentUrl, ['autoSubId', 'subKey']);

        $config = acym_config();
        if ($config->get('autologin_urls', 0) == 0) {
            acym_redirect($cleanedUrl);
        }

        $cmsId = acym_loadResult('SELECT `cms_id` FROM #__acym_user WHERE `id` = '.intval($subId).' AND `key` = '.acym_escapeDB($subKey));
        if (empty($cmsId) || $cmsId === acym_currentUserId()) {
            acym_redirect($cleanedUrl);
        }

        $user = get_user_by('id', $cmsId);

        if (!is_wp_error($user)) {
            wp_clear_auth_cookie();
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID);

            acym_redirect($cleanedUrl);
        }
    }

    private function deactivateHookAdminFooter()
    {
        remove_action('admin_footer', 'Freemius::_enrich_ajax_url');
        remove_action('admin_footer', 'Freemius::_open_support_forum_in_new_page');
    }
}

$acyRouter = new acyRouter();
