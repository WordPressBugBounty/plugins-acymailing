<?php

namespace AcyMailing\Helpers;

use AcyMailing\Core\AcymObject;

class UpdatemeHelper extends AcymObject
{
    public static function getDefaultHeaders(): array
    {
        $config = acym_config();
        $apiKey = $config->get('license_key', '');

        return [
            'Content-Type' => 'application/json',
            'API-KEY' => $apiKey,
        ];
    }

    public static function call(string $path, string $method = 'GET', array $data = [], array $headers = [], array $options = []): array
    {
        $url = ACYM_UPDATEME_API_URL.$path;

        $headers = $headers + self::getDefaultHeaders();

        if (ACYM_CMS === 'joomla' && acym_getCMSConfig('proxy_enable', false)) {
            $options['proxy'] = ['host' => acym_getCMSConfig('proxy_host', '').':'.acym_getCMSConfig('proxy_port', '')];
            if (!empty(acym_getCMSConfig('proxy_user', '')) && !empty(acym_getCMSConfig('proxy_pass', ''))) {
                $options['proxy']['auth'] = acym_getCMSConfig('proxy_user', '').':'.acym_getCMSConfig('proxy_pass', '');
            }
        }
        $options['verifySsl'] = false;
        $options['headers'] = $headers;
        $options['method'] = $method;
        $options['data'] = $data;
        $request = acym_makeCurlCall($url, $options);

        if (!empty($request['error'])) {
            acym_logError('Error while calling our API on path '.$path.' with the message: '.$request['error'], 'updateme');

            return [];
        }

        $return = $request;
        $return['success'] = true;

        if ($request['status_code'] < 200 || $request['status_code'] > 299) {
            acym_logError('Error while calling updateme on path '.$path.' with the status code: '.$request['status_code']."\r\n and body".json_encode($request), 'updateme');
            $return['success'] = false;
        }

        return $return;
    }

    public static function getLicenseInfo(bool $ajax): string
    {
        ob_start();
        $config = acym_config();
        $url = 'public/getLicenseInfo';
        $url .= '?level='.urlencode(strtolower($config->get('level', 'starter')));
        if (acym_level(ACYM_ESSENTIAL)) {
            if ($config->get('different_admin_url_toggle', 0) === 1) {
                $url .= '&domain='.$config->get('different_admin_url_value', 0);
            } else {
                $url .= '&domain='.urlencode(rtrim(ACYM_LIVE, '/'));
            }
        }
        $url .= '&version=latest';
        $userInformation = self::call($url);
        $warnings = ob_get_clean();
        $result = (!empty($warnings) && acym_isDebug()) ? $warnings : '';

        if (empty($userInformation)) {
            $config->save(['lastlicensecheck' => time()]);
            if ($ajax) {
                acym_sendAjaxResponse(
                    '',
                    [
                        'content' => '<br/><span style="color:#C10000;">'.acym_translation('ACYM_ERROR_LOAD_FROM_ACYBA').'</span><br/>'.$result,
                        'lastcheck' => acym_date(time(), 'Y/m/d H:i'),
                    ],
                    false
                );
            } else {
                return '';
            }
        }

        $newConfig = new \stdClass();

        $newConfig->latestversion = $userInformation['latestversion'];
        $newConfig->expirationdate = $userInformation['expiration'];
        $newConfig->lastlicensecheck = time();
        $newConfig->isTrial = empty($userInformation['isTrial']) ? 0 : 1;
        $config->save($newConfig);

        acym_checkPluginsVersion();

        return $newConfig->lastlicensecheck;
    }
}
