<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.article.controllers.*');

class rantevuPaymentsMobilematching extends rantevuMobilematchingSubController {

    public $current_time;
    public $default_time = 86400;

    public function tab1(){

        $this->current_time = time();

        $this->data = new stdClass();

        $this->initMobileMatching( $this->playid );

        // Action after return
        if (
            ( isset($_REQUEST['purchase_completed']) AND $_REQUEST['purchase_completed'] == '1' ) OR
            $this->getSavedVariable( 'purchase_boost_profile09' )
        ) {
            $this->mobilematchingobj->obj_thisuser->is_boosted = 1;
            $this->mobilematchingobj->obj_thisuser->boosted_timestamp = time();
            $this->mobilematchingobj->obj_thisuser->update();

            $this->deleteVariable( 'purchase_boost_profile09' );
        }

        if ( isset($this->mobilematchingobj->obj_thisuser->is_boosted) AND $this->mobilematchingobj->obj_thisuser->is_boosted ) {
            $this->getBoost();
        } else {
            $this->getPaymentText();
            $this->getPaymentButton();
        }

        return $this->data;
    }

    public function getPaymentText() {
        $this->data->scroll[] = $this->getImage( 'icon-boost.png', array( 'style' => 'rantevu-payments-icon' ) );
        $this->data->scroll[] = $this->getText( 'Κάνε Boost στο Προφίλ σου!', array( 'style' => 'rantevu-payments-heading' ) );
        $this->data->scroll[] = $this->getText( 'Θες περισσότερα ταιριάσματα; Εμφανίσου στην κορυφή των αναζητήσεων για 24 ώρες!', array( 'style' => 'rantevu-payments-description' ) );
        $this->data->scroll[] = $this->getText( '(0.99 ευρώ / μέρα)', array( 'style' => 'rantevu-payments-pricing' ) );
    }

    public function getBoost() {

        $options['mode'] = 'countdown';
        $options['style'] = 'rantevu-payments-timer';
        $options['timer_id'] = 'rantevu-timer';

        $initial_time = $this->mobilematchingobj->obj_thisuser->boosted_timestamp;
        $seconds_left = $this->default_time - ( $this->current_time - $initial_time );

        if ( $seconds_left < 0 ) {

            // Reset the booster
            $this->mobilematchingobj->obj_thisuser->is_boosted = 0;
            $this->mobilematchingobj->obj_thisuser->boosted_timestamp = null;
            $this->mobilematchingobj->obj_thisuser->update();

            $this->getPaymentText();
            $this->getPaymentButton();
            return false;
        }

        $this->data->scroll[] = $this->getImage( 'icon-boost.png', array( 'style' => 'rantevu-payments-icon' ) );
        $this->data->scroll[] = $this->getText( 'Συγχαρητήρια!', array( 'style' => 'rantevu-payments-heading' ) );
        $this->data->scroll[] = $this->getText( 'Εμφανίζεσαι στην κορυφή των αναζητήσεων για 24 ώρες! ', array( 'style' => 'rantevu-payments-description' ) );
        $this->data->scroll[] = $this->getRow(array(
            $this->getText('(Υπόλοιπο χρόνου ', array(
                'style' => 'rantevu-payments-timer',
            )),
            $this->getTimer($seconds_left, $options),
            $this->getText(')', array(
                'style' => 'rantevu-payments-timer',
            )),
        ), array(
            'style' => 'rantevu-payments-pricing-wrapper'
        ));

        $onclick = new StdClass();
        $onclick->action = 'close-popup';
        $onclick->keep_user_data = 1;
        
        $this->data->scroll[] = $this->getRow(array(
            $this->getText('Συνέχισε την αναζήτηση', array(
                'style' => 'rantevu-timer'
            )),
        ), array(
            'background-color' => '#ed4130',
            'margin' => '0 35 0 35',
            'padding' => '10 15 10 15',
            'border-radius' => '5',
            'text-align' => 'center',
            'onclick' => $onclick,
        ));

    }

    public function getPaymentButton() {

        $id_android = 'rantevu_boost';
        $id_ios = 'boost_profile09';

        $onclick = new StdClass();
        $onclick->action = 'inapp-purchase';
        $onclick->id = 'product_id';
        $onclick->product_id_ios = $id_ios; // Should be replaced with an id
        $onclick->product_id_android = $id_android;
        $onclick->producttype_android = 'inapp';
        $onclick->producttype_ios = 'inapp';

        $this->data->scroll[] = $this->getRow(array(
            $this->getTextbutton('Απόκτησε το', array(
                'id' => 'trigger-payment-id',
                'style' => 'rantevu-top-up-button',
                'onclick' => $onclick,
            )),
        ), array(
            'margin' => '0 35 0 35'
        ));

    }

}