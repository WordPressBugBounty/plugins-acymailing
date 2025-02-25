<?php

use AcyMailing\Core\AcymPlugin;
use AcyMailing\Classes\UserClass;
use Joomla\Registry\Registry;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Application\ApplicationHelper;

class plgAcymcreateuser extends AcymPlugin
{
    public function __construct()
    {
        parent::__construct();
        $this->cms = 'all';

        $usergroups = acym_getGroups();
        if ('joomla' === ACYM_CMS) {
            $joomlaUsersparams = ComponentHelper::getParams('com_users');
            $defaultUsergroup = $joomlaUsersparams->get('new_usertype');
        } else {
            $defaultUsergroup = get_option('default_role');
        }

        $onlyFrontOptions = [
            'front' => acym_translation('ACYM_FRONT'),
            'both' => acym_translation('ACYM_FRONT_BACK'),
        ];

        $this->settings = [
            'enableCreate' => [
                'type' => 'switch',
                'label' => 'ACYM_ALLOW_CMS_USER_CREATION',
                'value' => 0,
            ],
            'onlyFront' => [
                'type' => 'select',
                'label' => 'ACYM_CREATE_FROM_SUB_ON',
                'value' => 1,
                'data' => $onlyFrontOptions,
            ],
            'onModif' => [
                'type' => 'switch',
                'label' => 'ACYM_CREATE_ON_USER_MODIFICATION',
                'value' => 0,
            ],
            'group' => [
                'type' => 'select',
                'label' => 'ACYM_USER_GROUP',
                'value' => $defaultUsergroup,
                'data' => $usergroups,
            ],
        ];
    }

    public function onAcymAfterUserCreate(&$user)
    {
        $this->createCmsUser($user);
    }

    public function onAcymAfterUserModify(&$user, &$oldUser)
    {
        if ($this->getParam('onModif', '0') === '0') return;

        $this->createCmsUser($user);
    }

    public function createCmsUser($user)
    {
        $enableCreate = $this->getParam('enableCreate', '0');
        if (empty($user->email) || !empty($user->cms_id) || empty($enableCreate)) return;

        if ($this->getParam('onlyFront', 'front') === 'front' && acym_isAdmin()) return;

        if ('wordpress' === ACYM_CMS) {
            $this->createWordpressUser($user);
        } else {
            $this->createJoomlaUser($user);
        }
    }

    protected function createJoomlaUser($user)
    {
        $joomlaUsersparams = ComponentHelper::getParams('com_users');
        $joomlaConfig = Factory::getConfig();

        $useractivation = $joomlaUsersparams->get('useractivation');
        $allowUserRegistration = $joomlaUsersparams->get('allowUserRegistration');
        if ($allowUserRegistration == 0) return;

        acym_loadLanguageFile('com_users', JPATH_SITE);

        $minimum_integers = $joomlaUsersparams->get('minimum_integers');
        $minimum_symbols = $joomlaUsersparams->get('minimum_symbols');
        $minimum_uppercase = $joomlaUsersparams->get('minimum_uppercase');
        $length = 8;
        if ($joomlaUsersparams->get('minimum_length') > 8) $length = $joomlaUsersparams->get('minimum_length');
        $tryCount = 0;
        do {
            $password = UserHelper::genrandompassword($length);
            $tryCount++;
            if ($tryCount > 50) break;
        } while ($this->getConditionPassword($password, $minimum_integers, $minimum_symbols, $minimum_uppercase));

        $defaultUsergroup = $joomlaUsersparams->get('new_usertype');
        $configUserGroup = $this->getParam('group', $defaultUsergroup);

        if (empty($user->name)) {
            $user->name = ucwords(trim(str_replace(['.', '_', ')', ',', '(', '-', 1, 2, 3, 4, 5, 6, 7, 8, 9, 0], ' ', substr($user->email, 0, strpos($user->email, '@')))));
        }
        $userData = [
            'name' => $user->name,
            'username' => $user->email,
            'password' => $password,
            'password2' => $password,
            'email' => $user->email,
            'block' => 0,
            'groups' => [$configUserGroup],
        ];

        $joomlaUser = new User();

        if (in_array($useractivation, [1, 2])) {
            $userData['activation'] = ApplicationHelper::getHash(UserHelper::genrandompassword());
            $userData['block'] = 1;
        }

        if (!$joomlaUser->bind($userData)) return false;
        if (!$this->save($joomlaUser)) return false;

        $user->cms_id = $joomlaUser->id;
        $userClass = new UserClass();
        $userClass->sendConf = false;
        $userClass->save($user);

        $data = $joomlaUser->getProperties();
        $data['fromname'] = $joomlaConfig->get('fromname');
        $data['mailfrom'] = $joomlaConfig->get('mailfrom');
        $data['sitename'] = $joomlaConfig->get('sitename');
        $data['siteurl'] = str_replace('/administrator', '', acym_baseURI());

        $emailSubject = acym_translationSprintf('COM_USERS_EMAIL_ACCOUNT_DETAILS', $data['name'], $data['sitename']);
        if (in_array($useractivation, [1, 2])) {
            $base = acym_currentURL();
            $activationLink = 'index.php?option=com_users&task=registration.activate&token=';
            $data['activate'] = $data['siteurl'].$activationLink.$data['activation'];

            if ($useractivation == 2) {
                $emailBody = acym_translationSprintf(
                    'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
                    $data['name'],
                    $data['sitename'],
                    $data['activate'],
                    $data['siteurl'],
                    $data['username'],
                    $data['password_clear']
                );
            } else {
                $activationMessage = 'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY';
                $emailBody = acym_translationSprintf(
                    $activationMessage,
                    $data['name'],
                    $data['sitename'],
                    $data['activate'],
                    $data['siteurl'],
                    $data['username'],
                    $data['password_clear']
                );
            }
        } else {
            $emailBody = acym_translationSprintf(
                'COM_USERS_EMAIL_REGISTERED_BODY',
                $data['name'],
                $data['sitename'],
                $data['siteurl'],
                $data['username'],
                $data['password_clear']
            );
        }

        if (ACYM_J40) {
            $keys = [];
            $replaceData = [];
            foreach ($data as $key => $oneKey) {
                if (is_array($oneKey)) continue;

                $keys[] = '{'.strtoupper($key).'}';
                $replaceData[] = $oneKey;
            }
            $emailSubject = str_replace($keys, $replaceData, $emailSubject);
            $emailBody = str_replace($keys, $replaceData, $emailBody);
        }

        $mailer = Factory::getMailer();
        $sender = [$joomlaConfig->get('mailfrom'), $joomlaConfig->get('fromname')];
        $mailer->setSender($sender);
        $mailer->addRecipient($user->email);
        $mailer->setSubject($emailSubject);
        $mailer->setBody($emailBody);
        $send = $mailer->Send();
        if ($send !== true) return false;
    }

