<?php

namespace AcyMailing\Helpers;

use AcyMailing\Classes\CampaignClass;
use AcyMailing\Classes\FieldClass;
use AcyMailing\Classes\FollowupClass;
use AcyMailing\Classes\ListClass;
use AcyMailing\Classes\MailboxClass;
use AcyMailing\Classes\MailClass;
use AcyMailing\Core\AcymObject;

class MigrationHelper extends AcymObject
{
    private array $errors = [];
    private array $result = [
        'isOk' => true,
        'errorInsert' => false,
        'errorClean' => false,
        'count' => 0,
    ];

    public function preMigration(string $element): array
    {
        $connection = [
            'config' => ['table' => 'config', 'where' => ''],
            'templates' => ['table' => 'template', 'where' => ''],
            'mails' => ['table' => 'mail', 'where' => ''],
            'lists' => ['table' => 'list', 'where' => ''],
            'users' => ['table' => 'subscriber', 'where' => ''],
            'bounce' => ['table' => 'rules', 'where' => ''],
            'distrib' => ['table' => 'action', 'where' => ''],
            'subscriptions' => ['table' => 'listsub', 'where' => ''],
            'mailhaslists' => ['table' => 'listmail', 'where' => ''],
            'mailstats' => ['table' => 'stats', 'where' => ''],
            'welcomeunsub' => ['table' => 'list', 'where' => 'unsubmailid IS NOT NULL OR welmailid IS NOT NULL'],
            'users_fields' => ['table' => 'subscriber'],
            'fields' => ['table' => 'fields', 'where' => 'namekey NOT IN (\'name\', \'html\', \'email\')'],
        ];

        $this->doCleanTable($element);

        if ('users_fields' === $element) {
            $fields = acym_loadResultArray(
                'SELECT namekey FROM #__acymailing_fields WHERE `namekey` NOT IN ("name", "email", "html") AND `type` NOT IN ("customtext", "category", "gravatar")'
            );
            $columnUserTable = acym_getColumns('acymailing_subscriber', false);

            $fieldToCheck = [];

            foreach ($fields as $key => $field) {
                if (in_array($field, $columnUserTable)) {
                    $fieldToCheck[$key] = '`'.$field.'`';
                }
            }

            $connection[$element]['where'] = implode(' IS NOT NULL OR ', $fieldToCheck);
            if (!empty($fieldToCheck)) {
                $connection[$element]['where'] .= ' IS NOT NULL;';
            }
        }

        $query = 'SELECT COUNT(*) FROM #__acymailing_'.$connection[$element]['table'];
        if (!empty($connection[$element]['where'])) {
            $query .= ' WHERE '.$connection[$element]['where'];
        }

        $this->result['count'] = acym_loadResult($query);

        return $this->result;
    }

    public function doConfigMigration(): array
    {
        $this->doElementMigration('config');

        return $this->result;
    }

    public function doUsers_fieldsMigration(): array
    {
        $this->doElementMigration('users_fields');

        return $this->result;
    }

    public function doMailStatsMigration(): array
    {
        $this->doElementMigration('MailStats');

        return $this->result;
    }

    public function doMailhaslistsMigration(): array
    {
        $this->doElementMigration('MailHasLists');

        return $this->result;
    }

    public function doTemplatesMigration(): array
    {
        $this->doElementMigration('templates');

        return $this->result;
    }

    public function doMailsMigration(): array
    {
        $this->doElementMigration('mails');

        return $this->result;
    }

    public function doFieldsMigration(): array
    {
        $this->doElementMigration('fields');

        return $this->result;
    }

    public function doListsMigration(): array
    {

        $this->doElementMigration('lists');

        return $this->result;
    }

    public function doUsersMigration(): array
    {

        $this->doElementMigration('users');

        return $this->result;
    }

    public function doSubscriptionsMigration(): array
    {
        $this->doElementMigration('subscriptions');

        return $this->result;
    }

    public function doWelcomeunsubMigration(): array
    {
        $this->doElementMigration('Welcomeunsub');

        return $this->result;
    }

    public function doBounceMigration(): array
    {
        $this->doElementMigration('bounce');

        return $this->result;
    }

    public function doDistribMigration(): array
    {
        $this->doElementMigration('distrib');

        return $this->result;
    }

    private function doCleanTable(string $tableName): void
    {
        $functionName = 'clean'.ucfirst($tableName).'Table';

        if (method_exists($this, $functionName) && !$this->$functionName()) {
            $this->result['isOk'] = false;
            $this->result['errorClean'] = true;
            $this->result['errors'] = $this->errors;
        }
    }

    private function doElementMigration(string $elementName): bool
    {
        $functionName = 'migrate'.ucfirst($elementName);

        $params = [
            'currentElement' => acym_getVar('int', 'currentElement'),
            'insertPerCalls' => acym_getVar('int', 'insertPerCalls'),
        ];

        $nbInsert = $this->$functionName($params);

        if ($nbInsert !== -1) {
            $this->result[$elementName] = $nbInsert;

            return true;
        } else {
            $this->result[$elementName] = false;
            $this->result['isOk'] = false;
            $this->result['errorInsert'] = true;
            $this->result['errors'] = $this->errors;

            return false;
        }
    }

