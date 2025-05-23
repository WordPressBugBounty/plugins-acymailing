<?php

namespace AcyMailing\FrontControllers;

use AcyMailing\Classes\CampaignClass;
use AcyMailing\Classes\MailArchiveClass;
use AcyMailing\Classes\UserClass;
use AcyMailing\Helpers\EditorHelper;
use AcyMailing\Helpers\MailerHelper;
use AcyMailing\Helpers\PaginationHelper;
use AcyMailing\Core\AcymController;
use AcyMailing\Core\AcymParameter;

class ArchiveController extends AcymController
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultTask('view');

        $this->publicFrontTasks = [
            'view',
            'showArchive',
            'listing',
        ];
    }

    public function view(): void
    {
        if ($this->config->get('archive_index', 0) == 0) {
            acym_addMetadata('robots', 'noindex,nofollow');
        }

        acym_addMetadata('UTF-8', '', 'charset');

        $mailId = acym_getVar('int', 'id', 0);
        $isPopup = acym_getVar('int', 'is_popup', 0);

        $mailArchiveClass = new MailArchiveClass();
        $oneMail = $mailArchiveClass->getOneByMailId($mailId);
        if (empty($oneMail)) {
            $mailerHelper = new MailerHelper();
            $oneMail = $mailerHelper->load($mailId);
        }

        if (empty($oneMail)) {
            acym_raiseError(404, acym_translation('ACYM_EMAIL_NOT_FOUND'));

            return;
        }

        acym_addMetadata('og:url', acym_frontendLink('archive&task=view&id='.$mailId));
        acym_addMetadata('og:title', $oneMail->subject);
        acym_setPageTitle($oneMail->subject);

        $userKeys = acym_getVar('string', 'userid', 0);
        if (!empty($userKeys)) {
            $userId = intval(substr($userKeys, 0, strpos($userKeys, '-')));
            $userKey = substr($userKeys, strpos($userKeys, '-') + 1);
            $receiver = acym_loadObject('SELECT * FROM #__acym_user WHERE `id` = '.intval($userId).' AND `key` = '.acym_escapeDB($userKey));
        }

        $currentEmail = acym_currentUserEmail();
        if (empty($receiver) && !empty($currentEmail)) {
            $userClass = new UserClass();
            $receiver = $userClass->getOneByEmail($currentEmail);
        }

        if (empty($receiver)) {
            $receiver = new \stdClass();
            $receiver->name = acym_translation('ACYM_VISITOR');
        }

        acym_trigger('replaceUserInformation', [&$oneMail, &$receiver, false]);

        preg_match('@href="{unsubscribe:(.*)}"@', $oneMail->body, $match);
        if (!empty($match)) {
            $oneMail->body = str_replace($match[0], 'href="'.$match[1].'"', $oneMail->body);
        }

        if (strpos($oneMail->body, 'acym__wysid__template') !== false) {
            acym_addStyle(false, ACYM_CSS.'libraries/foundation_email.min.css?v='.filemtime(ACYM_MEDIA.'css'.DS.'libraries'.DS.'foundation_email.min.css'));
        }
        acym_addStyle(true, acym_getEmailCssFixes());
        if (!empty($oneMail->stylesheet)) {
            acym_addStyle(true, $oneMail->stylesheet);
        }
        $editorHelper = new EditorHelper();
        $settings = json_decode($oneMail->settings, true);
        if (!empty($settings)) {
            $settings = $editorHelper->getSettingsStyle($settings);

            if (!empty($settings)) {
                acym_addStyle(true, $settings);
            }
        }

        $oneMail->body = preg_replace('#background\-image: url\(&quot;([^)]*)&quot;\)#Uis', 'background-image: url($1)', $oneMail->body);

        $data = [
            'mail' => $oneMail,
            'receiver' => $receiver,
        ];

        acym_includeHeaders();
        parent::display($data);

        if ($isPopup || 'wordpress' === ACYM_CMS) exit;
    }

    public function listing(): void
    {
        acym_setVar('layout', 'listing');

        $search = acym_getVar('string', 'acym_search', '');

        $menu = acym_getMenu();
        if (!is_object($menu)) {
            acym_redirect(acym_rootURI());

            return;
        }
        $params = method_exists($menu, 'getParams') ? $menu->getParams() : $menu->params;
        $menuParams = new AcymParameter($params);

        $paramsJoomla = [];
        $paramsJoomla['suffix'] = $menuParams->get('pageclass_sfx', '');
        $paramsJoomla['page_heading'] = $menuParams->get('page_heading');
        $paramsJoomla['show_page_heading'] = $menuParams->get('show_page_heading', 0);

        if ($menuParams->get('menu-meta_description')) {
            acym_addMetadata('description', $menuParams->get('menu-meta_description'));
        }

        if ($menuParams->get('menu-meta_keywords')) {
            acym_addMetadata('keywords', $menuParams->get('menu-meta_keywords'));
        }

        if ($menuParams->get('robots')) {
            acym_addMetadata('robots', $menuParams->get('robots'));
        }

        $nbNewslettersPerPage = $menuParams->get('archiveNbNewslettersPerPage', 10);
        $listsSent = $menuParams->get('lists', '');
        $popup = $menuParams->get('popup', '1');
        $displayUserListOnly = $menuParams->get('displayUserListOnly', '1');

        $viewParams = [
            'nbNewslettersPerPage' => $nbNewslettersPerPage,
            'listsSent' => $listsSent,
            'popup' => $popup,
            'paramsCMS' => $paramsJoomla,
            'search' => $search,
            'displayUserListOnly' => $displayUserListOnly,
        ];

        $this->showArchive($viewParams);
    }

    public function showArchive(array $viewParams): void
    {
        acym_setVar('layout', 'listing');

        $data = $this->getDataForArchive($viewParams);

        parent::display($data);
    }

    private function getDataForArchive(array $viewParams): array
    {
        $params = [];

        $userId = false;
        $userClass = new UserClass();
        $currentUser = $userClass->identify(true);
        if (!empty($currentUser)) {
            $params['userId'] = $currentUser->id;
            $userId = $currentUser->id;
        }

        if (acym_isMultilingual()) {
            $params['language'] = acym_getLanguageTag();
        }

        if (!empty($viewParams['listsSent'])) {
            $params['lists'] = $viewParams['listsSent'];
        }

        if (!empty($viewParams['search'])) {
            $params['search'] = $viewParams['search'];
        }

        if (!empty($viewParams['displayUserListOnly'])) {
            $params['displayUserListOnly'] = $viewParams['displayUserListOnly'];
        }

        $params['page'] = $this->getVarFiltersListing('int', 'archive_pagination_page', 1);
        $campaignClass = new CampaignClass();
        $pagination = new PaginationHelper();
        $params['numberPerPage'] = (int)$viewParams['nbNewslettersPerPage'];

        $returnLastNewsletters = $campaignClass->getLastNewsletters($params);
        $pagination->setStatus($returnLastNewsletters['count'], $params['page'], $params['numberPerPage']);

        $disableButtons = '';
        if (isset($viewParams['disableButtons'])) {
            $disableButtons = $viewParams['disableButtons'];
        }

        return [
            'newsletters' => $returnLastNewsletters['matchingNewsletters'],
            'paramsCMS' => $viewParams['paramsCMS'],
            'pagination' => $pagination,
            'userId' => $userId,
            'popup' => '1' === $viewParams['popup'],
            'displayUserListOnly' => '1' === $viewParams['displayUserListOnly'],
            'search' => $viewParams['search'],
            'actionUrl' => acym_currentURL(),
            'disableButtons' => $disableButtons,
        ];
    }
}
