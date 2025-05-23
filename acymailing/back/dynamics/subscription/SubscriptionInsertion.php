<?php

use AcyMailing\Classes\CampaignClass;
use AcyMailing\Classes\MailClass;
use AcyMailing\Classes\UserClass;
use AcyMailing\Classes\ListClass;

trait SubscriptionInsertion
{
    private $addedListUnsubscribe = [];
    private $lists = [];
    private $listsowner = [];
    private $listsinfo = [];
    private $unsubscribeLink = [];
    private $mailLists = [];
    private $userClass = null;

    public function dynamicText($mailId)
    {
        return $this->pluginDescription;
    }

    public function textPopup()
    {
        $others = [];
        $others['unsubscribe'] = ['name' => acym_translation('ACYM_UNSUBSCRIBE_LINK'), 'default' => 'ACYM_UNSUBSCRIBE'];
        $others['unsubscribeall'] = ['name' => acym_translation('ACYM_UNSUBSCRIBE_ALL_LISTS_LINK'), 'default' => 'ACYM_UNSUBSCRIBE_ALL_LISTS'];
        $others['confirm'] = ['name' => acym_translation('ACYM_CONFIRM_SUBSCRIPTION_LINK'), 'default' => 'ACYM_CONFIRM_SUBSCRIPTION'];
        $others['subscribe'] = ['name' => acym_translation('ACYM_SUBSCRIBE_LINK'), 'default' => 'ACYM_SUBSCRIBE'];

        ?>
		<script type="text/javascript">
            var openedLists = false;
            var selectedSubscriptionDText = '';

            function changeSubscriptionTag(tagName) {
                selectedSubscriptionDText = tagName;
                let defaultText = [];
                <?php
                foreach ($others as $tagname => $tag) {
                    echo 'defaultText["'.$tagname.'"] = "'.acym_translation($tag['default'], true).'";';
                }
                ?>
                jQuery('.selected_row').removeClass('selected_row');
                jQuery('#tr_' + tagName).addClass('selected_row');
                jQuery('#acym__popup__subscription__tagtext').val(defaultText[tagName]);
                setSubscriptionTag();
            }

            function setSubscriptionTag() {
                var tag = '{' + selectedSubscriptionDText;
                var lists = jQuery('#acym__popup__subscription__listids');

                if ('subscribe' === selectedSubscriptionDText) {
                    tag += '|lists:' + lists.html();
                } else if (openedLists) {
                    jQuery('#acym__popup__plugin__subscription__lists__modal').slideUp();
                    jQuery('#select_lists_zone').hide();
                    openedLists = false;
                }

                tag += '}' + jQuery('#acym__popup__subscription__tagtext').val() + '{/' + selectedSubscriptionDText + '}';
                setTag(tag, jQuery('#tr_' + selectedSubscriptionDText));
            }

            function displayLists() {
                if (openedLists) return;
                openedLists = true;

                jQuery.acymModal();
                jQuery('#acym__popup__plugin__subscription__lists__modal').slideDown();
                jQuery('#select_lists_zone').toggle();
                jQuery('#acym__popup__subscription__change').on('change', function () {
                    var lists = JSON.parse(jQuery('#acym__modal__lists-selected').val());
                    jQuery('#acym__popup__subscription__listids').html(lists.join());
                    changeSubscriptionTag('subscribe');
                });
            }
		</script>
        <?php

        $text = '<div class="acym__popup__listing text-center grid-x">
                    <h1 class="acym__title acym__title__secondary text-center cell">'.acym_translation('ACYM_SUBSCRIPTION').'</h1>
                    <div class="grid-x medium-12 cell acym__row__no-listing text-left acym_vcenter">
                        <div class="grid-x cell medium-5 small-12 acym__listing__title acym__listing__title__dynamics acym__subscription__subscription acym_vcenter">
                            <label class="small-3 margin-bottom-0" for="acym__popup__subscription__tagtext">'.acym_translation('ACYM_TEXT').': </label>
                            <input class="small-9" type="text" name="tagtext" id="acym__popup__subscription__tagtext" onchange="setSubscriptionTag();">
                        </div>
                        <div class="medium-1"></div>
                        <div style="display: none;" id="select_lists_zone" class="grid-x cell medium-6 small-12 acym__listing__title acym__listing__title__dynamics">
                            <p class="shrink" id="acym__popup__subscription__text__list">'.acym_translation('ACYM_LISTS_SELECTED').'</p>
                            <p class="shrink" id="acym__popup__subscription__listids"></p>
                        </div>
                    </div>';
        $text .= '
					<div class="cell grid-x">';

        foreach ($others as $tagname => $tag) {
            $onclick = "changeSubscriptionTag('".$tagname."');";
            if ($tagname == 'subscribe') {
                $onclick .= 'displayLists();return false;';
            }
            $text .= '<div class="grid-x small-12 cell acym__row__no-listing acym__listing__row__popup text-left"  onclick="'.$onclick.'" id="tr_'.$tagname.'" >';
            $text .= '<div class="cell small-12 acym__listing__title acym__listing__title__dynamics">'.$tag['name'].'</div>';
            $text .= '</div>';
        }
        $text .= '</div>
					<div class="medium-1"></div>
                    <div class="medium-10 text-left">';
        $text .= acym_modalPaginationLists(
            'acym__popup__subscription__change',
            '',
            false,
            'style="display: none;" id="acym__popup__plugin__subscription__lists__modal"'
        );
        $text .= '  </div>
                    <div class="medium-1"></div>
				</div>';

        $others = [];
        $others['name'] = acym_translation('ACYM_LIST_NAME');
        $others['description'] = acym_translation('ACYM_LIST_DESCRIPTION');
        $others['names'] = acym_translation('ACYM_LIST_NAMES');
        $others['descriptions'] = acym_translation('ACYM_LIST_DESCRIPTIONS');
        $others['id'] = acym_translation('ACYM_LIST_ID', true);

        $text .= '<div class="acym__popup__listing text-center grid-x">
					<h1 class="acym__title acym__title__secondary text-center cell">'.acym_translation('ACYM_LIST').'</h1>
					<div class="cell grid-x">';

        foreach ($others as $tagname => $tag) {
            $text .= '<div class="grid-x medium-12 cell acym__row__no-listing acym__listing__row__popup text-left" onclick="changeSubscriptionTag(\'list\');setTag(\'{list:'.$tagname.'}\', jQuery(this));" id="tr_'.$tagname.'">
                        <div class="cell medium-12 small-12 acym__listing__title acym__listing__title__dynamics">'.$tag.'</div>
                      </div>';
        }

        $text .= '</div></div>';

        $text .= '<div class="acym__popup__listing text-center grid-x">
					<span class="acym__title acym__title__secondary text-center cell">'.acym_translation('ACYM_CAMPAIGN').'</span>
					<div class="cell grid-x">';
        $othersMail = ['campaignid', 'subject'];

        foreach ($othersMail as $tag) {
            $text .= '<div class="grid-x medium-12 cell acym__row__no-listing acym__listing__row__popup text-left" onclick="changeSubscriptionTag(\'mail\');setTag(\'{mail:'.$tag.'}\', jQuery(this));" id="tr_'.$tag.'">
                        <div class="cell medium-12 small-12 acym__listing__title acym__listing__title__dynamics">'.$tag.'</div>
                      </div>';
        }
        $text .= '</div></div>';

        $text .= '<div class="acym__popup__listing text-center grid-x">
					<span class="acym__title acym__title__secondary text-center cell">'.acym_translation('ACYM_AUTO').' '.acym_translation('ACYM_CAMPAIGNS').'</span>
					<div class="cell grid-x">';
        $autoMail = ['number_generated' => ['name' => acym_translation('ACYM_ISSUE_NB'), 'default' => '#1']];

        foreach ($autoMail as $tag => $oneTag) {
            $tagInserted = $tag;
            if (!empty($oneTag['default'])) $tagInserted = $tag.'|default:'.$oneTag['default'];
            $text .= '<div class="grid-x medium-12 cell acym__row__no-listing acym__listing__row__popup text-left" onclick="changeSubscriptionTag(\'automail\');setTag(\'{automail:'.$tagInserted.'}\', jQuery(this));" id="tr_'.$tag.'">
                        <div class="cell medium-12 small-12 acym__listing__title acym__listing__title__dynamics">'.$oneTag['name'].'</div>
                      </div>';
        }
        $text .= '</div></div>';

        echo $text;
    }

