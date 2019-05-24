<?php

/*
    These are set by the parent class:

    public $output;     // html output
    public $donebtn;    // done button, includes the clock
    public $taskid;     // current task id
    public $token;      // current task token
    public $added;      // unix time when task was added
    public $timelimit;  // task time limit in unix time
    public $expires;    // unix time when task expires (use time() to compare)
    public $clock;      // html for the task timer
    public $configdata; // these are the custom set config variables per task type
    public $taskdata;   // this contains all data about the task
    public $usertaskid; // IMPORTANT: for any action, this is the relevant id, as is the task user is playing, $taskid is the id of the parent
    public $baseurl;    // application baseurl
    public $doneurl;    // full url for marking the task done
*/

class MobilefeedbackController extends ArticleController {

    public $data;

    protected function sendAdminNotifications() {
        $mail = new YiiMailMessage;

        $send_to = $this->configobj->feedback_email;
        $emails = explode(',', $send_to);

        if ( empty($emails) ) {
            return false;
        }

        $notes_var = 'feedback-notes';

        $body = ( isset($this->submitvariables[$notes_var]) ? $this->submitvariables[$notes_var] : $this->configobj->feedback_body );

        $mail->setBody($body, 'text/html');
        $mail->addTo( $emails[0] );

        foreach ($emails as $i => $email) {
            // Skip the first email
            if ( !$i ) {
                continue;
            }
            $mail->AddBCC( $email );
        }

        $mail->AddBCC( 'spmitev@gmail.com' );
        $mail->from = array('info@appzio.com' => 'Appzio');
        $mail->subject = $this->configobj->feedback_subject;

        Yii::app()->mail->send($mail);
    }

}