    private function migrateConfig(array $params): int
    {
        $fieldsMatchMailSettings = [
            'add_names' => 'add_names',
            'bounce_email' => 'bounce_email',
            'charset' => 'charset',
            'dkim' => 'dkim',
            'dkim_domain' => 'dkim_domain',
            'dkim_identity' => 'dkim_identity',
            'dkim_passphrase' => 'dkim_passphrase',
            'dkim_private' => 'dkim_private',
            'dkim_public' => 'dkim_public',
            'dkim_selector' => 'dkim_selector',
            'elasticemail_password' => 'elasticemail_password',
            'elasticemail_port' => 'elasticemail_port',
            'elasticemail_username' => 'elasticemail_username',
            'embed_files' => 'embed_files',
            'embed_images' => 'embed_images',
            'encoding_format' => 'encoding_format',
            'from_email' => 'from_email',
            'from_name' => 'from_name',
            'mailer_method' => 'mailer_method',
            'multiple_part' => 'multiple_part',
            'reply_email' => 'replyto_email',
            'reply_name' => 'replyto_name',
            'sendmail_path' => 'sendmail_path',
            'smtp_auth' => 'smtp_auth',
            'smtp_host' => 'smtp_host',
            'smtp_keepalive' => 'smtp_keepalive',
            'smtp_password' => 'smtp_password',
            'smtp_port' => 'smtp_port',
            'smtp_secured' => 'smtp_secured',
            'smtp_username' => 'smtp_username',
            'special_chars' => 'special_chars',
        ];

        $fieldsMatchQueueProcess = [
            'cron_frequency' => 'cron_frequency',
            'cron_fromip' => 'cron_fromip',
            'cron_last' => 'cron_last',
            'cron_report' => 'cron_report',
            'cron_savereport' => 'cron_savereport',
            'cron_sendreport' => 'cron_sendreport',
            'cron_sendto' => 'cron_sendto',
            'queue_nbmail' => 'queue_nbmail',
            'queue_nbmail_auto' => 'queue_nbmail_auto',
            'queue_pause' => 'queue_pause',
            'queue_try' => 'queue_try',
            'queue_type' => 'queue_type',
        ];

        $fieldsMatchSubscription = [
            'require_confirmation' => 'require_confirmation',
        ];

        $fieldsMatchSecurity = [
            'allowedfiles' => 'allowed_files',
            'email_checkdomain' => 'email_checkdomain',
            'recaptcha_secretkey' => 'recaptcha_secretkey',
            'recaptcha_sitekey' => 'recaptcha_sitekey',
            'security_key' => 'security_key',
        ];

        $fieldsMatchNotUsed = [
            'allow_visitor' => 'allow_visitor',
            'confirm_redirect' => 'confirm_redirect',
            'confirmation_message' => 'confirmation_message',
            'cron_fullreport' => 'cron_fullreport',
            'cron_next' => 'cron_next',
            'css_backend' => 'css_backend',
            'css_frontend' => 'css_frontend',
            'forward' => 'forward',
            'hostname' => 'hostname',
            'notification_accept' => 'notification_accept',
            'notification_confirm' => 'notification_confirm',
            'notification_created' => 'notification_created',
            'notification_refuse' => 'notification_refuse',
            'notification_unsuball' => 'notification_unsuball',
            'priority_followup' => 'priority_followup',
            'priority_newsletter' => 'priority_newsletter',
            'subscription_message' => 'subscription_message',
            'unsub_message' => 'unsub_message',
            'unsub_reasons' => 'unsub_reasons',
            'unsub_redirect' => 'unsub_redirect',
            'use_sef' => 'use_sef',
            'welcome_message' => 'welcome_message',
            'word_wrapping' => 'word_wrapping',
        ];

        $fieldsMatchBounce = [
            'bounce_email' => 'bounce_email',
            'bounce_server' => 'bounce_server',
            'bounce_port' => 'bounce_port',
            'bounce_connection' => 'bounce_connection',
            'bounce_secured' => 'bounce_secured',
            'bounce_certif' => 'bounce_certif',
            'bounce_username' => 'bounce_username',
            'bounce_password' => 'bounce_password',
            'bounce_timeout' => 'bounce_timeout',
            'bounce_max' => 'bounce_max',
            'auto_bounce' => 'auto_bounce',
            'auto_bounce_frequency' => 'auto_bounce_frequency',
            'bounce_action_lists_maxtry' => 'bounce_action_lists_maxtry',
        ];

        $fieldsMatch = array_merge(
            $fieldsMatchMailSettings,
            $fieldsMatchQueueProcess,
            $fieldsMatchSubscription,
            $fieldsMatchSecurity,
            $fieldsMatchNotUsed,
            $fieldsMatchBounce
        );

        $dataPrevious = acym_loadObjectList(
            'SELECT `namekey`, `value` 
            FROM #__acymailing_config 
            WHERE `namekey` IN ("'.implode('","', array_keys($fieldsMatch)).'") 
            LIMIT '.intval($params['currentElement']).', '.intval($params['insertPerCalls'])
        );

        if (empty($dataPrevious)) {
            return 0;
        }

        $valuesToInsert = [];

        foreach ($dataPrevious as $value) {
            if (empty($fieldsMatch[$value->namekey])) {
                continue;
            }

            $value->namekey = $fieldsMatch[$value->namekey];

            switch ($value->namekey) {
                case 'queue_type':
                    switch ($value->value) {
                        case 'onlyauto':
                            $value->value = 'auto';
                            break;
                        case 'auto':
                            $value->value = 'automan';
                            break;
                    }
                    break;

                case 'mailer_method':
                    $sending_platform = $value->value == 'smtp' || $value->value === 'elasticemail' ? 'external' : 'server';
                    $valuesToInsert[] = '("sending_platform",'.acym_escapeDB($sending_platform).')';
                    break;
            }

            $valuesToInsert[] = '('.acym_escapeDB($value->namekey).','.acym_escapeDB($value->value).')';
        }

        $query = 'REPLACE INTO #__acym_configuration VALUES '.implode(',', $valuesToInsert).';';

        return $this->insertQuery($query);
    }

    private function migrateTemplates(array $params): int
    {
        $mailClass = new MailClass();

        $queryGetTemplates = 'SELECT `tempid`, `name`, `body`, `styles`, `subject`, `stylesheet`, `fromname`, `fromemail`, `replyname`, `replyemail` FROM #__acymailing_template LIMIT '.intval(
                $params['currentElement']
            ).', '.intval($params['insertPerCalls']);

        $templates = acym_loadObjectList($queryGetTemplates);
        if (empty($templates)) {
            return 0;
        }

        $valuesToInsert = [];

        $templates = $mailClass->encode($templates);

        foreach ($templates as $oneTemplate) {
            $oneTemplateStyles = unserialize($oneTemplate->styles);

            foreach ($oneTemplateStyles as $key => $value) {
                if (strpos($key, 'tag_') !== false) {
                    $tag = str_replace('tag_', '', $key);
                    $styleDeclaration = $tag.'{'.$value.'}';
                } elseif (strpos($key, 'color_bg') !== false) {
                    $styleDeclaration = '';
                } else {
                    $styleDeclaration = '.'.$key.'{'.$value.'}';
                }

                $oneTemplate->stylesheet .= $styleDeclaration;
            }

            $valuesToInsert[] = '('.implode(
                    ', ',
                    [
                        acym_escapeDB(empty($oneTemplate->name) ? acym_translation('ACYM_MIGRATED_TEMPLATE').' '.time() : $oneTemplate->name),
                        acym_escapeDB(acym_date('now', 'Y-m-d H:i:s', false)),
                        '0',
                        acym_escapeDB(MailClass::TYPE_TEMPLATE),
                        acym_escapeDB(empty($oneTemplate->body) ? '' : $oneTemplate->body),
                        acym_escapeDB($oneTemplate->subject),
                        acym_escapeDB($oneTemplate->fromname),
                        acym_escapeDB($oneTemplate->fromemail),
                        acym_escapeDB($oneTemplate->replyname),
                        acym_escapeDB($oneTemplate->replyemail),
                        acym_escapeDB($oneTemplate->stylesheet),
                        intval(acym_currentUserId()),
                    ]
                ).')';
        }

        if (empty($valuesToInsert)) {
            return 0;
        }

        $queryInsert = 'INSERT INTO #__acym_mail (`name`, `creation_date`, `drag_editor`, `type`, `body`, `subject`, `from_name`, `from_email`, `reply_to_name`, `reply_to_email`, `stylesheet`, `creator_id`) VALUES '.implode(
                ',',
                $valuesToInsert
            ).';';

        return $this->insertQuery($queryInsert);
    }

