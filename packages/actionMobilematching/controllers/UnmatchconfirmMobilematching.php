<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

class UnmatchconfirmMobilematching extends MobilematchingController
{
    public $source;

    public function tab1()
    {
        $this->data = new stdClass();

        if (strstr($this->menuid, 'unmatch_')) {
            $playId = str_replace('unmatch_', '', $this->menuid);
            $this->initMobileMatching($playId);
            $this->unmatchUser($playId);
        }

        $playId = $this->sessionGet('lastOpenedUser');
        $this->sessionSet('lastOpenedUser', null);
        $variables = AeplayVariable::getArrayOfPlayvariables($playId);

        $this->data->header[] = $this->getText('{#unmatch#}', array(
            'text-align' => 'center',
            'background-color' => '#C7003A',
            'color' => '#FFFFFF',
            'padding' => '20 0 20 0',
            'margin' => '0 0 0 0',
        ));

        $this->data->scroll[] = $this->getText('{#are_you_sure_you_want_to_unmatch#} ' . ucfirst($this->getFirstName($variables)) . '?', array(
            'padding' => '20 30 20 30'
        ));

        $clicker = array();
        $onclick = new stdClass();
        $onclick->id = 'unmatch_' . $playId;
        $onclick->action = 'submit-form-content';

        $clicker[] = $onclick;
        $clicker[] = $this->getOnclick('close-popup');

        $this->data->footer[] = $this->getRow(array(
            $this->getText('{#no#}', array(
                'width' => '50%',
                'background-color' => '#FAFAFA',
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

    /**
     * Unmatch a user
     *
     * @param $playId
     */
    private function unmatchUser($playId)
    {
        $this->mobilematchingobj->removeTwoWayMatches(false);

        $unmatchedUsers = json_decode($this->getVariable('unmatched_users'));
        $isUnmatched = false;

        if (!empty($unmatchedUsers)) {
            foreach ($unmatchedUsers as $unmatchedUser) {
                if ($unmatchedUser[0] == $playId) {
                    $isUnmatched = true;
                }
            }
        }

        if (!$isUnmatched) {
            $this->addToVariable('unmatched_users', array($playId, time()));
        }

        $otherUserVariables = $this->getPlayVariables($playId);

        if (isset($otherUserVariables['unmatched_me'])) {
            $otherUserUnmatchers = json_decode($otherUserVariables['unmatched_me']);
        } else {
            $otherUserUnmatchers = array();
        }

        $otherUserUnmatchers[] = array($this->playid, time());
        $this->saveRemoteVariable('unmatched_me', json_encode($otherUserUnmatchers), $playId);

        $notificationTitle = 'You have been unmatched';
        $notificationText = $this->getVariable('real_name') . ' has unmatched you';
        Aenotification::addUserNotification($playId, $notificationTitle, $notificationText, 0, $this->gid);
    }
}