    public function replaceUserInformation(&$email, &$user, $send = true)
    {
        $this->_replacelisttags($email, $user, $send);

        if (empty($user->id) || !empty($this->addedListUnsubscribe[$email->id][$user->id])) return;
        if (empty($this->unsubscribeLink[$email->id]) || !method_exists($email, 'addCustomHeader')) return;


        $this->addedListUnsubscribe[$email->id][$user->id] = true;

        $mailto = '';
        if ($this->config->get('auto_bounce', 0)) {
            $mailto = $this->config->get('bounce_email');
        }
        if (empty($mailto)) {
            $mailto = empty($email->replyemail) ? $this->config->get('replyto_email') : $email->replyemail;
        }

        if (empty($mailto)) {
            return;
        }

        $body = 'Please%20unsubscribe%20user%20ID%20'.$user->id;

        if (!isset($this->mailLists[$email->id])) {
            $mailClass = new MailClass();
            $lists = $mailClass->getAllListsByMailId($email->id);
            $this->mailLists[$email->id] = empty($lists) ? null : array_keys($lists);
        }

        if (!empty($this->mailLists[$email->id])) {
            $userClass = $this->getUserClass();
            $userLists = $userClass->getSubscriptionStatus($user->id, [], 1);
            if (!empty($userLists)) {
                $commonLists = array_intersect($this->mailLists[$email->id], array_keys($userLists));

                if (!empty($commonLists)) {
                    $body .= '%20from%20list(s)%20'.implode(',', $commonLists).'.';
                }
            }
        }

        $unsubscribeLink = str_replace(
            ['{subscriber:id}', '{subscriber:key|urlencode}'],
            [$user->id, urlencode($user->key)],
            $this->unsubscribeLink[$email->id]
        );
        $email->addCustomHeader('List-Unsubscribe', '<'.$unsubscribeLink.'&ajax=1>, <mailto:'.$mailto.'?subject=unsubscribe_user_'.$user->id.'&body='.$body.'>');
        $email->addCustomHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
    }

