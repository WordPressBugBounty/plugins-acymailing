<?php

function acym_route($url, $xhtml = true, $ssl = null)
{
    return acym_baseURI().$url;
}

function acym_addPageParam($url, $ajax = false, $front = false)
{
    preg_match('#^([a-z]+)(?:[^a-z]|$)#Uis', $url, $ctrl);

    if ($front) {
        $link = 'index.php?page='.ACYM_COMPONENT.'_front&ctrl='.$url;
        if ($ajax) $link .= '&'.acym_noTemplate();
    } else {
        $link = 'admin.php?page='.ACYM_COMPONENT.'_'.$ctrl[1].'&ctrl='.$url;
        if ($ajax) {
            $link .= '&action='.ACYM_COMPONENT.'_router&'.acym_noTemplate();
        }
    }

    return $link;
}

function acym_baseURI($pathonly = false)
{
    if (acym_isAdmin()) {
        return admin_url();
    }

    return acym_rootURI();
}

function acym_rootURI($pathonly = false, $path = 'siteurl')
{
    $rootURI = rtrim(site_url(), '/').'/';

    if (!acym_isAdmin()) {
        $wpmlSiteUrl = apply_filters('wpml_home_url', $rootURI);
        if ($wpmlSiteUrl !== $rootURI) {
            $rootURI = rtrim($wpmlSiteUrl, '/').'/';
        }
    }

    if (defined('CONTENT_DIR') && substr($rootURI, -3) === 'wp/') {
        $rootURI = substr($rootURI, 0, -3);
    }

    return $rootURI;
}

function acym_completeLink($link, $popup = false, $redirect = false, $forceNoPopup = false)
{
    if (($popup || acym_isNoTemplate()) && $forceNoPopup == false) {
        $link .= '&'.acym_noTemplate();
    }

    $link = acym_addPageParam($link);

    return acym_route($link);
}

function acym_prepareAjaxURL($url)
{
    return htmlspecialchars_decode(acym_route(acym_addPageParam($url, true)));
}

function acym_frontendLink($link, $complete = true, $sef = true)
{
    return acym_rootURI().acym_addPageParam($link, true, true);
}

function acym_backendLink(string $link): string
{
    return admin_url().acym_addPageParam($link);
}

function acym_getMenu()
{
    return get_post();
}
