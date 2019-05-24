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

class Essay extends ActivationEngineAction {

    
    public $validationError = '';

	 public function disableScripts(){
        return array('disableBootstrap' => true, 'disableDefaultCss' => true, 'disableJquery' => true);
    }

    public function render(){

         $this->init();
        // $pto = AeplayAction::model()->findByPk($this->usertaskid);
		 $pto = AeplayAction::model()->with('aeplay','aetask')->findByAttributes(array('shorturl'=>$_REQUEST['token']));
        
		$this->setColors();

	    $this->output = '';
		 

		 if ($pto->essay_status==1) {
		     $this->output .= '<strong>{%pending_review%}</strong><br><br>';	
		 }
       
		//print_r($this); die;
        //$this->output .='<strong>{%essay%}:</strong><br>';
		if (isset($this->configdata->essay)) {
          $this->output .= $this->configdata->essay .'<br>';
		}
	
	 if ((($pto->aetask->remains_visible==1) && ($pto->status >1))  || (($pto->aetask->editable ==1) && ($pto->status >1))) {
			
		 $this->output .= $this->saveForm();
         $this->output .= '<br><br>';	
			
		} else {
		
		
		if ($pto->essay_status==1) {
		//answer of player	
		  $answer = AeplayVariable::model()->findByAttributes(array('variable_id' => $this->configdata->variable1, 'play_id' => $pto->play_id));
		 
		  $this->output .= '<br><br>';
		  $this->output .= '<strong>{%your_answer%}:</strong>';
		  $this->output .= $answer->value;
		} elseif ($pto->essay_status==2) {
		//answer + feedback
		   $answer = AeplayVariable::model()->findByAttributes(array('variable_id' => $this->configdata->variable1, 'play_id' => $pto->play_id));
		   $feedback = AeplayVariable::model()->findByAttributes(array('variable_id' => $this->configdata->variable2, 'play_id' => $pto->play_id));	
		   $this->output .= '<strong>{%your_answer%}:</strong>';
		   $this->output .=$answer->value;
		   $this->output .= '<br><br>';
		   $this->output .= '<strong>{%coach_feedback%}:</strong>';
		   $this->output .= '<br><br>';
		   $this->output .= $feedback->value;
		}	else  {
        $this->output .= $this->saveForm();
        $this->output .= '<br><br>';
		 }
	  }
	   
	   
	  
	  
	   	
		if (isset($this->configdata->skip_action_posibility) && ($this->configdata->skip_action_posibility==1)) {
		$this->output .= $this->skipBtn();
		}
	  
	  
        return $this->output;
    }

    
	
	 public function skipBtn() {
		
		$output = '<a href="'.$this->skipurl.'" class="btn btn-success">{%skip_action%}</a>';
		return $output;
		}
		

   
}

?>