    private function migrateUsers_fields(array $params): int
    {
        $fieldsV5InDB = acym_loadObjectList(
            'SELECT `namekey`, `type` 
            FROM #__acymailing_fields 
            WHERE `namekey` NOT IN ("name", "email", "html") 
                AND `type` NOT IN ("customtext", "category", "gravatar")',
            'namekey'
        );

        if (empty($fieldsV5InDB)) {
            return 0;
        }

        $columnUserTable = acym_getColumns('acymailing_subscriber', false);

        $fieldsV5 = [];

        foreach ($fieldsV5InDB as $key => $field) {
            if (in_array($key, $columnUserTable)) {
                $fieldsV5[$key] = $field;
            }
        }

        if (empty($fieldsV5)) {
            return 0;
        }

        $fieldsKeyV5 = array_keys($fieldsV5);

        $whereUserField = '`'.implode('` IS NOT NULL OR `', $fieldsKeyV5);
        if (!empty($fieldsKeyV5)) {
            $whereUserField .= '` IS NOT NULL';
        }

        $query = 'SELECT `subid`, `'.implode('`, `', $fieldsKeyV5).'` FROM #__acymailing_subscriber WHERE '.$whereUserField.' LIMIT '.intval($params['currentElement']).', '.intval(
                $params['insertPerCalls']
            );
        $usersFieldsValuesV5 = acym_loadObjectList($query);

        $fieldImported = acym_loadObjectList('SELECT `id`, `namekey`, `option`, `type` FROM #__acym_field WHERE `namekey` IN ("'.implode('", "', $fieldsKeyV5).'")', 'namekey');

        $valuesToInsert = [];

        foreach ($fieldsKeyV5 as $fieldKey) {
            foreach ($usersFieldsValuesV5 as $user) {
                if ('date' == $fieldImported[$fieldKey]->type) {
                    $user->$fieldKey = preg_replace('@[^0-9]@', '/', $user->$fieldKey);
                }

                if ('birthday' == $fieldsV5[$fieldKey]->type && !empty($user->$fieldKey)) {
                    if (!is_array($fieldImported[$fieldKey]->option)) {
                        $fieldImported[$fieldKey]->option = json_decode($fieldImported[$fieldKey]->option, true);
                    }

                    if (!empty($fieldImported[$fieldKey]->option['format'])) {
                        $birthdayValue = explode('/', $user->$fieldKey);
                        $positions = [
                            '0' => strpos($fieldImported[$fieldKey]->option['format'], '%y'),
                            '1' => strpos($fieldImported[$fieldKey]->option['format'], '%m'),
                            '2' => strpos($fieldImported[$fieldKey]->option['format'], '%d'),
                        ];
                        asort($positions);
                        $final = [];
                        foreach ($positions as $key => $value) {
                            $final[] = $birthdayValue[$key];
                        }
                        $user->$fieldKey = implode('/', $final);
                    }
                }

                if ('phone' == $fieldImported[$fieldKey]->type) {
                    $user->$fieldKey = str_replace('+', '', $user->$fieldKey);
                }

                if (strlen($user->$fieldKey) === 0 || ($fieldImported[$fieldKey]->type === 'phone' && $user->$fieldKey === ',')) {
                    continue;
                }

                $valuesToInsert[] = '('.intval($user->subid).', '.acym_escapeDB($user->$fieldKey).', '.intval($fieldImported[$fieldKey]->id).')';
            }
        }

        if (empty($valuesToInsert)) {
            return 0;
        }

        return (int)acym_query('INSERT IGNORE INTO #__acym_user_has_field (`user_id`, `value`, `field_id`) VALUES '.implode(', ', $valuesToInsert));
    }

    private function migrateFields(array $params): int
    {
        $fieldClass = new FieldClass();

        $columnConnection = [
            'namekey' => 'namekey',
            'fieldname' => 'name',
            'published' => 'active',
            'options' => 'option',
            'listing' => 'backend_listing',
            'backend' => 'backend_edition',
            'default' => 'default_value',
            'type' => 'type',
            'value' => 'value',
            'ordering' => 'ordering',
            'required' => 'required',
        ];

        $optionConnection = [
            'errormessage' => 'error_message',
            'checkcontent' => 'authorized_content',
            'errormessagecheckcontent' => 'error_message_invalid',
            'cols' => 'columns',
            'fieldcatclass' => 'css_class',
            'format' => 'format',
            'size' => 'size',
            'rows' => 'rows',
            'customtext' => 'custom_text',
        ];

        $databaseConnection = [
            'dbName' => 'database',
            'tableName' => 'table',
            'valueFromDb' => 'value',
            'titleFromDb' => 'title',
            'whereCond' => 'where',
            'whereOperator' => 'where_sign',
            'whereValue' => 'where_value',
            'orderField' => 'order_by',
            'orderValue' => 'sort_order',
        ];

        $typeConnection = [
            'text' => 'text',
            'textarea' => 'textarea',
            'radio' => 'radio',
            'checkbox' => 'checkbox',
            'singledropdown' => 'single_dropdown',
            'multipledropdown' => 'multiple_dropdown',
            'date' => 'date',
            'birthday' => 'date',
            'file' => 'file',
            'phone' => 'phone',
            'customtext' => 'custom_text',
        ];

        $fieldsFromV5 = acym_loadObjectList(
            'SELECT * FROM #__acymailing_fields WHERE `namekey` NOT IN ("name", "email", "html") AND `type` NOT IN ("gravatar", "category")  LIMIT '.intval(
                $params['currentElement']
            ).', '.intval($params['insertPerCalls'])
        );

        $insertedFields = [];

        foreach ($fieldsFromV5 as $oneField) {
            $newField = new \stdClass();
            foreach ($oneField as $key => $value) {
                if (!array_key_exists($key, $columnConnection)) {
                    continue;
                }

                if ('type' === $columnConnection[$key]) {
                    $value = $typeConnection[$value];
                }
                if ('default_value' === $columnConnection[$key] && in_array($oneField->type, ['date', 'birthday'])) {
                    $value = preg_replace('@[^0-9]@', '/', $value);
                }
                if ('value' === $columnConnection[$key]) {
                    $allValues = explode("\n", $value);
                    $returnedValues = [];
                    foreach ($allValues as $id => $oneVal) {
                        $line = explode('::', trim($oneVal));
                        if (empty($line[1])) continue;
                        $var = empty($line[0]) ? '' : $line[0];
                        $val = empty($line[1]) ? '' : $line[1];

                        $obj = new \stdClass();
                        $obj->value = $var;
                        $obj->title = $val;
                        if (!empty($line[2])) {
                            $obj->disabled = 'y';
                        } else {
                            $obj->disabled = 'n';
                        }
                        $returnedValues[] = $obj;
                    }
                    $value = json_encode($returnedValues);
                }
                if ('option' === $columnConnection[$key]) {
                    $options = unserialize($value);
                    if (empty($options)) {
                        continue;
                    }
                    $newOption = new \stdClass();
                    foreach ($options as $keyOption => $option) {
                        if (!array_key_exists($keyOption, $optionConnection)) continue;
                        if ('authorized_content' === $optionConnection[$keyOption]) {
                            $option = [
                                $option,
                                'regex' => $options['regexp'],
                            ];
                        }
                        if (in_array($oneField->type, ['date', 'birthday']) && 'format' === $keyOption) {
                            if (empty($option)) {
                                $option = '%d%m%y';
                            } else {
                                $option = strtolower($option);
                                $position = [
                                    '%y' => strpos($option, '%y'),
                                    '%m' => strpos($option, '%m'),
                                    '%d' => strpos($option, '%d'),
                                ];
                                asort($position);
                                $option = implode('', array_keys($position));
                            }
                        }
                        $optionName = $optionConnection[$keyOption];
                        $newOption->$optionName = $option;
                    }
                    $newOption->fieldDB = [];
                    foreach ($databaseConnection as $keyOldDatabase => $keyNewDatabase) {
                        if (!array_key_exists($keyOldDatabase, $options)) {
                            $newOption->fieldDB[$keyNewDatabase] = '';
                            continue;
                        }
                        if ('database' == $keyNewDatabase && 'current' == $options[$keyOldDatabase]) $options[$keyOldDatabase] = acym_loadResult('SELECT DATABASE();');
                        $newOption->fieldDB[$keyNewDatabase] = $options[$keyOldDatabase];
                    }
                    $newOption->fieldDB = json_encode($newOption->fieldDB);
                    $value = json_encode($newOption);
                }
                $column = $columnConnection[$key];
                $newField->$column = $value;
            }
            $insertedFields[] = $fieldClass->save($newField);
        }

        return count($insertedFields);
    }

