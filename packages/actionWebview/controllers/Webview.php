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

class Webview extends ActivationEngineAction {


    public function disableScripts(){
        return array('disableBootstrap' => true, 'disableDefaultCss' => true, 'disableJquery' => true);
    }

    public function render_component(){
        return $this->render();
    }

    public function render(){
        $this->init();

        if (isset($this->configdata->url_to_show) && ($this->configdata->url_to_show)) {
            header("Location: ".$this->configdata->url_to_show);
            die();
        } else {
            $this->output = '';
            /* main content */

            if(isset($this->configdata->msg)){
                $this->output .= $this->configdata->msg .'<br>';
            }

            $this->output .= '<br><br>';

            if (isset($this->configdata->user_can_complete_action) && ($this->configdata->user_can_complete_action==1)) {
                $this->output .= $this->donebtn;
            }

            if (isset($this->configdata->skip_action_posibility) && ($this->configdata->skip_action_posibility==1)) {
                $this->output .='&nbsp;&nbsp;&nbsp;&nbsp;';
                $this->output .= $this->skipBtn();
            }

            return $this->output;
        }
    }




}

?>