    public function replaceContent(&$email, $send = true)
    {
        $this->_replaceSubscriptionTags($email);
        $this->_replacemailtags($email);
        $this->replaceAutomailTags($email);
    }

    private function _replacemailtags(&$email)
    {
        $result = $this->pluginHelper->extractTags($email, 'mail');
        $tags = [];

        foreach ($result as $key => $oneTag) {
            if (isset($tags[$key])) {
                continue;
            }

            $field = $oneTag->id;
            if (!empty($email) && !empty($email->$field)) {
                $text = $email->$field;
                $this->pluginHelper->formatString($text, $oneTag);
                $tags[$key] = $text;
            } elseif (substr($field, 0, 8) == 'campaign') {
                $this->getCampaignTags($email, $tags, $oneTag, $key);
            } else {
                $tags[$key] = $oneTag->default;
            }
        }

        $this->pluginHelper->replaceTags($email, $tags);
    }

    private function getCampaignTags(&$email, &$tags, $oneTag, $key)
    {
        $campaignClass = new CampaignClass();
        $campaignFromMail = $campaignClass->getOneCampaignByMailId($email->id);
        $campaignField = substr($oneTag->id, 8);
        if (!empty($campaignFromMail) && !empty($campaignFromMail->$campaignField)) {
            $text = $campaignFromMail->$campaignField;
            $this->pluginHelper->formatString($text, $oneTag);
            $tags[$key] = $text;
        } else {
            $tags[$key] = $oneTag->default;
        }
    }