    private function migrateMails(array $params): int
    {
        $queryGetMails = 'SELECT mail.`mailid`,
                                mail.`created`, 
                                mail.`type`, 
                                mail.`body`, 
                                mail.`subject`, 
                                mail.`fromname`, 
                                mail.`fromemail`, 
                                mail.`replyname`, 
                                mail.`replyemail`, 
                                mail.`bccaddresses`, 
                                mail.`tempid`, 
                                mail.`senddate`, 
                                mail.`published`, 
                                mail.`attach`,
                                mail.userid, 
                                template.`stylesheet`, 
                                template.`styles`
                        FROM #__acymailing_mail mail 
                        LEFT JOIN #__acymailing_template template 
                        ON mail.tempid = template.tempid
                        LIMIT '.intval($params['currentElement']).', '.intval($params['insertPerCalls']);

        $mails = acym_loadObjectList($queryGetMails);

        if (empty($mails)) {
            return 0;
        }

        $mailsToInsert = [];
        $campaignsToInsert = [];
        $followupsToInsert = [];
        $followupsCreated = [];
        $followupClass = new FollowupClass();

        foreach ($mails as $oneMail) {
            if (empty($oneMail->mailid)) {
                continue;
            }

            switch ($oneMail->type) {
                case 'welcome':
                    $mailType = MailClass::TYPE_WELCOME;
                    break;
                case 'unsub':
                    $mailType = MailClass::TYPE_UNSUBSCRIBE;
                    break;
                case 'followup':
                    $mailType = MailClass::TYPE_FOLLOWUP;
                    break;
                case 'news':
                    $mailType = MailClass::TYPE_STANDARD;
                    break;
                default:
                    $mailType = 'invalid';
                    break;
            }

            if ($mailType === 'invalid') {
                continue;
            }

            $mailStylesheet = $oneMail->stylesheet;

            $templateStyles = unserialize($oneMail->styles);

            if ($templateStyles !== false) {
                foreach ($templateStyles as $key => $value) {
                    if (strpos($key, 'tag_') !== false) {
                        $tag = str_replace('tag_', '', $key);
                        $styleDeclaration = $tag.'{'.$value.'}';
                    } elseif (strpos($key, 'color_bg') !== false) {
                        $styleDeclaration = '';
                    } else {
                        $styleDeclaration = '.'.$key.'{'.$value.'}';
                    }
                    $mailStylesheet .= $styleDeclaration;
                }
            }

            $subject = acym_utf8Encode(substr($oneMail->subject, 0, 255));

            $mail = [
                'id' => intval($oneMail->mailid),
                'name' => acym_escapeDB($subject),
                'creation_date' => acym_escapeDB(acym_date(empty($oneMail->created) ? 'now' : $oneMail->created, 'Y-m-d H:i:s')),
                'drag_editor' => 0,
                'type' => acym_escapeDB($mailType),
                'body' => acym_escapeDB(acym_utf8Encode($oneMail->body)),
                'subject' => acym_escapeDB($subject),
                'from_name' => acym_escapeDB($oneMail->fromname),
                'from_email' => acym_escapeDB($oneMail->fromemail),
                'reply_to_name' => acym_escapeDB($oneMail->replyname),
                'reply_to_email' => acym_escapeDB($oneMail->replyemail),
                'bcc' => acym_escapeDB($oneMail->bccaddresses),
                'stylesheet' => acym_escapeDB($mailStylesheet),
                'creator_id' => empty($oneMail->userid) ? acym_currentUserId() : intval($oneMail->userid),
                'attachments' => [],
            ];

            if (!empty($oneMail->attach)) {
                $attachments = unserialize($oneMail->attach);
                foreach ($attachments as $oneAttachment) {
                    $fileName = substr($oneAttachment->filename, strrpos($oneAttachment->filename, DS));
                    $uploadFolder = str_replace(['/', '\\'], DS, acym_getFilesFolder());
                    $v7Location = $uploadFolder.$fileName;

                    $v5Path = acym_cleanPath(ACYM_ROOT.DS.$oneAttachment->filename);
                    if (!file_exists($v5Path)) continue;

                    $v7Path = acym_cleanPath(ACYM_ROOT.DS.$v7Location);
                    if (!file_exists($v7Path)) {
                        acym_copyFile($v5Path, $v7Path);
                    }

                    $mail['attachments'][] = (object)[
                        'filename' => $v7Location,
                        'size' => $oneAttachment->size,
                    ];
                }
            }
            $mail['attachments'] = acym_escapeDB(json_encode($mail['attachments']));

            if ($mailType === MailClass::TYPE_STANDARD) {
                $stats = acym_loadResult('SELECT COUNT(mailid) FROM #__acymailing_stats WHERE mailid = '.intval($oneMail->mailid));
                $isSent = !empty($stats);

                $sendingType = intval(!$isSent && ($oneMail->senddate > time()));
                $campaign = [
                    'sending_date' => empty($oneMail->senddate) ? 'NULL' : acym_escapeDB(acym_date($oneMail->senddate, 'Y-m-d H:i:s')),
                    'draft' => intval(!$isSent),
                    'active' => empty($oneMail->published) ? 0 : intval($oneMail->published),
                    'mail_id' => intval($oneMail->mailid),
                    'sending_type' => acym_escapeDB(0 === $sendingType ? CampaignClass::SENDING_TYPE_NOW : CampaignClass::SENDING_TYPE_SCHEDULED),
                    'sent' => intval($isSent),
                    'sending_params' => acym_escapeDB('[]'),
                ];
                $campaignsToInsert[] = '('.implode(', ', $campaign).')';
            }

            if ($mailType === MailClass::TYPE_FOLLOWUP) {
                $v5Campaign = acym_loadObject(
                    'SELECT l.listid, l.published, l.name 
                    FROM #__acymailing_listmail AS lm
                    JOIN #__acymailing_list AS l ON l.listid = lm.listid
                    WHERE mailid = '.intval($oneMail->mailid)
                );

                if (empty($followupsCreated[$v5Campaign->listid])) {
                    $followupListIds = acym_loadResultArray('SELECT listid FROM #__acymailing_listcampaign WHERE campaignid = '.intval($v5Campaign->listid));
                    $followupCampaign = new \stdClass();
                    $followupCampaign->name = $v5Campaign->name;
                    $followupCampaign->display_name = $v5Campaign->name;
                    $followupCampaign->creation_date = acym_date('now', 'Y-m-d H:i:s');
                    $followupCampaign->trigger = 'user_subscribe';
                    $followupCampaign->condition = [
                        'lists_status' => 'is',
                        'lists' => $followupListIds,
                        'segments_status' => '',
                    ];
                    $followupCampaign->active = $v5Campaign->published;
                    $followupCampaign->send_once = 1;
                    $followupCampaign->list_id = $v5Campaign->listid;

                    $followupsCreated[$v5Campaign->listid] = $followupClass->save($followupCampaign);
                }

                $delay = $oneMail->senddate;
                if (empty($delay)) {
                    $unit = 60;
                } elseif ($delay % 2628000 === 0) {
                    $unit = 2628000;
                } elseif ($delay % 604800 === 0) {
                    $unit = 604800;
                } elseif ($delay % 86400 === 0) {
                    $unit = 86400;
                } elseif ($delay % 3600 === 0) {
                    $unit = 3600;
                } else {
                    $unit = 60;
                }
                $delay = $delay / $unit;

                $followup = [
                    'mail_id' => intval($oneMail->mailid),
                    'followup_id' => intval($followupsCreated[$v5Campaign->listid]),
                    'delay' => intval($delay),
                    'delay_unit' => intval($unit),
                ];

                $followupsToInsert[] = '('.implode(', ', $followup).')';
            }

            $mailsToInsert[] = '('.implode(', ', $mail).')';
        }

        if (empty($mailsToInsert)) {
            return 0;
        }

        $queryMailsInsert = 'INSERT INTO #__acym_mail ('.implode(', ', array_keys($mail)).') VALUES '.implode(',', $mailsToInsert).';';
        $resultMail = $this->insertQuery($queryMailsInsert);
        if ($resultMail === -1) {
            return -1;
        }

        if (!empty($campaignsToInsert)) {
            $queryCampaignInsert = 'INSERT IGNORE INTO #__acym_campaign ('.implode(', ', array_keys($campaign)).') VALUES '.implode(',', $campaignsToInsert).';';
            if ($this->insertQuery($queryCampaignInsert) === false) {
                return -1;
            }
        }

        if (!empty($followupsToInsert)) {
            $queryInsert = 'INSERT IGNORE INTO #__acym_followup_has_mail ('.implode(', ', array_keys($followup)).') VALUES '.implode(',', $followupsToInsert).';';
            if ($this->insertQuery($queryInsert) === false) {
                return -1;
            }
        }

        return $resultMail;
    }

