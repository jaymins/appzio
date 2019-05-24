<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

class HideMobilematching extends MobilematchingController
{
    public $source;

//    public function tab1()
//    {
//        $this->data = new stdClass();
//
//        if (strstr($this->menuid, 'save')) {
//            $this->source = explode('|', $this->menuid)[1];
//            $this->saveHiddenUsers();
//            $this->submitvariables['search'] = '';
//        }
//
//        $this->rewriteActionConfigField('background_color', '#f9fafb');
//
//        $this->initMobileMatching();
//        $this->source = 'friends';
//
//        $this->setTopBar();
//
//        $this->data->header[] = $this->getColumn(array(
//            $this->setHeader(),
//            $this->getRow(array(
//                $this->getImage('search_glass.png', array(
//                    'width' => '25',
//                    'margin' => '7 15 0 0'
//                )),
//                $this->getFieldtext('', array(
//                    'hint' => '{#search_by_name#}',
//                    'submit_on_entry' => '1',
//                    'variable' => 'search',
//                    'keep-open' => 1,
//                    'width' => '100%',
//                ))
//            ), array(
//                'vertical-align' => 'middle',
//                'padding' => '10 10 10 10',
//            ))
//        ), array(
//            'background-color' => '#f9fafb',
//            'vertical-align' => 'middle',
//        ));
//
//        $facebookToken = $this->getSavedVariable('fb_token');
//
//        if (!$facebookToken) {
//            return $this->data;
//        }
//
//        $allFriends = ThirdpartyServices::getUserFbFriends($this->getSavedVariable('fb_token'), $this->appinfo->fb_api_id, $this->appinfo->fb_api_secret);
//        $allFriends = $this->formatFriendsList($allFriends);
//
//        $allFriends = $this->filterHidden($allFriends);
//
//        $this->renderHiddenFriends($allFriends);
//
//        $this->renderSaveButton();
//
//        return $this->data;
//    }

    private function filterHidden($allFriends)
    {
        $friendName = isset($this->submitvariables['search']) ? $this->submitvariables['search'] : '';

        $allFriends = array_map(function($friend) use ($friendName) {
            if ((!stristr($friend['real_name'], $friendName) && !empty($friendName))) {
                $friend['filtered'] = 1;
            }

            return $friend;
        }, $allFriends);

        if (empty($allFriends)) {
            return;
        }

        $facebookIds = array_map(function ($friend) {
            return $friend['fb_id'];
        }, $allFriends);

        $facebookIds = implode(', ', $facebookIds);

        $sql = "
            SELECT
                ae_game_play_variable.id,
                ae_game_play_variable.value,
                ae_game_variable.id,
                ae_game_variable.name,
                ae_game_play_variable.play_id
            FROM ae_game_play_variable
            LEFT JOIN ae_game_variable ON ae_game_play_variable.variable_id = ae_game_variable.id
    
            WHERE `value` IN ($facebookIds)
            AND ae_game_variable.`name` = 'fb_id'
            AND ae_game_variable.game_id = $this->gid
            
            ORDER BY play_id DESC
        ";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->queryAll();

        $facebookId = $this->getVariable('fb_id');
        $toBeFiltered = array();

        foreach ($rows as $row) {
            $playId = $row['play_id'];
            $hiddenFriends = json_decode($this->getPlayVariables($playId)['hidden_friends']);
            if (!empty($hiddenFriends) && in_array($facebookId, $hiddenFriends)) {
                $toBeFiltered[] = $row['value'];
            }
        }

        $filteredFriends = array_filter($allFriends, function ($friend) use ($toBeFiltered) {
            return !in_array($friend['fb_id'], $toBeFiltered);
        });

        return $filteredFriends;
    }

    private function formatFriendsList($allFriends)
    {
        $friends = array_map(function ($friend) {
            return [
                'real_name' => $friend['name'],
                'profilepic' => $friend['picture']['data']['url'],
                'fb_id' => $friend['id']
            ];
        }, $allFriends);

        return $friends;
    }

    public function tab1()
    {

        $this->rewriteActionConfigField('background_color', '#f9fafb');

        $this->data = new stdClass();
        $this->initMobileMatching();
        $this->source = 'users';

        $this->setTopBar();

        $this->data->header[] = $this->getColumn(array(
//            $this->setHeader(),
            $this->getRow(array(), array(
                'padding' => '3 0 3 0',
            ))
        ));

        $hiddenAppUsers = $this->getHiddenUserIds('hidden_users');
        $users = $this->mobilematchingobj->getHiddenUsers($hiddenAppUsers);

        $this->renderHiddenUsers($users);
        $this->renderSaveButton();

        return $this->data;
    }

    private function getHiddenUserIds($list)
    {
        $hiddenIds = json_decode($this->getSavedVariable($list));
        return $hiddenIds;
    }

    private function renderHiddenUsers($users)
    {
        if (empty($users)) {
            $this->renderNoUsersMessage();
            return;
        }

        foreach ($users as $user) {
            $variables = $vars = AeplayVariable::getArrayOfPlayvariables($user);

            if (!isset($variables['profilepic'])) {
                continue;
            }

            $variables['active'] = 1;
            $this->renderSingleHiddenUser($variables);
        }
    }

