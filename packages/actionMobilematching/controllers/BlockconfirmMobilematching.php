<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

class BlockconfirmMobilematching extends MobilematchingController
{
    public function tab1()
    {
        $this->data = new stdClass();
        $playId = $this->sessionGet('lastOpenedUser');
        $this->sessionSet('lastOpenedUser', null);
        $variables = AeplayVariable::getArrayOfPlayvariables($playId);

        if (strstr($this->menuid, 'block_user_')) {
            $id = str_replace('block_user_', '', $this->menuid);
            $this->blockUser($id);
            $this->no_output = 1;
            return $this->data;
        }

        $this->data->header[] = $this->getText('{#block_user#}', array(
            'text-align' => 'center',
            'background-color' => '#C7003A',
            'color' => '#FFFFFF',
            'padding' => '20 0 20 0',
            'margin' => '0 0 0 0',
        ));

        $this->renderBlockReasons();

        $clicker = array();
        $onclick = new stdClass();
        $onclick->id = 'block_user_' . $playId;
        $onclick->action = 'submit-form-content';

        $clicker[] = $onclick;
        $clicker[] = $this->getOnclick('close-popup');

        $this->data->footer[] = $this->getRow(array(
            $this->getFieldonoff(0, array(
                'variable' => 'superblock',
                'margin' => '10 20 10 0',
            )),
            $this->getRow(array(
                $this->getText('Superblock', array(
                    'color' => '#C7003A'
                )),
                $this->getText(' this user')
            ))
        ), array(
            'text-align' => 'center',
            'background-color' => '#F9FAFB'
        ));
        $this->data->footer[] = $this->getRow(array(
            $this->getText('{#no#}', array(
                'width' => '50%',
                'background-color' => '#DFDFDF',
                'color' => '#000000',
                'text-align' => 'center',
                'padding' => '15 0 15 0',
                'font-size' => '16',
                'onclick' => $this->getOnclick('close-popup')
            )),
            $this->getText(ucfirst('{#yes#}'), array(
                'width' => '50%',
                'background-color' => '#FFC204',
                'color' => '#000000',
                'text-align' => 'center',
                'padding' => '15 0 15 0',
                'font-size' => '16',
                'onclick' => $clicker
            ))
        ));

        return $this->data;
    }

    private function renderBlockReasons()
    {
        // If images are not prepared up front they will not be rendered
        $this->copyAssetWithoutProcessing('circle_non_bg.png');
        $this->copyAssetWithoutProcessing('circle_selected_bg.png');

        $reasons = array(
            '{#offensive_photos#}',
            '{#fake_profile#}',
            '{#offensive_description#}',
            '{#other_reasons#}'
        );
        $selectedState = array('style' => 'radio_selected_state', 'allow_unselect' => 1, 'animation' => 'fade');

        foreach ($reasons as $reason) {
            $selectedState['variable_value'] = $reason;

            $this->data->scroll[] = $this->getRow(array(
                $this->getText(ucfirst($reason), array(
                    'padding' => '15 10 15 20'
                )),
                $this->getRow(array(
                    $this->getText('', array(
                        'style' => 'radio_default_state',
                        'variable' => 'block_reason',
                        'selected_state' => $selectedState
                    ))
                ), array(
                    'width' => '40%',
                    'floating' => 1,
                    'float' => 'right'
                ))
            ));
            $this->data->scroll[] = $this->getHairline('#f3f3f3');
        }
    }

    public function blockUser($id)
    {
        $this->initMobileMatching($id);
        $this->mobilematchingobj->removeTwoWayMatches(false);

        $blockedUsers = json_decode($this->getVariable('blocked_users'));
        $isBlocked = false;

        if (!empty($blockedUsers)) {
            foreach ($blockedUsers as $blockedUser) {
                if ($blockedUser[0] == $id) {
                    $isBlocked = true;
                }
            }
        }

        if (!$isBlocked) {
            $this->addToVariable('blocked_users', array(
                $id,
                time(),
                $this->submitvariables['block_reason'],
                $this->submitvariables['superblock']
            ));
        }
    }
}