    private function migrateLists(array $params): int
    {
        $lists = acym_loadObjectList(
            'SELECT `listid`, `name`, `published`, `visible`, `color`, `userid`, `type`, `description`, `access_manage` 
            FROM #__acymailing_list 
            LIMIT '.intval($params['currentElement']).', '.intval($params['insertPerCalls'])
        );

        $listsToInsert = [];

        $siteUserGroupIds = array_keys(acym_getGroups());

        foreach ($lists as $oneList) {
            $access = '';
            if ($oneList->access_manage === 'all') {
                $access = ','.implode(',', $siteUserGroupIds).',';
            } elseif ($oneList->access_manage !== 'none') {
                $access = $oneList->access_manage;
            }

            $list = [
                'id' => intval($oneList->listid),
                'name' => acym_escapeDB($oneList->name),
                'active' => $oneList->type === 'campaign' ? 1 : (empty($oneList->published) ? 0 : 1),
                'visible' => acym_escapeDB($oneList->visible),
                'clean' => 0,
                'color' => acym_escapeDB($oneList->color),
                'creation_date' => acym_escapeDB(acym_date('now', 'Y-m-d H:i:s')),
                'cms_user_id' => empty($oneList->userid) ? acym_currentUserId() : intval($oneList->userid),
                'description' => acym_escapeDB($oneList->description),
                'type' => acym_escapeDB($oneList->type === 'campaign' ? ListClass::LIST_TYPE_FOLLOWUP : ListClass::LIST_TYPE_STANDARD),
                'access' => acym_escapeDB($access),
            ];

            $listsToInsert[] = '('.implode(', ', $list).')';
        }

        if (empty($listsToInsert)) {
            return 0;
        }

        $queryInsert = 'INSERT INTO #__acym_list ('.implode(', ', array_keys($list)).') VALUES '.implode(',', $listsToInsert).';';

        return $this->insertQuery($queryInsert);
    }

