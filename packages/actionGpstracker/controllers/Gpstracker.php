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

class Gpstracker extends ActivationEngineAction {

    public function disableScripts(){
        return array('disableBootstrap' => false, 'disableDefaultCss' => false, 'disableJquery' => false);
    }
    public function render(){

        $this->init();
        

        $this->output = '';


		if (isset($this->configdata->subject) ) {
			$this->output .= '<h2>' .$this->configdata->subject .'</h2><br>';
		}
        
		if (isset($this->configdata->msg)) {
        $this->output .= $this->configdata->msg .'<br>';
		}
		
	

        if ($this->configdata->km_target && is_numeric($this->configdata->km_target)) {
        	$this->output .= '<script>km_target = '.$this->configdata->km_target;
        	$this->output .= '</script>';
        } else {
        	$this->output .= '<script>lat_target = '.$this->configdata->lat_target.";";
        	$this->output .= 'lon_target = '.$this->configdata->lon_target.";";
        	$this->output .= 'rmax = '.$this->configdata->dis_limit.";";
        	$this->output .= '</script>';
        }
        
	$this->output .= "<script>doneurl = \"" . $this->doneurl . "\"</script>";
	$this->output .= "<script>taskid = \"" . $this->taskid . "\"</script>";
	$this->output .= "<script>token = \"" . $this->token . "\"</script>";
	
	$p = <<<HTML
<div id='map' style='width:100%; height:350px'></div>
<div id='dp'></div>
<script type='text/javascript' src='http://maps.google.com/maps/api/js?sensor=false'></script>
<!--
<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js'></script>
-->
<br>{%done_btn_active_when_moved%}<br><br>



<input id="donebtn" type='button' class="btn btn-success" onClick='done()' value='Done' disabled/>
<script>initMap();</script>
HTML;
        
        $this->output .= $p;

        if(isset($this->configdata->testmode) AND $this->configdata->testmode == 1){
            $this->output .= '{%test_mode_on%}:' .$this->donebtn;
        }
        
        
     
        
        if (isset($this->configdata->skip_action_posibility) && ($this->configdata->skip_action_posibility==1)) {
		$this->output .='&nbsp;&nbsp;&nbsp;&nbsp;';
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

