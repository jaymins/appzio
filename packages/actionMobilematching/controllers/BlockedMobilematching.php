<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

class BlockedMobilematching extends MobilematchingController
{
    public function tab1()
    {
        $this->data = new stdClass();

        if (strstr($this->menuid, 'unblock_')) {
            $id = str_replace('unblock_', '', $this->menuid);
            $this->unblockUser($id);
        }

        $blockedUsers = json_decode($this->getVariable('blocked_users'));

        if (empty($blockedUsers)) {
            $this->renderNoBlockedUsersMessage();
            return $this->data;
        }

        foreach ($blockedUsers as $blockedUser) {
            $user = AeplayVariable::getArrayOfPlayvariables($blockedUser[0]);
            $timeOfBlock = $blockedUser[1];
            $this->renderBlockedUser($user, $timeOfBlock, $blockedUser[0]);
        }

        return $this->data;
    }

    private function renderBlockeduser($user, $timeOfBlock, $playId)
    {
        $time = time() - $timeOfBlock;
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$time");
        $days = (int)$dtF->diff($dtT)->d;

        if( $days > 1 ) {
            $daysSinceBlock = $days . ' days ago';
        } else if ($days == 1) {
            $daysSinceBlock = '1 day ago';
        } else {
            $daysSinceBlock = 'Today';
        }

        $this->data->scroll[] = $this->getHairline('#f3f3f3');

        $age = date('Y') - $user['birth_year'];

        $this->data->scroll[] = $this->getRow(array(
            $this->getImage($user['profilepic'], array(
                'crop' => 'round',
                'vertical-align' => 'middle',
                'text-align' => 'center',
                'margin' => '5 1 5 1',
                'width' => '60',
                'height' => '60',
                'priority' => 9,
                'border-radius' => '3'
            )),
            $this->getColumn(array(
                $this->getText($user['real_name'] . ', ' . $age, array(
                    'margin' => '0 0 5 0'
                )),
                $this->getRow(array(
                    $this->getTextbutton('{#unblock#}', array(
                        'id' => 'unblock_' . $playId,
                        'style' => 'unblock_button'
                    )),
                    $this->getText('{#blocked#} ' . $daysSinceBlock, array(
                        'style' => 'blocked_time_text'
                    ))
                ))
            ), array(
                'margin' => '0 0 0 20',
                'vertical-align' => 'middle'
            ))
        ), array(
            'padding' => '5 15 5 10'
        ));
    }

    private function renderNoBlockedUsersMessage()
    {
        $this->data->scroll[] = $this->getText('{#there_are_no_blocked_users#}', array(
            'text-align' => 'center',
            'padding' => '10 10 10 10'
        ));
    }

    private function unblockUser($id)
    {
        $blockedUsers = json_decode($this->getVariable('blocked_users'));

        $blockedUsers = array_filter($blockedUsers, function($user) use ($id) {
            return $user[0] == $id ? false : true;
        });

        $this->saveVariable('blocked_users', json_encode($blockedUsers));

        $this->initMobileMatching($id);
        $this->mobilematchingobj->saveMatch();
        $this->mobilematchingobj->playid_otheruser = $this->playid;
        $this->mobilematchingobj->playid_thisuser = $id;
        $this->mobilematchingobj->saveMatch();
    }
}