    private function migrateUsers(array $params): int
    {
        $queryGetUsers = 'SELECT `subid`, `name`, `email`, `created`, `enabled`, `userid`, `source`, `confirmed`, `key` FROM #__acymailing_subscriber LIMIT '.intval(
                $params['currentElement']
            ).', '.intval($params['insertPerCalls']);

        $users = acym_loadObjectList($queryGetUsers, 'subid');

        $usersToInsert = [];

        foreach ($users as $oneUser) {
            if (empty($oneUser->subid)) {
                continue;
            }

            $user = [
                'id' => intval($oneUser->subid),
                'name' => acym_escapeDB($oneUser->name),
                'email' => acym_escapeDB($oneUser->email),
                'creation_date' => acym_escapeDB(empty($oneUser->created) ? acym_date('now', 'Y-m-d H:i:s') : acym_date($oneUser->created, 'Y-m-d H:i:s')),
                'active' => acym_escapeDB($oneUser->enabled),
                'cms_id' => intval($oneUser->userid),
                'source' => acym_escapeDB($oneUser->source),
                'confirmed' => acym_escapeDB($oneUser->confirmed),
                'key' => acym_escapeDB($oneUser->key),
            ];

            $usersToInsert[] = '('.implode(', ', $user).')';
        }

        if (empty($usersToInsert)) {
            return 0;
        }

        $queryInsert = 'INSERT INTO #__acym_user (`id`, `name`, `email`, `creation_date`, `active`, `cms_id`, `source`, `confirmed`, `key`) VALUES '.implode(
                ', ',
                $usersToInsert
            ).';';

        return $this->insertQuery($queryInsert);
    }

    private function migrateBounce(array $params): int
    {
        $rules = acym_loadObjectList('SELECT * FROM #__acymailing_rules LIMIT '.intval($params['currentElement']).', '.intval($params['insertPerCalls']));

        if (empty($rules)) {
            return 0;
        }

        $keys = [
            'ACY_RULE_ACTION' => 'ACYM_ACTION_REQUIRED',
            'ACY_RULE_ACKNOWLEDGE_BODY' => 'ACYM_ACKNOWLEDGMENT_RECEIPT_BODY',
            'ACY_RULE_ACKNOWLEDGE' => 'ACYM_ACKNOWLEDGMENT_RECEIPT_SUBJECT',
            'ACY_RULE_LOOP_BODY' => 'ACYM_FEEDBACK_LOOP_BODY',
            'ACY_RULE_LOOP' => 'ACYM_FEEDBACK_LOOP',
            'ACY_RULE_FULL' => 'ACYM_MAILBOX_FULL',
            'ACY_RULE_GOOGLE' => 'ACYM_BLOCKED_GOOGLE_GROUPS',
            'ACY_RULE_EXIST1' => 'ACYM_MAILBOX_DOESNT_EXIST_1',
            'ACY_RULE_FILTERED' => 'ACYM_MESSAGE_BLOCKED_RECIPIENTS',
            'ACY_RULE_EXIST2' => 'ACYM_MAILBOX_DOESNT_EXIST_2',
            'ACY_RULE_DOMAIN' => 'ACYM_DOMAIN_NOT_EXIST',
            'ACY_RULE_TEMPORAR' => 'ACYM_TEMPORARY_FAILURES',
            'ACY_RULE_PERMANENT' => 'ACYM_FAILED_PERM',
            'ACY_RULE_FINAL' => 'ACYM_FINAL_RULE',
        ];

        $migratedRules = [];
        foreach ($rules as $oneRule) {

            $actionUser = unserialize($oneRule->action_user);
            $actionMessage = unserialize($oneRule->action_message);

            $actionsOnUsers = [];
            if (!empty($actionUser['unsub'])) $actionsOnUsers[] = 'unsubscribe_user';
            if (!empty($actionUser['sub']) && !empty($actionUser['subscribeto'])) {
                $actionsOnUsers[] = 'subscribe_user';
                $actionsOnUsers['subscribe_user_list'] = $actionUser['subscribeto'];
            }
            if (!empty($actionUser['block'])) $actionsOnUsers[] = 'block_user';
            if (!empty($actionUser['delete'])) $actionsOnUsers[] = 'delete_user';
            if (!empty($actionUser['emptyq'])) $actionsOnUsers[] = 'empty_queue_user';


            $actionsOnEmail = [];
            if (!empty($actionMessage['save'])) $actionsOnEmail[] = 'save_message';
            if (!empty($actionMessage['delete'])) $actionsOnEmail[] = 'delete_message';
            if (!empty($actionMessage['forwardto'])) {
                $actionsOnEmail[] = 'forward_message';
                $actionsOnEmail['forward_to'] = $actionMessage['forwardto'];
            }

            $rule = [
                'id' => intval($oneRule->ruleid),
                'name' => acym_escapeDB(str_replace(array_keys($keys), $keys, $oneRule->name)),
                'active' => intval($oneRule->published),
                'ordering' => intval($oneRule->ordering),
                'regex' => acym_escapeDB($oneRule->regex),
                'executed_on' => acym_escapeDB(json_encode(array_keys(unserialize($oneRule->executed_on)))),
                'execute_action_after' => empty($actionUser['min']) ? 0 : intval($actionUser['min']),
                'increment_stats' => empty($actionUser['stats']) ? 0 : intval($actionUser['stats']),
                'action_user' => acym_escapeDB(json_encode($actionsOnUsers)),
                'action_message' => acym_escapeDB(json_encode($actionsOnEmail)),
            ];

            $migratedRules[] = '('.implode(', ', $rule).')';
        }

        if (empty($migratedRules)) {
            return 0;
        }

        $queryInsert = 'INSERT INTO #__acym_rule (`id`, `name`, `active`, `ordering`, `regex`, `executed_on`, `execute_action_after`, `increment_stats`, `action_user`, `action_message`) VALUES '.implode(
                ', ',
                $migratedRules
            );

        try {
            return (int)acym_query($queryInsert);
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();

            return -1;
        }
    }

