<?php

class dealsappMobilefeedbackSubController extends MobilefeedbackController {

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

        $this->data->scroll[] = $this->getImage( 'banner-feedback.png' );
        $this->data->scroll[] = $this->getText('Please send us feedback what would make your experience even better', array('style' => 'feedback-title'));
        $this->data->scroll[] = $this->getFieldtextarea('', array( 'style' => $classname, 'hint' => 'Type your message here...', 'variable' => $notes_var ));

        if ( $show_error ) {
            $this->data->scroll[] = $this->getText( 'Please enter the text for your feedback', array('style' => 'feedback-error') );
        }

        if ( isset($this->menuid) AND $this->menuid == '5555' ) {
            $text = ( $show_error ? 'Send Feedback' : 'Thank you!' );
            $this->data->footer[] = $this->getTextbutton($text,array('id' => 5555));
        } else {
            $this->data->footer[] = $this->getTextbutton('Send Feedback',array('id' => 5555));
        }
        
        return $this->data;
    }

}