    public function save($joomlaUser)
    {
        $table = $joomlaUser->getTable();
        $joomlaUser->params = '{}';
        $table->bind($joomlaUser->getProperties());

        try {
            if (!$table->check()) {
                $joomlaUser->setError($table->getError());

                return false;
            }

            $isNew = empty($joomlaUser->id);

            $my = Factory::getUser();

            $oldUser = new User($joomlaUser->id);

            $iAmRehashingSuperadmin = false;
            if (($my->id == 0 && !$isNew) && $joomlaUser->id == $oldUser->id && $oldUser->authorise('core.admin') && $oldUser->password != $joomlaUser->password) {
                $iAmRehashingSuperadmin = true;
            }

            $iAmSuperAdmin = $my->authorise('core.admin');
            if ($iAmSuperAdmin != true && $iAmRehashingSuperadmin != true) {
                if (!$isNew && Access::check($joomlaUser->id, 'core.admin')) {
                    throw new \RuntimeException('User not Super Administrator');
                }

                if ($joomlaUser->groups != null) {
                    foreach ($joomlaUser->groups as $groupId) {
                        if (Access::checkGroup($groupId, 'core.admin')) {
                            throw new \RuntimeException('User not Super Administrator');
                        }
                    }
                }
            }

            PluginHelper::importPlugin('user');
            if (ACYM_J40) {
                $result = Factory::getApplication()->triggerEvent('onUserBeforeSave', [$oldUser->getProperties(), $isNew, $joomlaUser->getProperties()]);
            } else {
                $dispatcher = \JEventDispatcher::getInstance();
                $result = $dispatcher->trigger('onUserBeforeSave', [$oldUser->getProperties(), $isNew, $joomlaUser->getProperties()]);
            }
            if (in_array(false, $result, true)) return false;

            $result = $table->store();

            if (empty($joomlaUser->id)) {
                $joomlaUser->id = $table->get('id');
            }

            if ($my->id == $table->id) {
                $registry = new Registry($table->params);
                $my->setParameters($registry);
            }
        } catch (\Exception $e) {
            $joomlaUser->setError($e->getMessage());

            return false;
        }

        return $result;
    }

    protected function createWordpressUser($user)
    {
        if (!get_option('users_can_register')) return;

        if (false !== username_exists($user->email)) return false;

        $defaultUsergroup = get_option('default_role');
        $configUserGroup = $this->getParam('group', $defaultUsergroup);

        $pwdTemp = wp_generate_password(12, true);
        $result = wp_create_user($user->email, $pwdTemp, $user->email);

        if (is_wp_error($result)) {
            return false;
        } else {
            $user->cms_id = $result;
            $userClass = new UserClass();
            $userClass->sendConf = false;
            $userClass->save($user);
            $wpUser = new WP_User($result);
            $wpUser->set_role($configUserGroup);
            wp_new_user_notification($result, null, 'user');
        }
    }

    protected function getConditionPassword($password, $minimum_integers, $minimum_symbols, $minimum_uppercase)
    {
        $notEnoughInt = !empty($minimum_integers) && preg_match_all("/[0-9]/", $password, $out) < $minimum_integers;
        $notEnoughSymbols = !empty($minimum_symbols) && preg_match_all("/[a-z]/", $password, $out) < $minimum_symbols;
        $notEnoughUpperCase = !empty($minimum_uppercase) && preg_match_all("/[A-Z]/", $password, $out) < $minimum_uppercase;

        return $notEnoughInt || $notEnoughSymbols || $notEnoughUpperCase;
    }
}