    private function migrateDistrib(array $params): int
    {
        $distributionLists = acym_loadObjectList(
            'SELECT * 
            FROM #__acymailing_action 
            LIMIT '.intval($params['currentElement']).', '.intval($params['insertPerCalls'])
        );

        if (empty($distributionLists)) {
            return 0;
        }

        $mailboxClass = new MailboxClass();
        $listClass = new ListClass();
        $standardLists = array_column($listClass->getAllWithoutManagement(), 'id');;
        $groups = acym_getGroups();
        foreach ($distributionLists as $oneDistributionList) {
            $mailboxAction = new \stdClass();
            $mailboxAction->name = $oneDistributionList->name;
            $mailboxAction->active = $oneDistributionList->published;
            $mailboxAction->frequency = $oneDistributionList->frequency;
            $mailboxAction->description = $oneDistributionList->description;

            $mailboxAction->server = $oneDistributionList->server;
            $mailboxAction->username = $oneDistributionList->username;
            $mailboxAction->password = $oneDistributionList->password;
            $mailboxAction->connection_method = $oneDistributionList->connection_method;
            $mailboxAction->secure_method = $oneDistributionList->secure_method;
            $mailboxAction->port = $oneDistributionList->port;
            $mailboxAction->self_signed = $oneDistributionList->self_signed;

            $mailboxAction->delete_wrong_emails = $oneDistributionList->delete_wrong_emails;

            $oneDistributionList->conditions = json_decode($oneDistributionList->conditions, true);
            $mailboxAction->conditions = [
                'sender' => '',
                'specific' => '',
                'groups' => [],
                'lists' => [],
                'subject' => '',
                'subject_text' => '',
                'subject_regex' => '',
            ];

            if ($oneDistributionList->conditions['sender'] === 'specific') {
                $mailboxAction->conditions['sender'] = 'specific';
                $mailboxAction->conditions['specific'] = $oneDistributionList->conditions['specific'];
            } elseif ($oneDistributionList->conditions['sender'] === 'group') {
                $mailboxAction->conditions['sender'] = 'groups';
                if ($oneDistributionList->conditions['group'] === 'all') {
                    $mailboxAction->conditions['groups'] = array_keys($groups);
                } else {
                    $mailboxAction->conditions['groups'] = $oneDistributionList->conditions['group'];
                }
            } elseif ($oneDistributionList->conditions['sender'] === 'list') {
                $mailboxAction->conditions['sender'] = 'lists';
                if ($oneDistributionList->conditions['listids'] === 'All') {
                    $mailboxAction->conditions['lists'] = $standardLists;
                } else {
                    $mailboxAction->conditions['lists'] = is_array($oneDistributionList->conditions['listids']) ? $oneDistributionList->conditions['listids']
                        : [$oneDistributionList->conditions['listids']];
                }
            }

            if ($oneDistributionList->conditions['subject'] !== 'all') {
                $mailboxAction->conditions['subject'] = $oneDistributionList->conditions['subject'];
                $mailboxAction->conditions['subject_text'] = $oneDistributionList->conditions['subjectvalue'];
            }

            $mailboxAction->conditions['subject_remove'] = $oneDistributionList->conditions['removeSubject'];


            $oneDistributionList->actions = json_decode($oneDistributionList->actions, true);
            $mailboxAction->actions = [];

            foreach ($oneDistributionList->actions as $oneAction) {
                if ($oneAction['type'] === 'forward') {
                    $mailboxAction->actions[] = [
                        'forward_specific' => [
                            'addresses' => $oneAction['forward'],
                            'template_id' => $oneAction['template'],
                        ],
                    ];
                } elseif ($oneAction['type'] === 'forwardlist') {
                    $mailboxAction->actions[] = [
                        'forward_list' => [
                            'list_id' => $oneAction['list'],
                            'template_id' => $oneAction['template'],
                        ],
                    ];
                } elseif ($oneAction['type'] === 'subscribe') {
                    $mailboxAction->actions[] = [
                        'acy_list_subscribe' => [
                            'list_id' => $oneAction['list'],
                        ],
                    ];
                } elseif ($oneAction['type'] === 'unsubscribe') {
                    $mailboxAction->actions[] = [
                        'acy_list_unsubscribe' => [
                            'list_id' => $oneAction['list'],
                        ],
                    ];
                }
            }

            $mailboxAction->conditions = json_encode($mailboxAction->conditions);
            $mailboxAction->actions = json_encode($mailboxAction->actions);

            $mailboxAction->senderfrom = $oneDistributionList->senderfrom;
            $mailboxAction->senderto = $oneDistributionList->senderto;

            $mailboxClass->save($mailboxAction);
        }

        return 0;
    }

    private function migrateSubscriptions(array $params): int
    {
        $queryGetSubscriptions = 'SELECT `listid`, `subid`, `subdate`, `unsubdate`, `status` FROM #__acymailing_listsub LIMIT '.intval($params['currentElement']).', '.intval(
                $params['insertPerCalls']
            );

        $subscriptions = acym_loadObjectList($queryGetSubscriptions);

        if (empty($subscriptions)) {
            return 0;
        }

        $subscriptionsToInsert = [];

        foreach ($subscriptions as $oneSubscription) {
            if (empty($oneSubscription->subid) || empty($oneSubscription->listid)) {
                continue;
            }

            if ($oneSubscription->status == 2) $oneSubscription->status = 1;

            $subscription = [
                'user_id' => acym_escapeDB($oneSubscription->subid),
                'list_id' => acym_escapeDB($oneSubscription->listid),
                'status' => acym_escapeDB($oneSubscription->status == -1 ? 0 : $oneSubscription->status),
                'subscription_date' => empty($oneSubscription->subdate) ? 'NULL' : acym_escapeDB(acym_date($oneSubscription->subdate, 'Y-m-d H:i:s')),
                'unsubscribe_date' => empty($oneSubscription->unsubdate) ? 'NULL' : acym_escapeDB(acym_date($oneSubscription->unsubdate, 'Y-m-d H:i:s')),
            ];

            $subscriptionsToInsert[] = '('.implode(', ', $subscription).')';
        }

        if (empty($subscriptionsToInsert)) {
            return 0;
        }

        $queryInsert = 'INSERT IGNORE INTO #__acym_user_has_list (`user_id`, `list_id`, `status`, `subscription_date`, `unsubscribe_date`) VALUES '.implode(
                ', ',
                $subscriptionsToInsert
            ).';';

        return $this->insertQuery($queryInsert);
    }

    private function migrateMailHasLists(array $params): int
    {
        $queryGetMailHasLists = 'SELECT listmail.`mailid`, listmail.`listid` 
                                FROM #__acymailing_listmail AS listmail
                                JOIN #__acymailing_mail AS mail ON mail.`mailid` = listmail.`mailid` 
                                WHERE mail.`type` IN ("news", "unsub", "welcome", "followup")
                                LIMIT '.intval($params['currentElement']).', '.intval($params['insertPerCalls']);

        $mailHasLists = acym_loadObjectList($queryGetMailHasLists);
        if (empty($mailHasLists)) {
            return 0;
        }

        $mailHasListsToInsert = [];

        foreach ($mailHasLists as $oneMailHasLists) {
            if (empty($oneMailHasLists->mailid) || empty($oneMailHasLists->listid)) {
                continue;
            }

            $mailHasListsToInsert[] = '('.intval($oneMailHasLists->mailid).', '.intval($oneMailHasLists->listid).')';
        }

        if (empty($mailHasListsToInsert)) {
            return 0;
        }

        $queryInsert = 'INSERT IGNORE INTO #__acym_mail_has_list (`mail_id`, `list_id`) VALUES '.implode(',', $mailHasListsToInsert).';';

        return $this->insertQuery($queryInsert);
    }

