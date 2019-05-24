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


/*    $choices = array(
        array('id' => 'all', 'name' => '{%all_answers_are_correct%}'),
        array('id' => '1','name' => '{%choice%} 1'),
        array('id' => '2','name' => '{%choice%} 2'),
        array('id' => '3','name' => '{%choice%} 2'),
        array('id' => '4','name' => '{%choice%} 2'),
        array('id' => '5','name' => '{%choice%} 2'),
    );

    $answersave = array(
        array('id' => '', 'name' => ''),
        array('id' => 'collect_id', 'name' => '{%collect_to_variable_id%}'),
        array('id' => 'collect_content', 'name' => '{%collect_to_variable_content%}')
    );

return array(
    'config[subject]' => array('type'=>'text', 'title'=>'%subject%', 'onChange' => 'this.form.submit()'),
    'config[msg]' => array('type'=>'redactor','class' => 'span2', 'rows'=>10, 'hint' => '{%msg_hint%}'),
    'config[shortmsg]' => array('type'=>'textarea','rows'=>5, 'width' => '100%', 'maxlength' => 200, 'hint' => '{%shortmsg_hint%}'),
    'config[choice1]' => array('type'=>'text', 'hint' => '{%choice%} 1'),
    'config[choice2]' => array('type'=>'text', 'hint' => '{%choice%} 2'),
    'config[choice3]' => array('type'=>'text', 'hint' => '{%choice%} 3'),
    'config[choice4]' => array('type'=>'text', 'hint' => '{%choice%} 4'),
    'config[choice5]' => array('type'=>'text', 'hint' => '{%choice%} 5'),
    'config[correct_answer]' => array('type'=>'dropdownlist', 'items' => CHtml::listData($choices,'id','name'), 'hint' => '{%correct_answer%}'),
    'config[answersave]' => array('type'=>'dropdownlist', 'items' => CHtml::listData($choices,'id','name'), 'hint' => '{%multiselect_answer_saving%}'),
    'config[variable]' => array('type'=>'text', 'hint' => '{%collect_to_variable_hint%}'),*/


class Feeling extends ActivationEngineAction {

	 public function disableScripts(){
        return array('disableBootstrap' => true, 'disableDefaultCss' => true, 'disableJquery' => true);
    }


    public function render(){

        $this->init();
        $pto = AeplayAction::model()->with('aeplay','aetask')->findByAttributes(array('shorturl'=>$_REQUEST['token']));
        $this->output = '';
        /* main content */
		
		 ///colors
		$this->setColors();
		

        if(isset($this->configdata->subject)){

            $this->output .= '<h2>' .$this->configdata->subject .'</h2><br>';
        }

        if(isset($this->configdata->msg)){

            $this->output .= $this->configdata->msg .'<br>';
        }
        
	   if (($pto->aetask->editable!=1) && ($pto->status >1) && (($pto->aetask->remains_visible==1))) {
	
		$answer = AeplayVariable::model()->findByAttributes(array('variable_id' => $this->configdata->variable, 'play_id' => $pto->play_id));
		$this->output .= '{%your_value_is%}: '.$answer->value;   
		   
	    } else {
        $count = 1;

        while($count < 6){
            $this->output .= $this->smiley($count);
            $count++;
        }

      
	  }
	  
	   
	   $this->output .= '<br><br>';
	   
	   if (isset($this->configdata->skip_action_posibility) && ($this->configdata->skip_action_posibility==1)) {
		$this->output .='&nbsp;&nbsp;&nbsp;&nbsp;';
		$this->output .= $this->skipBtn();
		}
	   
	    $this->output .= '<br><br>';
        return $this->output;
    }


    private function smiley($number){
        $output = '<a href="' .$this->doneurl .'&c=' .$number .'">';
        $output .= '<img src="/images/milky/32/emo' .$number .'.png" style="margin-right:10px;">';
        $output .= '</a>';
        return $output;
    }

    private function selector($number){
        $var = 'choice' .$number;

        if(isset($this->configdata->$var) AND strlen($this->configdata->$var) > 0){
            $output = '<a href="' .$this->doneurl .'&c=' .$number .'" class="btn info" style="width:80%;margin-bottom:5px; border-right: 4px solid black;border-bottom: 4px solid black; background:#'.$this->color_btn.';color:#'.$this->color_btn_text.';" >';
            $output .= $this->configdata->$var;
            $output .= '</a><br>';
            return $output;
        }
    }
	
	
   public function skipBtn() {
		
		$output = '<a href="'.$this->skipurl.'" class="btn btn-success" style="background:#'.$this->color_btn.';color:#'.$this->color_btn_text.';">{%skip_action%}</a>';
		return $output;
		}
	
	
	

}

?>

