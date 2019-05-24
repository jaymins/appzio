<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.article.controllers.*');

Yii::import('application.modules.aelogic.packages.actionMobiledates.models.*');

class dittoConfirmationMobiledates extends dittoMobiledatesSubController {

    public function tab1(){

        $this->data = new stdClass();

        $this->data->scroll[] = $this->getText( 'You are about to cancel all your current plans. Please confirm!', array( 'style' => 'confirmation-text' ) );

        $this->data->footer[] = $this->getRow(array(
            $this->getColumn(array(
                $this->getText('Confirm', array( 'style' => 'ditto-button-pink', 'onclick' => $this->submitConfirmationPopup() ))
            ), array(
                'width' => '50%',
                'text-align' => 'center',
                'vertical-align' => 'middle',
                'padding' => '0 5 0 10',
            )),
            $this->getColumn(array(
                $this->getText('Ignore', array( 'style' => 'ditto-button-white', 'onclick' => $this->closeConfirmationPopup() ))
            ), array(
                'width' => '50%',
                'text-align' => 'center',
                'vertical-align' => 'middle',
                'padding' => '0 10 0 5',
            )),
        ), array(
            'margin' => '10 0 10 0',
        ));

        return $this->data;
    }

    public function submitConfirmationPopup() {
        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->action_config = $this->getActionidByPermaname( 'datemanager' )  ;
        $onclick->id = 'cancel-accepters-plans';
        $onclick->sync_open = 1;
        return $onclick;
    }

    public function closeConfirmationPopup() {
        $onclick = new StdClass();
        $onclick->action = 'close-popup';
        $onclick->keep_user_data = 1;
        return $onclick;
    }

}