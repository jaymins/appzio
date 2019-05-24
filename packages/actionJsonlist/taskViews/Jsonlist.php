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

class Jsonlist extends ActivationEngineAction {


    public function render(){

        $this->init();

        $this->output = '';
        /* main content */
		if (isset($this->configdata->msg)) {
        $this->output .= $this->configdata->msg .'<br>';
		}
        $this->output .= '<br><br>';
        $this->output .= $this->donebtn;

        $this->output .= 'hello';
        return $this->output;
    }

}

?>