    private function replaceAutomailTags(&$email)
    {
        $result = $this->pluginHelper->extractTags($email, 'automail');
        $tags = [];

        foreach ($result as $key => $oneTag) {
            if (isset($tags[$key])) {
                continue;
            }

            $field = $oneTag->id;

            $campaignClass = new CampaignClass();
            $autoCampaignFromMail = $campaignClass->getAutoCampaignFromGeneratedMailId($email->id);

            if (!empty($autoCampaignFromMail) && !empty($autoCampaignFromMail->sending_params[$field])) {
                $text = $autoCampaignFromMail->sending_params[$field];
                $this->pluginHelper->formatString($text, $oneTag);
                $tags[$key] = $text;
            } else {
                $tags[$key] = $oneTag->default;
            }
        }

        $this->pluginHelper->replaceTags($email, $tags);
    }

    private function _replacelisttags(&$email, &$user, $send)
    {
        $tags = $this->pluginHelper->extractTags($email, 'list');
        if (empty($tags)) {
            return;
        }

        $replaceTags = [];
        foreach ($tags as $oneTag => $parameter) {
            $method = 'list'.trim(strtolower($parameter->id));

            if (method_exists($this, $method)) {
                $replaceTags[$oneTag] = $this->$method($email, $user, $parameter);
            } else {
                $replaceTags[$oneTag] = 'Method not found: '.$method;
            }
        }

        $this->pluginHelper->replaceTags($email, $replaceTags, true);
    }

    private function _getAttachedListid($email, $subid)
    {
        $mailid = $email->id;

        if (isset($this->lists[$mailid][$subid])) {
            return $this->lists[$mailid][$subid];
        }

        $mailClass = new MailClass();
        $mailLists = array_keys($mailClass->getAllListsByMailId($mailid));
        $userLists = [];

        if (!empty($subid)) {
            $userClass = $this->getUserClass();
            $userLists = $userClass->getUserSubscriptionById($subid, 'id', false, false, false, true);

            $listid = null;
            foreach ($userLists as $id => $oneList) {
                if ($oneList->status == 1 && in_array($id, $mailLists)) {
                    $this->lists[$mailid][$subid] = $id;

                    return $this->lists[$mailid][$subid];
                }
            }

            if (!empty($listid)) {
                $this->lists[$mailid][$subid] = $listid;

                return $listid;
            }
        }

        if (!empty($mailLists)) {
            $this->lists[$mailid][$subid] = array_shift($mailLists);

            return $this->lists[$mailid][$subid];
        }

        if (!empty($subid) && !empty($email->type) && $email->type === MailClass::TYPE_WELCOME) {
            $listid = acym_loadResult(
                'SELECT list.id 
				FROM #__acym_list AS list 
				JOIN #__acym_user_has_list AS userlist ON list.id = userlist.list_id 
				WHERE list.welcome_id = '.intval($mailid).' AND userlist.user_id = '.intval($subid).' 
				ORDER BY userlist.subscription_date DESC'
            );
            if (!empty($listid)) {
                $this->lists[$mailid][$subid] = $listid;

                return $listid;
            }
        }

        if (!empty($subid) && !empty($email->type) && $email->type === MailClass::TYPE_UNSUBSCRIBE) {
            $listid = acym_loadResult(
                'SELECT list.id 
				FROM #__acym_list AS list 
				JOIN #__acym_user_has_list AS userlist ON list.id = userlist.list_id 
				WHERE list.unsubscribe_id = '.intval($mailid).' AND userlist.user_id = '.intval($subid).' 
				ORDER BY userlist.unsubscribe_date DESC'
            );
            if (!empty($listid)) {
                $this->lists[$mailid][$subid] = $listid;

                return $listid;
            }
        }

        if (!empty($userLists)) {
            $listIds = array_keys($userLists);
            $this->lists[$mailid][$subid] = array_shift($listIds);

            return $this->lists[$mailid][$subid];
        }

        return 0;
    }

