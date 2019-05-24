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

class Feelingslider extends ActivationEngineAction {

    public function render(){
        $this->init();
		Yii::app()->clientScript->registerScriptFile('//code.jquery.com/ui/1.11.2/jquery-ui.js');
		Yii::app()->clientScript->registerCssFile('//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css');
		
        $pto = AeplayAction::model()->with('aeplay','aetask')->findByAttributes(array('shorturl'=>$_REQUEST['token']));
        $this->output = '';
		
		$min=1;
		if (isset($this->configdata->min_value_slider)) {$min=$this->configdata->min_value_slider;}
		
		$max=6;
		if (isset($this->configdata->max_value_slider)) {$max=$this->configdata->max_value_slider;}
		

	    $this->output .= '<br><br>';
		if(isset($this->configdata->msg)){

            $this->output .= $this->configdata->msg .'<br>';
        }
		
		 if (($pto->aetask->editable!=1) && ($pto->status >1) && (($pto->aetask->remains_visible==1))) {
	
		$answer = AeplayVariable::model()->findByAttributes(array('variable_id' => $this->configdata->variable, 'play_id' => $pto->play_id));
		$this->output .= '{%your_value_is%}: '.$answer->value;   
		   
	    } else {
       
	    $this->output .= '<script>$(function() {
			var doneurl=$("#donebtn").attr("href");
$( "#feeling" ).slider({
range: "max",
min: '.$min.',
max: '.$max.',
value: '.(($min+$max)/2).',
slide: function( event, ui ) {
$( "#meter" ).html( ui.value );
$("#donebtn").attr("href", doneurl+"&c="+ui.value);
}
});
$( "#meter" ).html( $( "#feeling" ).slider( "value" ) );
$("#donebtn").attr("href", doneurl+"&c="+$( "#feeling" ).slider( "value" ));

});
</script>';
	     
	    $this->output .= '<div id="meter"></div>
		                  <div id="feeling"></div>';
      
	   }
	  
	   $this->output .= '<br><br><div class="btnHolders">';
	   $this->output .= $this->donebtn;
	   $this->output .= '<br><br>';
	   
	   if (isset($this->configdata->skip_action_posibility) && ($this->configdata->skip_action_posibility==1)) {
		$this->output .='&nbsp;&nbsp;&nbsp;&nbsp;';
		$this->output .= $this->skipBtn();
		}
		
		$this->output .= '</div><br><br>';
		return $this->output;
    }
	
	
	
	
	
	
	

}

?>

