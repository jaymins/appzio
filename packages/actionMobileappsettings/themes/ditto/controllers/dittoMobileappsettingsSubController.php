<?php

Yii::import('application.modules.aelogic.packages.actionMobiledates.models.*');

class dittoMobileappsettingsSubController extends MobileappsettingsController {

    public $wallet;
    public $wallet_log;

    public $receipt_sent = false;
    public $image_var = 'upload_receipt_image';

    public function tab1(){

        $this->data = new StdClass();

        $this->wallet = new WalletModel();
        $this->wallet->playid = $this->playid;

        $this->wallet_log = new WalletLogsModel();

        switch ( $this->menuid ) {
            case 'do-logout':
                $this->doLogout();
                break;
            
            case 'trigger-cache-out':
                $this->doCacheOutConfirmation();
                return true;
                break;

            case 'finish-cache-out':
                $error_code = $this->validateCheckout();
                
                if ( empty($error_code) ) {
                    $this->runCacheOutLogic();
                } else {
                    $this->doCacheOutConfirmation( $error_code );
                    return true;
                }

                break;

            case 'send-report':
                $this->runSendReportLogic();
                break;
        }

        if ( isset($_REQUEST['purchase_completed']) ) {
            
            $db_vars = array(
                'purchase_dc_250_1' => '250',
                'purchase_250DC' => '250',
                'purchase_dc_550_1' => '550',
                'purchase_550DC' => '550',
                'purchase_dc_1200_1' => '1200',
                'purchase_1200DC' => '1200',
                // 'purchase_dc_1700_1' => '1700',
                // 'purchase_1700DC' => '1700',
            );

            foreach ($db_vars as $var => $value_tbu) {
                $var_data = $this->getVariable( $var );
                if ( $var_data AND $var_data > 0 ) {
                    $this->addWalletPoints( $value_tbu );
                    $this->saveVariable( $var, 0 );
                }
            }
        }

        $col1 = $this->getImage('icon-bank.png', array(
            'width' => '40',
            'margin' => '0 10 0 0',
        ));
        $col2 = $this->getText('Wallet', array( 'style' => 'wallet-heading' ));
        $this->data->scroll[] = $this->getRow(array(
            $col1, $col2
        ), array( 'margin' => '20 20 0 20', 'padding' => '10 20 5 20', 'vertical-align' => 'middle', 'background-color' => '#ffffff', 'border-radius' => 5 ));

        $this->data->scroll[] = $this->getText($this->getTotal() . ' DC', array(
            'color' => '#195359',
            'font-size' => '35',
            'text-align' => 'center',
            'font-ios' => 'Lato-Light',
            'font-android' => 'Lato-Light',
            'padding' => '20 10 20 10',
            'margin' => '-5 20 0 20',
            'background-color' => '#ffffff',
        ));

        $checkout_btn = array();

        if ( $this->userCanCheckout() ) {
            $checkout_btn[] = $this->getTextbutton('Cash out', array(
                'id' => 'trigger-cache-out',
                'style' => 'cash-button'
            ));
        }

        $this->data->scroll[] = $this->getRow(
            $checkout_btn
        , array( 'padding' => '10 20 10 20', 'margin' => '-5 20 30 20', 'text-align' => 'center', 'vertical-align' => 'middle', 'background-color' => '#ffffff', 'border-radius' => 5 ));

        if ( isset($this->varcontent['system_source']) AND $this->varcontent['system_source'] == 'client_android' ) {
            $this->getTopUpView();
        }

        $this->getReceiptUploadView();

        $this->getLogoutButton();

        return $this->data;
    }

    public function getTopUpView() {

        $col1 = $this->getImage('icon-bank.png', array(
            'width' => '40',
            'margin' => '0 10 0 0',
        ));
        $col2 = $this->getText('Top-up', array( 'style' => 'wallet-heading' ));
        $this->data->scroll[] = $this->getRow(array(
            $col1, $col2
        ), array( 'margin' => '0 20 0 20', 'padding' => '10 20 5 20', 'vertical-align' => 'middle', 'background-color' => '#ffffff', 'border-radius' => 5 ));

        $buttons = array();

        $amounts = array(
            '250' => 'dc_250_1|250DC',
            '550' => 'dc_550_1|550DC',
            '1200' => 'dc_1200_1|1200DC',
            // '1700' => 'dc_1700_1|1700DC',
        );

        foreach ($amounts as $amount => $ids) {
            $pieces = explode('|', $ids);
            $id_android = $pieces[0];
            $id_ios = $pieces[1];

            $onclick = new StdClass();
            $onclick->action = 'inapp-purchase';
            $onclick->id = 'product_' . $amount;
            $onclick->product_id_ios = $id_ios; // Should be replaced with an id
            $onclick->product_id_android = $id_android;
            $onclick->producttype_android = 'inapp';
            $onclick->producttype_ios = 'inapp';

            $buttons[] = $this->getTextbutton($amount . ' DC', array(
                'id' => 'trigger-payment-' . $amount,
                'style' => 'top-up-button',
                'onclick' => $onclick,
            ));
        }

        $this->data->scroll[] = $this->getRow(
            $buttons,
            array( 'padding' => '10 15 10 15', 'margin' => '-4 20 0 20', 'text-align' => 'center', 'vertical-align' => 'middle', 'background-color' => '#ffffff' ));

        $this->data->scroll[] = $this->getText( '', array( 'padding' => '10 15 10 15', 'margin' => '-5 20 30 20', 'height' => 10, 'background-color' => '#ffffff', 'border-radius' => 5 ) );
    }