    private function listnames(&$email, &$user, &$parameter)
    {
        if (empty($user->id)) return '';

        $userClass = $this->getUserClass();
        $usersubscription = $userClass->getUserSubscriptionById($user->id, 'id', false, true);
        $lists = [];
        if (!empty($usersubscription)) {
            foreach ($usersubscription as $onesub) {
                if ($onesub->status < 1 || empty($onesub->active)) {
                    continue;
                }
                $lists[] = (!empty($onesub->display_name) ? $onesub->display_name : $onesub->name);
            }
        }

        return implode(isset($parameter->separator) ? $parameter->separator : ', ', $lists);
    }

    private function listowner(&$email, &$user, &$parameter)
    {
        if (empty($user->id)) {
            return '';
        }

        if (!in_array($parameter->field, ['username', 'name', 'email'])) {
            return 'Field not found : '.$parameter->field;
        }

        $listid = $this->_getAttachedListid($email, $user->id);
        if (empty($listid)) {
            return '';
        }

        global $acymCmsUserVars;
        if (!isset($this->listsowner[$listid])) {
            $this->listsowner[$listid] = acym_loadObject(
                'SELECT `user`.* FROM #__acym_list AS `list` 
				JOIN '.$acymCmsUserVars->table.' AS `user` 
					ON `user`.'.$acymCmsUserVars->id.' = `list`.cms_user_id 
				WHERE `list`.id = '.intval($listid)
            );
        }

        return @$this->listsowner[$listid]->{$acymCmsUserVars->{$parameter->field}};
    }

    private function listname(&$email, &$user, &$parameter)
    {
        if (empty($user->id)) {
            return '';
        }
        $listid = $this->_getAttachedListid($email, $user->id);
        if (empty($listid)) {
            return '';
        }

        $this->_loadlist($listid);

        return !empty($this->listsinfo[$listid]->display_name) ? $this->listsinfo[$listid]->display_name : @$this->listsinfo[$listid]->name;
    }

    private function listdescription(&$email, &$user, &$parameter)
    {
        if (empty($user->id)) {
            return '';
        }
        if (!empty($parameter->listid)) $listid = $parameter->listid;
        if (empty($listid)) $listid = $this->_getAttachedListid($email, $user->id);
        if (empty($listid)) {
            return '';
        }

        $this->_loadlist($listid);

        return @$this->listsinfo[$listid]->description;
    }

    private function _loadlist($listid)
    {
        if (isset($this->listsinfo[$listid])) {
            return;
        }

        $listClass = new ListClass();
        $this->listsinfo[$listid] = $listClass->getOneById(intval($listid));
    }

    private function listdescriptions(&$email, &$user, &$parameter)
    {
        if (empty($user->id)) return '';

        $userClass = $this->getUserClass();
        $usersubscription = $userClass->getUserSubscriptionById($user->id);
        $listids = [];
        if (!empty($parameter->listids)) $listids = explode(',', $parameter->listids);
        $lists = [];
        if (!empty($usersubscription)) {
            foreach ($usersubscription as $onesub) {
                if (empty($onesub->description) || $onesub->status < 1 || empty($onesub->active) || (!empty($listids) && !in_array($onesub->id, $listids))) {
                    continue;
                }
                $lists[] = $onesub->description;
            }
        }

        return implode(isset($parameter->separator) ? $parameter->separator : ', ', $lists);
    }

    private function listid(&$email, &$user, &$parameter)
    {
        if (empty($user->id)) {
            return '';
        }
        $listid = $this->_getAttachedListid($email, $user->id);
        if (empty($listid)) {
            return '';
        }

        return $listid;
    }

