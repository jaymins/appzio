<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

class HideconfirmMobilematching extends MobilematchingController
{
    public function tab1()
    {
        $this->data = new stdClass();
        $playId = $this->sessionGet('lastOpenedUser');
        $this->sessionSet('lastOpenedUser', null);
        $variables = AeplayVariable::getArrayOfPlayvariables($playId);

        if (strstr($this->menuid, 'hide_user_')) {
            $facebookId = str_replace('hide_user_', '', $this->menuid);
            $this->hideUser($facebookId);
            $this->no_output = 1;
            return $this->data;
        }

        $this->data->header[] = $this->getText('{#hide_from#}', array(
            'text-align' => 'center',
            'background-color' => '#C7003A',
            'color' => '#FFFFFF',
            'padding' => '20 0 20 0',
            'margin' => '0 0 0 0',
        ));

        $this->data->scroll[] = $this->getText('{#are_you_sure_you_want_to_hide_from#} ' . ucfirst($this->getFirstName($variables) . '?'), array(
            'padding' => '20 30 20 30'
        ));

        $id = isset($variables['fb_id']) ? $variables['fb_id'] : '';

        $clicker = array();
        $onclick = new stdClass();
        $onclick->id = 'hide_user_' . $id;
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

    public function hideUser($facebookId)
    {
        $hiddenUsers = json_decode($this->getVariable('hidden_users'));

        if (is_null($hiddenUsers) || !in_array($facebookId, $hiddenUsers)) {
            $this->addToVariable('hidden_users', $facebookId);
        }
    }
}