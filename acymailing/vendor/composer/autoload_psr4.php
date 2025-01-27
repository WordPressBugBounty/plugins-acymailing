<?php


$vendorDir = dirname(__DIR__);
$baseDir = dirname($vendorDir);

return array(
    'ZBateson\\StreamDecorators\\' => array($vendorDir . '/zbateson/stream-decorators/src'),
    'ZBateson\\MbWrapper\\' => array($vendorDir . '/zbateson/mb-wrapper/src'),
    'ZBateson\\MailMimeParser\\' => array($vendorDir . '/zbateson/mail-mime-parser/src'),
    'Symfony\\Polyfill\\Mbstring\\' => array($vendorDir . '/symfony/polyfill-mbstring'),
    'Symfony\\Polyfill\\Iconv\\' => array($vendorDir . '/symfony/polyfill-iconv'),
    'Symfony\\Component\\CssSelector\\' => array($baseDir . '/back/Libraries/Symfony/CssSelector'),
    'Sabberworm\\CSS\\' => array($baseDir . '/back/Libraries/Sabberworm/CSS/src'),
    'Psr\\Http\\Message\\' => array($vendorDir . '/psr/http-factory/src', $vendorDir . '/psr/http-message/src'),
    'Psr\\Container\\' => array($vendorDir . '/psr/container/src'),
    'Pelago\\Emogrifier\\' => array($baseDir . '/back/Libraries/Pelago/Emogrifier/src'),
    'Javanile\\Imap2\\' => array($vendorDir . '/javanile/php-imap2/src'),
    'GuzzleHttp\\Psr7\\' => array($vendorDir . '/guzzlehttp/psr7/src'),
    'AcyMailing\\WpInit\\' => array($baseDir . '/WpInit'),
    'AcyMailing\\' => array($baseDir . '/back', $baseDir . '/front'),
    'AcyMailerPhp\\' => array($baseDir . '/back/Libraries/Mailer'),
);
