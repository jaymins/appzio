<?php

/*

    this is a dynamic article action, which is launched either by
    Apiaction.php (component)
    Updatevariables (api method)
    Refreshaction (api method)

    If its called by either api method, the object is passed on to Apiaction.php eventually.

    Either RenderData or DynamicUpdate are called

    It should return json which gets put into the layoutconfig

    Data saving on picture submit is little complicated, because we upload
    async from the client. So once user has submitted a photo, we launch
    an async process to deal with that and to eventually add it to the action.
    Process is not perfect, as we rely on temporary variable values that might
    get overwritten if user uploads two photos very quickly after one another.

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');

class MobileappsettingsController extends ArticleController {

    public $data;
    public $configobj;
    public $theme;

    public function doLogout(){

        $this->data->scroll[] = $this->getSpacer(120);
        $this->data->scroll[] = $this->getText('{#logging_out#}',array( 'style' => 'register-text-step-2'));

        $this->saveVariable('logged_in','0');
        $this->saveVariable('fb_universal_login','0');
        //$this->deleteVariable('reg_phase');

        AeplayBranch::activateBranch($this->getConfigParam('login_branch'),$this->playid);
        AeplayBranch::activateBranch($this->getConfigParam('register_branch'),$this->playid);

        $complete = new StdClass();

        $complete->action = 'list-branches';
        $this->data->onload[] = $complete;

        $complete = new StdClass();
        $complete->action = 'fb-logout';
        $this->data->onload[] = $complete;

        $complete = new StdClass();
        $complete->action = 'complete-action';
        $this->data->onload[] = $complete;
    }

    public function sendAdminNotification( $vars ) {
        $mail = new YiiMailMessage;

        $send_to = $this->getConfigParam( 'email_receiver' );
        $amount_tbc = $vars['tmp_amount_tbc'];

        $body = 'User name: ' . $vars['name'];
        $body .= '<br />';
        $body .= 'Time and date submitted: ' . date( 'F j, Y G:i:s' );
        $body .= '<br />';
        $body .= 'Amount to cash out: ' . $amount_tbc;

        $mail->setBody($body, 'text/html');
        $mail->addTo( $send_to );
        $mail->AddBCC( 'spmitev@gmail.com' );
        $mail->from = array('info@appzio.com' => 'Appzio');
        $mail->subject = 'Cash out notification: '. $vars['name'] .' requested to cash out '. $amount_tbc .' DC';

        // Yii::app()->mail->send($mail);
    }

    public function sendUserNotification( $vars ) {

        if ( !isset($vars['email']) OR empty($vars['email']) ) {
            return false;
        }

        $mail = new YiiMailMessage;

        $amount_tbc = $vars['tmp_amount_tbc'];
        $send_to = $vars['email'];

        $body = 'Dear ' . $vars['name'] . ',';
        $body .= '<br />';
        $body .= '<br />';
        $body .= 'You have cashed out a total of '. $amount_tbc .' ditto credits (DC), equivalent to [currency symbol][credits in currency value]. Please reply within 5 days the following so that we can facilitate a transfer of funds to your account:';
        $body .= '<br />';
        $body .= '
- Bank name:
- Bank account number:
- Bank address:
- Name of account holder:
- SWIFT code:
';
        $body .= '<br />';
        $body .= 'If you do not respond in 5 days, your wallet will be re-credited with '. $amount_tbc .'DC. Feel free to get back to us with any questions. Thank you';
        $body .= '<br />';
        $body .= '<br />';
        $body .= 'Nicola';

        $mail->setBody($body, 'text/html');
        $mail->addTo( $send_to );
        $mail->AddBCC( 'spmitev@gmail.com' );
        $mail->from = array('info@appzio.com' => 'Appzio');
        $mail->subject = 'You are cashing out '. $amount_tbc .' in Ditto credits!';

        // Yii::app()->mail->send($mail);
    }

}