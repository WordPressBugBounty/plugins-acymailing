<?php

use AcyMailing\Core\AcymPlugin;

class SendinblueClass extends AcymPlugin
{
    protected array $headers;
    public plgAcymSendinblue $plugin;

    public function __construct(&$plugin, $headers = [])
    {
        parent::__construct();
        $this->plugin = &$plugin;
        $this->headers = $headers;
    }

    protected function callApiSendingMethod(string $url, array $data = [], array $headers = [], string $type = 'GET', array $authentication = [], bool $dataDecoded = false): array
    {
        $response = parent::callApiSendingMethod(plgAcymSendinblue::SENDING_METHOD_API_URL.$url, $data, $headers, $type, $authentication, $dataDecoded);

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        if (!empty($response['error_curl'])) {
            if (!$backtrace[0]['file'] && !empty($backtrace[1]['function'])) {
                $this->plugin->errors[] = $backtrace[0]['file'].': '.$backtrace[1]['function'];
            }
            acym_logError('Error calling the URL '.$url.': '.$response['error_curl'], 'sendinblue');
            $this->plugin->errors[] = $response['error_curl'];
        } elseif (!empty($response['message'])) {
            acym_logError('Error calling the URL '.$url.': '.$response['message'], 'sendinblue');

            if (strpos($response['message'], 'Contact already in list') === false) {
                if (!$backtrace[0]['file'] && !empty($backtrace[1]['function'])) {
                    $this->plugin->errors[] = $backtrace[0]['file'].': '.$backtrace[1]['function'];
                }
                $this->plugin->errors[] = $response['message'];
            }

            if (strpos($response['message'], 'Your account is under validation.') !== false) {
                $this->config->saveConfig(['sendinblue_validation' => 1]);
            }
        }

        return $response;
    }
}
