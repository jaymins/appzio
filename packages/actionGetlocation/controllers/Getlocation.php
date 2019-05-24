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

class Getlocation extends ActivationEngineAction {

    public function render(){

        $this->init();
        $pto = AeplayAction::model()->with('aeplay','aetask')->findByAttributes(array('shorturl'=>$_REQUEST['token']));    
		

		 ///colors
		 $this->setColors();

		
        $this->output = '';
		$msg='';
		if (isset ($this->configdata->msg)) {$msg=$this->configdata->msg;}
        /* main content */
		


       
		if (($pto->status >1) && ($pto->aetask->remains_visible==1) && (($this->configdata->fieldtype1=='file') || ($this->configdata->fieldtype2=='file')) && ($pto->aetask->editable!=1)) {
		 $this->output .= $msg .'<br>';
		
		} else {
		$this->output .= $msg .'<br>';
		
        $this->output .= $this->saveForm();
		}
		
		
	
		
		
		if (isset($this->configdata->skip_action_posibility) && ($this->configdata->skip_action_posibility==1)) {
		$this->output .='&nbsp;&nbsp;&nbsp;&nbsp;';
		$this->output .= $this->skipBtn();
		}
		
		
        $this->output .= '<br><br>';
       // $this->output .= $this->donebtn;
        return $this->output;
    }
	

	

}

?>

