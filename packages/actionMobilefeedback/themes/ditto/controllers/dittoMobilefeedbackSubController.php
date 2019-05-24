<?php

class dittoMobilefeedbackSubController extends MobilefeedbackController {

    public function tab1(){

        $this->data = new StdClass();

        $classname = 'feedback-submit-review';
        $notes_var = 'feedback-notes';
        $show_error = false; 

        if ( isset($this->menuid) AND $this->menuid == '5555' ) {

            if ( empty($this->submitvariables[$notes_var]) ) {
                $classname = 'feedback-submit-review-error';
                $show_error = true;
            } else {
                $this->sendAdminNotifications();
                $this->saveVariables();
            }

        }

        if(!$this->getConfigParam('hide_image')){
            $this->data->scroll[] = $this->getImage( 'banner-feedback-ditto.png' );
        }

        $this->data->scroll[] = $this->getText('{#please_send_us_feedback_or_question_using_the_form_below#}', array('style' => 'feedback-title'));
        $this->data->scroll[] = $this->getFieldtextarea('', array( 'style' => $classname, 'hint' => '{#type_your_message_here#}...', 'variable' => $notes_var ));

        if ( $show_error ) {
            $this->data->scroll[] = $this->getText( '{#please_enter_text_for_your_feedback#}', array('style' => 'feedback-error') );
        }

        if ( isset($this->menuid) AND $this->menuid == '5555' ) {
            $text = ( $show_error ? '{#send_feedback#}' : '{#thank_you#}!' );
            $this->data->footer[] = $this->getTextbutton($text,array('id' => 5555, 'style' => 'feedback-button'));
        } else {
            $this->data->footer[] = $this->getTextbutton('{#send_feedback#}',array('id' => 5555, 'style' => 'feedback-button'));
        }
        
        return $this->data;
    }

    protected function sendAdminNotifications() {

        $mail = new YiiMailMessage;

        $send_to = $this->configobj->feedback_email;
        $emails = explode(',', $send_to);

        if ( empty($emails) ) {
            return false;
        }

        $notes_var = 'feedback-notes';

        $name = $this->getSavedVariable('name') ? $this->getSavedVariable('name') : $this->getSavedVariable('screen_name');

        $body = 'User name: ' . $name;
        $body .= '<br />';
        $body .= 'Email: ' . $this->getSavedVariable('email');
        $body .= '<br />';
        $body .= 'OS: ' . $this->getSavedVariable('system_push_plattform');
        $body .= '<br />';
        $body .= 'Play ID: ' . $this->playid;
        $body .= '<br />';
        $body .= 'Message: ';
        $body .= '<br /><br />';

        $body .= ( isset($this->submitvariables[$notes_var]) ? $this->submitvariables[$notes_var] : $this->configobj->feedback_body );

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