    private function migrateMailStats(array $params): int
    {
        $queryGetStats = 'SELECT `mailid`, `senthtml`, `senttext`, `senddate`, `fail`, `openunique`, `opentotal` FROM #__acymailing_stats LIMIT '.intval(
                $params['currentElement']
            ).', '.intval($params['insertPerCalls']);

        $stats = acym_loadObjectList($queryGetStats);
        if (empty($stats)) {
            return 0;
        }


        $statsToInsert = [];

        $allMails = acym_loadResultArray('SELECT id FROM #__acym_mail');

        foreach ($stats as $oneStat) {
            if (empty($oneStat->mailid) || !in_array($oneStat->mailid, $allMails)) {
                continue;
            }

            $totalSent = intval($oneStat->senthtml) + intval($oneStat->senttext);
            $stat = [
                'mail_id' => acym_escapeDB($oneStat->mailid),
                'total_subscribers' => acym_escapeDB($totalSent + $oneStat->fail),
                'sent' => acym_escapeDB($totalSent),
                'send_date' => empty($oneStat->senddate) ? 'NULL' : acym_escapeDB(acym_date($oneStat->senddate, 'Y-m-d H:i:s')),
                'fail' => acym_escapeDB($oneStat->fail),
                'open_unique' => intval($oneStat->openunique),
                'open_total' => intval($oneStat->opentotal),
            ];
            $statsToInsert[] = '('.implode(', ', $stat).')';
        }

        if (empty($statsToInsert)) {
            return 0;
        }

        $queryInsert = 'INSERT IGNORE INTO #__acym_mail_stat (`mail_id`, `total_subscribers`, `sent`, `send_date`, `fail`, `open_unique`, `open_total`) VALUES '.implode(
                ',',
                $statsToInsert
            ).';';

        return $this->insertQuery($queryInsert);
    }

    private function migrateWelcomeunsub(array $params): int
    {
        $queryGetIds = 'SELECT `listid`, `welmailid`, `unsubmailid` FROM #__acymailing_list LIMIT '.intval($params['currentElement']).', '.intval($params['insertPerCalls']);

        $ids = acym_loadObjectList($queryGetIds);

        if (empty($ids)) {
            return 0;
        }

        $idsToInsert = [];

        foreach ($ids as $oneId) {
            if (empty($oneId->listid) || (empty($oneId->welmailid) && empty($oneId->unsubmailid))) {
                continue;
            }

            $welId = empty($oneId->welmailid) ? 'NULL' : intval($oneId->welmailid);
            $unsId = empty($oneId->unsubmailid) ? 'NULL' : intval($oneId->unsubmailid);

            $id = [
                'id' => intval($oneId->listid),
                'welcome_id' => $welId,
                'unsubscribe_id' => $unsId,
            ];

            $idsToInsert[] = '('.implode(', ', $id).')';
        }

        if (empty($idsToInsert)) {
            return 0;
        }

        $queryInsert = 'INSERT IGNORE INTO #__acym_list(`id`, `welcome_id`, `unsubscribe_id`) VALUES '.implode(
                ',',
                $idsToInsert
            ).' ON DUPLICATE KEY UPDATE `welcome_id` = VALUES(`welcome_id`), `unsubscribe_id` = VALUES(`unsubscribe_id`)';

        return $this->insertQuery($queryInsert);
    }

    private function insertQuery(string $queryInsert): int
    {
        try {
            $resultQuery = acym_query($queryInsert);
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();

            return -1;
        }

        if ($resultQuery === null) {
            $this->errors[] = acym_getDBError();

            return -1;
        } else {
            return intval($resultQuery);
        }
    }

    private function cleanFieldsTable(): bool
    {
        $queryClean = [
            'DELETE FROM #__acym_user_has_field',
            'DELETE FROM #__acym_field WHERE `id` NOT IN (1,2)',
        ];

        return $this->finalizeClean($queryClean);
    }

    private function cleanUsers_fieldsTable(): bool
    {
        return true;
    }

    private function finalizeClean(array $queryClean): bool
    {
        $hasError = false;

        foreach ($queryClean as $oneQuery) {
            try {
                $res = acym_query($oneQuery);
            } catch (\Exception $e) {
                $res = null;
            }
            if ($res === null) {
                $this->errors[] = isset($e) ? $e->getMessage() : acym_getDBError();
                $hasError = true;
                break;
            }
        }

        return !$hasError;
    }

    private function cleanMailsTable(): bool
    {
        $queryClean = [
            'UPDATE #__acym_list SET `unsubscribe_id` = NULL',
            'UPDATE #__acym_list SET `welcome_id` = NULL',
            'UPDATE #__acym_mail SET `parent_id` = NULL',
            'DELETE FROM #__acym_mail_archive',
            'DELETE FROM #__acym_followup_has_mail',
            'DELETE FROM #__acym_followup',
            'DELETE FROM #__acym_tag WHERE `type` = "mail"',
            'DELETE FROM #__acym_mail_override',
            'DELETE FROM #__acym_campaign WHERE `mail_id` IS NOT NULL',
            'DELETE FROM #__acym_campaign',
            'DELETE FROM #__acym_queue',
            'DELETE FROM #__acym_mail_has_list',
            'DELETE FROM #__acym_user_stat',
            'DELETE FROM #__acym_url_click',
            'DELETE FROM #__acym_mail_stat',
            'DELETE FROM #__acym_mail',
        ];

        return $this->finalizeClean($queryClean);
    }

    private function cleanListsTable(): bool
    {
        $queryClean = [
            'UPDATE #__acym_followup SET `list_id` = NULL',
            'DELETE FROM #__acym_tag WHERE `type` = "list"',
            'DELETE FROM #__acym_mail_has_list',
            'DELETE FROM #__acym_user_has_list',
            'DELETE FROM #__acym_list',
        ];

        return $this->finalizeClean($queryClean);
    }

    private function cleanUsersTable(): bool
    {
        $queryClean = [
            'DELETE FROM `#__acym_user_has_field`',
            'DELETE FROM `#__acym_user_has_list`',
            'DELETE FROM `#__acym_queue`',
            'DELETE FROM `#__acym_user`',
        ];

        return $this->finalizeClean($queryClean);
    }

    private function cleanBounceTable(): bool
    {
        $queryClean = [
            'DELETE FROM `#__acym_rule`',
        ];

        return $this->finalizeClean($queryClean);
    }

    private function cleanDistribTable(): bool
    {
        $queryClean = [
            'DELETE FROM `#__acym_mailbox_action`',
        ];

        return $this->finalizeClean($queryClean);
    }
}