    private function _replaceSubscriptionTags(&$email)
    {
        $match = '#(?:{|%7B)(confirm|unsubscribe(?:\|[^}]+)*|unsubscribeall|subscribe(?:\|[^}]+)*)(?:}|%7D)(.*)(?:{|%7B)/(confirm|unsubscribe|unsubscribeall|subscribe)(?:}|%7D)#Uis';
        $variables = ['subject', 'body'];
        $found = false;
        $results = [];
        foreach ($variables as $var) {
            if (empty($email->$var)) continue;

            $found = preg_match_all($match, $email->$var, $results[$var]) || $found;
            if (empty($results[$var][0])) unset($results[$var]);
        }

        if (!$found) return;

        $tags = [];
        $this->addedListUnsubscribe[$email->id] = [];
        foreach ($results as $var => $allresults) {
            foreach ($allresults[0] as $i => $oneTag) {
                if (isset($tags[$oneTag])) continue;

                $tags[$oneTag] = $this->_replaceSubscriptionTag($allresults, $i, $email);
            }
        }

        $this->pluginHelper->replaceTags($email, $tags);
    }

    private function _replaceSubscriptionTag(&$allresults, $i, &$email)
    {
        $parameters = $this->pluginHelper->extractTag($allresults[1][$i]);

        $lang = $this->getLanguage($email->links_language);

        if ($parameters->id === 'confirm') {
            $myLink = acym_frontendLink('frontusers&task=confirm&userId={subscriber:id}&userKey={subscriber:key|urlencode}'.$lang);
            if (empty($allresults[2][$i])) {
                return $myLink;
            }

            return '<a target="_blank" href="'.$myLink.'"><span class="acym_confirm acym_link">'.$allresults[2][$i].'</span></a>';
        } elseif ($parameters->id === 'subscribe') {
            if (empty($parameters->lists)) {
                return acym_translation('ACYM_EXPORT_SELECT_LIST');
            }
            $lists = explode(',', $parameters->lists);
            acym_arrayToInteger($lists);
            $captchaKey = $this->config->get('captcha', 'none') !== 'none' ? '&seckey='.$this->config->get('security_key', '') : '';
            $myLink = acym_frontendLink(
                'frontusers&task=subscribe&hiddenlists='.implode(',', $lists).'&userId={subscriber:id}&userKey={subscriber:key|urlencode}'.$lang.$captchaKey
            );
            if (empty($allresults[2][$i])) {
                return $myLink;
            }

            return '<a style="text-decoration:none;" target="_blank" href="'.$myLink.'"><span class="acym_subscribe acym_link">'.$allresults[2][$i].'</span></a>';
        } else {
            $baseLink = 'frontusers'.$lang.'&mail_id='.$email->id;
            if ($parameters->id === 'unsubscribe') {
                $unsubscribeLink = $baseLink.'&task=unsubscribe&userId={subscriber:id}&userKey={subscriber:key|urlencode}';
				$unsubscribeLink .= '&'.acym_noTemplate();
                $unsubClass = 'acym_unsubscribe';
            } else {
                $unsubscribeLink = $baseLink.'&task=unsubscribeAll&user_id={subscriber:id}&user_key={subscriber:key|urlencode}';
                $unsubClass = 'acym_unsubscribe_all_lists';
            }

            $needToCompleteLink = true;
            if (!empty($parameters->baseUrl)) {
                $unsubscribeLink = $parameters->baseUrl.$unsubscribeLink;
                $needToCompleteLink = false;
            }

            $unsubscribeLink = acym_frontendLink($unsubscribeLink, $needToCompleteLink);

            $this->unsubscribeLink[$email->id] = $unsubscribeLink;

            if (empty($allresults[2][$i])) {
                return $unsubscribeLink;
            }

            return '<a style="text-decoration:none;" target="_blank" href="'.$unsubscribeLink.'"><span class="'.$unsubClass.' acym_link">'.$allresults[2][$i].'</span></a>';
        }
    }

    private function getUserClass()
    {
        if ($this->userClass === null) {
            $this->userClass = new UserClass();
        }

        return $this->userClass;
    }
}