    private function renderHiddenFriends($friends)
    {
        if (empty($friends)) {
            $this->renderNoUsersMessage();
            return;
        }

        $hiddenFacebookUsers = $this->getHiddenUserIds('hidden_friends');

        foreach ($friends as $friend) {
            if (!empty($hiddenFacebookUsers) && in_array($friend['fb_id'], $hiddenFacebookUsers)) {
                $friend['active'] = 1;
            }
            $this->renderSingleHiddenUser($friend);
        }
    }

    private function renderSingleHiddenUser($variables)
    {
        $active = false;

        if (isset($variables['active']) || in_array($variables['fb_id'], array_filter($this->submitvariables))) {
            $active = true;
        }

        $selectedState = array(
            'style' => 'radio_selected_state',
            'allow_unselect' => 1,
            'animation' => 'fade',
            'variable_value' => $variables['fb_id'],
            'active' => $active
        );

        $this->data->scroll[] = $this->getHairline('#f3f3f3', array(
            'visibility' => isset($variables['filtered']) ? 'hidden' : ''
        ));

        $this->data->scroll[] = $this->getRow(array(
            $this->getImage($variables['profilepic'], array(
                'crop' => 'yes',
                'vertical-align' => 'middle',
                'text-align' => 'center',
                'margin' => '5 1 5 1',
                'width' => '50',
                'height' => '50',
                'priority' => 9,
                'border-radius' => '3'
            )),
            $this->getText($variables['real_name'], array(
                'margin' => '0 0 0 20'
            )),
            $this->getRow(array(
                $this->getText('', array(
                    'style' => 'radio_default_state',
                    'variable' => $variables['fb_id'],
                    'selected_state' => $selectedState
                ))
            ), array(
                'width' => '20%',
                'floating' => 1,
                'float' => 'right',
                'padding' => '12 0 0 0',
                'vertical-align' => 'middle',
            ))
        ), array(
            'padding' => '5 15 5 10',
            'vertical-align' => 'middle',
            'background-color' => '#ffffff',
            'visibility' => isset($variables['filtered']) ? 'hidden' : ''
        ));
    }

    private function renderSaveButton()
    {
        $this->data->footer[] = $this->getTextbutton('Save', array(
            'id' => 'save|' . $this->source,
            'style' => 'desee_general_button_style_footer'
        ));
    }

    private function renderNoUsersMessage()
    {
        $this->data->scroll[] = $this->getText('You are not invisible from anyone yet', array(
            'text-align' => 'center',
            'margin' => '20 0 0 0'
        ));
    }

    private function setHeader()
    {
        $tabs = array(
            'tab1' => '{#facebook_users#}',
            'tab2' => '{#app_users#}'
        );

        $params = array(
            'color' => $this->colors['top_bar_text_color'],
            'color_topbar' => $this->topbar_color,
            'color_topbar_hilite' => $this->colors['top_bar_text_color'],
            'btn_padding' => '12 10 12 10',
            'font-size' => '16',
            'divider' => true,
        );

        return $this->getRow(array(
            $this->getTabs( $tabs, $params )
        ), array(
            'background-color' => '#ffffff',
            'shadow-color' => '#33000000',
            'shadow-radius' => 3,
            'shadow-offset' => '0 1',
        ));
    }

    private function setTopBar() {
        $toggleSidemenu = new stdClass();
        $toggleSidemenu->action = 'open-sidemenu';

        $this->data->header[] = $this->getRow(array(
            $this->getImage('ic_menu_new.png', array(
                'width' => '20',
                'onclick' => $toggleSidemenu
            )),
            $this->getText('{#invisible_list#}', array(
                'color' => '#ff6600',
                'width' => '90%',
                'text-align' => 'center',
            ))
        ), array(
            'background-color' => '#ffffff',
            'padding' => '10 20 10 20',
            'width' => '100%',
        ));

        return true;
    }

    private function saveHiddenUsers()
    {
//        if (isset($this->submitvariables['search']) && !empty($this->submitvariables['search'])) {
//
//            unset($this->submitvariables['search']);
//
//            foreach ($this->submitvariables as $key => $value) {
//                if (empty($value)) {
//                    $this->removeFromVariable('hidden_' . $this->source, $key);
//                } else {
//                    $this->addToVariable('hidden_' . $this->source, $key);
//                }
//            }
//
//            $users = json_decode($this->getVariable('hidden_' . $this->source));
//            $this->saveVariable('hidden_' . $this->source, json_encode(array_values((array)$users)));
//
//            return;
//        }

        unset($this->submitvariables['search']);

        $users = array();

        foreach ($this->submitvariables as $key => $value) {
            $users[] = $value;
        }

        // Filter array to make sure there are no blank values
        $users = array_filter($users);
        $users = array_values($users);

        $this->saveVariable('hidden_' . $this->source, json_encode($users));
    }

}