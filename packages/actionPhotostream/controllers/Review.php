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

    You can use these html helpers to get activation engine styled email components:
            HtmlHelpers::button($title,$url);
            $body = HtmlHelpers::htmlMail($body);

*/

class Review extends ActivationEngineAction {

    public $validationError = '';

    public function render(){

        $this->init();

       /* if(isset($_POST['AeplayVariable'])){
            $this->inputValidation();
        } */

        $this->output = '';
        /* main content */

      /*  if($this->validationError){
            $this->output .= '<div class="validationError">' .$this->validationError .'</div>';
        }*/

        $this->output .= $this->configdata->essay .'<br>';
        $this->output .= $this->saveForm();
        $this->output .= '<br><br>';

     //   $this->output .= '{%this_is_what_your_friends_see%}:<div class="friendmsg">';
     //   $this->output .= '<b>{%subject%}:</b><br>' .$this->configdata->subject_tofriend .'<br>';
    //    $this->output .= '<b>{%message%}:</b><br>' .$this->configdata->msg_tofriend .'<br>';
    //    $this->output .= '</div>';

        // $this->output .= $this->donebtn;
        return $this->output;
    }



   
}

?>