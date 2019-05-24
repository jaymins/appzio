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

class Achievement extends ActivationEngineAction {

	

	 public function disableScripts(){
        return array('disableBootstrap' => true, 'disableDefaultCss' => false, 'disableJquery' => true);
    }

    public function render(){
		 
        $this->init();

		if (isset($this->configdata->skip_diploma) && ($this->configdata->skip_diploma==1)) {
			Yii::app()->request->redirect($this->doneurl);
		}
		
         $this->setColors();

		$url=Yii::app()->Controller->getDomain().Yii::app()->i18n->getCurrentLang().'/aeplay/home/showtask?ptid='.$this->usertaskid.'&token='.$this->token;


	  //  print_r($this); die;
	   
	    $this->output = '';
		if (isset( $this->configdata->text) && ($this->configdata->text!='') ) {
           $this->output .= '<p style="color:#'.$this->color_text.';">'.$this->configdata->text .'</p>';
		 }
        $this->output .= '<div class="sidebox diploma">
		   <div class="top_frame"><div class="lt_frame"></div><div class="mt_frame"></div><div class="rt_frame"></div></div>
		   <div class="m_frame">
		   <div class="inm_frame">';
		
		   if (isset( $this->configdata->title) ) {
			   $this->output .='<div class="bigtitle" style="color:#'.$this->color_text.';">'.$this->configdata->title.'</div>';
		   }
		   
           /* main content */
		   $this->output .= '<br />';
		  
		   $this->output .= '<div class="diplomatxt" style="color:#'.$this->color_text.';">{%achivement%}</div>';
		   $this->output .= '<br /><br />';
		   $this->output .= '<div class="diplomanamerow"><span class="labelcell">{%name%}:</span><span class="namecell">'.$this->playername.'</span></div>';
		   $this->output .= '<br /><br />';
		   $this->output .= '<div class="diplomapointsrow"><span class="labelcell">{%achievement%}:</span><span class="pointecell">'.$this->allpoints.'<span class="toppoints">{%points%}</span></span></div>';
		   $this->output .= '<br /><br />';
		   if ( isset($this->configdata->badgecolor) && isset($this->configdata->badge) ) {
		  
		   $this->output .= '<div class="diplomanamepoints"><img src="'.Yii::app()->Controller->getDomain() .'/images/president_signature.gif" width="120" class="signature"/><img src="'.Yii::app()->Controller->getDomain() .'/images/badges/'.$this->configdata->badgecolor .'/'.$this->configdata->badge.'" width="120" style="margin:0px 22px;"  class="diploamabadge"/><img src="'.Yii::app()->Controller->getDomain() .'/images/signature_vice.gif" width="120" class="signature"/></div>';
		   }
        $this->output .= '<br><br>';
		$this->output .= $this->donebtn;
		$this->output .= '<br><br>';
		$this->output .= '</div></div><div class="bot_frame"><div class="lb_frame"></div><div class="mb_frame"></div><div class="rb_frame"></div></div>';
		$this->output .='</div>';
        return $this->output;
    }

}

?>