    public function getReceiptUploadView() {

        $onclick = new StdClass();
        $onclick->id = 'send-report';
        $onclick->action = 'submit-form-content';

        $btn_col1 = $this->getImageUplaod();

        if ( $this->getVariableId($this->image_var) AND !$this->receipt_sent ) {
            $btn_col2 = $this->getText('Upload receipt', array( 'style' => 'wallet-heading', 'onclick' => $onclick ));
        } else {
            $btn_col2 = $this->getText('Upload receipt', array( 'style' => 'wallet-heading' ));
        }

        $margin = '0 20 30 20';

        if ( $this->receipt_sent ) {
            $margin = '0 20 0 20';
        }

        $this->data->scroll[] = $this->getRow(array(
            $btn_col1, $btn_col2
        ), array( 'padding' => '10 20 10 20', 'margin' => $margin, 'vertical-align' => 'middle', 'background-color' => '#ffffff', 'border-radius' => 5 ));

        if ( $this->receipt_sent ) {
            $this->data->scroll[] = $this->getText('Thanks! Your receipt was successfully sent!', array(
                'color' => '#195359',
                'font-size' => '15',
                'text-align' => 'center',
                'font-ios' => 'Lato-Light',
                'font-android' => 'Lato-Light',
                'padding' => '5 10 10 10',
                'margin' => '-7 20 30 20',
                'border-radius' => 5,
                'background-color' => '#ffffff',
            ));

            sleep(2);

            $complete = new StdClass();
            $complete->action = 'submit-form-content';
            $this->data->onload[] = $complete;
        }
    }

    public function getLogoutButton() {
        $onclick = new StdClass();
        $onclick->action = 'submit-form-content';
        $onclick->id = 'do-logout';

        $btn_col1 = $this->getImage('icon-logout.png', array(
            'width' => '40',
            'margin' => '0 10 0 0',
        ));
        $btn_col2 = $this->getText('Logout', array( 'style' => 'wallet-heading' ));
        $this->data->scroll[] = $this->getRow(array(
            $btn_col1, $btn_col2
        ), array( 'padding' => '10 20 10 20', 'margin' => '0 20 0 20', 'vertical-align' => 'middle', 'background-color' => '#ffffff', 'border-radius' => 5, 'onclick' => $onclick ));
    }

    public function doCacheOutConfirmation( $error_code = false ) {

        $vars = $this->getPlayVariables();
        $money_earned = $this->getTotal();

        $this->data->scroll[] = $this->getRow(array(
                $this->getText('Your current balance is ' . $money_earned . ' DC. Please enter the amount you would like to cache out.', array(
                    'style' => 'wallet-heading-text',
                ))
            ), array( 'padding' => '10 20 10 20', 'margin' => '20 20 0 20', 'vertical-align' => 'middle', 'background-color' => '#ffffff', 'border-radius' => 5 ));

        $this->data->scroll[] = $this->getRow(array(
                $this->getFieldtext(
                    $this->getVariable( 'tmp_amount_tbc' ),
                    array(
                        'style' => 'field-enter-amount',
                        'variable' => $this->getVariableId( 'tmp_amount_tbc' ),
                        'hint' => 'You must keep at least 100 DC',
                        'input_type' => 'number',
                    )
                ),
            ), array( 'padding' => '10 20 10 20', 'margin' => '20 20 -5 20', 'vertical-align' => 'middle', 'background-color' => '#ffffff', 'border-radius' => 5 ));

        if ( $error_code ) {
            switch ($error_code) {
                case '1':
                    $this->data->scroll[] = $this->getText( 'Please enter a value', array( 'style' => 'checkout-validation-text' ) );
                    break;
                
                case '2':
                    $this->data->scroll[] = $this->getText( 'You don\'t have enough ditto credits', array( 'style' => 'checkout-validation-text' ) );
                    break;

                case '3':
                    $this->data->scroll[] = $this->getText( 'You should check out at least 250 DC', array( 'style' => 'checkout-validation-text' ) );
                    break;

                case '4':
                    $this->data->scroll[] = $this->getText( 'You should check out a maximum of 12000 DC', array( 'style' => 'checkout-validation-text' ) );
                    break;

                case '5':
                    $this->data->scroll[] = $this->getText( 'You should keep at least 100 DC', array( 'style' => 'checkout-validation-text' ) );
                    break;
            }
        }

        // Temporary disabled
        $this->data->scroll[] = $this->getRow(array(
            $this->getTextbutton('Cash out', array(
                'id' => 'finish-cache-out',
                'style' => 'cash-button',
            )),
        ), array( 'padding' => '10 20 10 20', 'margin' => '-5 20 30 20', 'text-align' => 'center', 'vertical-align' => 'middle', 'background-color' => '#ffffff', 'border-radius' => 5 ));

    }

    public function addWalletPoints( $amount ) {
        $wallet = $this->wallet->getWallet();
        $wallet_amount = $wallet->funds_raw;

        $current_amount = ( $wallet_amount ? $wallet_amount : '0' );
        $current_amount += $amount;

        $this->wallet->updateWallet( $current_amount );
    }

    public function runCacheOutLogic() {

        $vars = $this->getPlayVariables();

        $amount_tbc = $vars['tmp_amount_tbc'];

        $this->sendAdminNotification( $vars );
        $this->sendUserNotification( $vars );

        // Add a new Log entry
        $this->wallet_log->play_id = $this->playid;
        $this->wallet_log->funds = $amount_tbc;
        $this->wallet_log->payed = 0;
        $this->wallet_log->insert();

        $this->wallet->updateWallet( $this->getTotal() - $amount_tbc );

        $this->saveVariable( 'tmp_amount_tbc', 0 );
    }

    public function getTotal() {
        $wallet = $this->wallet->getWallet();
        $amount = $wallet->funds_raw;
        return $amount;
    }

    /*
    * Checks whether user could perform a Checkout.
    */
    public function userCanCheckout() {

        $amount = $this->getTotal();

        if ( $amount >= 5100 ) {
            return true;
        }

        return false;
    }

    public function getImageUplaod(){

        // $this->deleteVariable( $this->image_var );

        $params['width'] = '40';
        $params['imgwidth'] = '600';
        $params['imgheight'] = '600';
        $params['imgcrop'] = 'yes';
        $params['crop'] = 'yes';
        $params['priority'] = '9';
        
        $params['defaultimage'] = 'icon-upload.png';
        $params['width'] = '40';
        $params['margin'] = '0 10 0 0';

        $params['onclick'] = new StdClass();
        $params['onclick']->action = 'upload-image';
        $params['onclick']->max_dimensions = '600';
        $params['onclick']->variable = $this->getVariableId($this->image_var);
        $params['onclick']->action_config = $this->getVariableId($this->image_var);
        $params['onclick']->sync_upload = true;

        $params['variable'] = $this->getVariableId($this->image_var);
        $params['config'] = $this->getVariableId($this->image_var);
        $params['debug'] = 1;
        $params['fallback_image'] = 'selecting-image.png';

        return $this->getImage($this->getVariable($this->image_var),$params);
    }

    public function runSendReportLogic() {
        
        if ( !$this->getVariable( $this->image_var ) ) {
            return false;
        }

        $mailer = new YiiMailMessage;

        $email = 'hello@myditto.co';
        $subject = 'New receipt uploaded: '. $this->getVariable( 'name' ) .' uploaded a new receipt';

        $body = 'User name: ' . $this->getVariable( 'name' );
        $body .= '<br />';
        $body .= 'User Email: ' . $this->getVariable( 'email' );
        $body .= '<br />';
        $body .= 'Time and date submitted: ' . date( 'F j, Y G:i:s' );
        
        $image_path = $this->getVariable( $this->image_var );
        $base_path = str_replace('protected', '', yii::app()->basePath);
        $file_path = $base_path . $image_path;

        if ( !file_exists($file_path) ) {
            $this->runSendReportLogic();
        }

        $mailer->setBody($body, 'text/html');
        // $mailer->addTo( 'dogostz@gmail.com' );
        $mailer->addTo( $email );
        $mailer->AddBCC( 'spmitev@gmail.com' );
        $mailer->from = array('info@myditto.co' => 'Ditto Malaysia');
        $mailer->subject = $subject;

        $swiftAttachment = Swift_Attachment::fromPath($file_path);
        $mailer->attach($swiftAttachment);

        Yii::app()->mail->send($mailer);

        // Delete the older image
        $this->deleteVariable( $this->image_var );

        $this->receipt_sent = true;
    }

    public function validateCheckout() {
        $var_id = $this->getVariableId( 'tmp_amount_tbc' );
        $value = $this->submitvariables[$var_id];
        $money_earned = $this->getTotal();

        // Empty value
        if ( empty($value) ) {
            return 1;
        }

        // Insufficient funds
        if ( $value > $money_earned ) {
            return 2;
        }

        if ( $value < 250 ) {
            return 3;
        }

        if ( $value > 12000 ) {
            return 4;
        }

        // At least 100 DC must be kept
        if ( ($money_earned - $value) < 100 ) {
            return 5;
        }

        $this->saveVariable( 'tmp_amount_tbc', $value );
    }
    
}