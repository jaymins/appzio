<?php

/* all individual task displays extend from this file
*/

Yii::import('aegame.models.*');


class ActivationEngineAction  {

    public $output;     // html output
    public $donebtn;    // done button, includes the clock
    public $taskid;     // current task id
    public $usertaskid; // IMPORTANT: for any action, this is the relevant id, as is the task user is playing, $taskid is the id of the parent
    public $token;      // current task token
    public $added;      // unix time when task was added
    public $timelimit;  // task time limit in unix time
    public $expires;    // unix time when task expires (use time() to compare)
    public $clock;      // html for the task timer
    public $configdata; // these are the custom set config variables per task type
    public $taskdata;   // this contains all data about the task
    public $baseurl;    // application baseurl
    public $doneurl;    // full url for marking the task done
    public $variable_id;
    public $playid;
    public $playurl = 'aeplay/default/';
    public $btntext;

    public $ptid;

    public $branchid;
    public $gameid;
    public $actiontype;

    /* data objects */
    public $actiondata;
    public $gamedata;
    public $branchdata;
	
	public $skipurl;

    public $branchurl;
    public $colors;

    public $isComponent;

	
    public $color_bckg = false;
    public $color_bg = false;
    public $color_text = false;
    public $color_btn = false;
    public $color_btn_text = false;
	public $color_btn_icon = false;

    function init(){

        /* make sure we have a user */
        if(!isset(Yii::app()->user->id)){
            //Yii::app()->request->redirect(Yii::app()->Controller->getDomain() .'/' .Yii::app()->i18n->getCurrentLang() .'/aeplay/public/login');
        }

        //$this->tasker->joku = 'sdkd';

        if(isset($_REQUEST['branchid'])){ $this->branchurl = '&branchid=' .$_REQUEST['branchid']; } else { $this->branchurl = false; }

        /* figure out the action id */
        if(isset($_REQUEST['token'])){
            $this->task = AeplayAction::model()->find('`shorturl` = ' ."'" .$_REQUEST['token'] ."'");
            $this->taskid=$this->task->action_id;
            $this->token = $_REQUEST['token'];
            $this->ptid = $this->task->id;
        } elseif(isset($_REQUEST['ptid'])){
            // note, user needs to be logged in, if we are requesting without token!!
            $this->task = AeplayAction::model()->with('aeplay')->find('`shorturl` = ' ."'" .$_REQUEST['ptid'] ."'");
            if($this->task->aeplay->user_id != Yii::app()->user->id){
                Yii::app()->request->redirect(Yii::app()->i18n->BaseUrl() .$this->playurl);
                Yii::app()->end();
            }

            $this->taskid=$this->task->action_id;
            $this->ptid = $this->task->id;
        } else {
            $this->taskid=$this->taskdata->aetask->taskid;
        }

		if (!isset($this->task)) {
			$task = AeplayAction::model()->find('`id` = ' ."'" .$this->taskdata->id ."'");
			$this->task = $task;
			$this->taskid=$this->task->action_id;
		}

        /* data objects */
        $this->actiondata = Aeaction::model()->findByPk($this->taskid);
        $this->branchdata = Aebranch::model()->findByPk($this->actiondata->branch_id);
        $this->gamedata= Aegame::model()->findByPk($this->branchdata->game_id);

        /* colors */
        //$this->colors = Controller::getColors(false,false,$this->taskid);

        /* ids */
        $this->playid = $this->taskdata->play_id;
        $this->branchid = $this->actiondata->branch_id;
        $this->gameid = $this->branchdata->game_id;
        $this->usertaskid=$this->taskdata->id;

        /* params */
        $this->added=strtotime($this->taskdata->added);
        $this->timelimit=$this->taskdata->aetask->timelimit;
        $this->baseurl = Yii::app()->i18n->BaseUrl();
        $this->actiontype = Aeaction::getTaskTypeFromTypeId($this->actiondata->type_id);

        if(isset($task->expires)){
            $this->expires = $task->expires;
        }

        $abranch = new Aebranch();
        $abranch->gid = $this->branchdata->game_id;
        $componentsid = $abranch->getComponentsId();

        if($componentsid == $this->actiondata->branch_id){
            $this->isComponent = true;
        } else {
            $this->isComponent = false;
        }


        if(!isset($this->doneurl)){
            $this->doneurl = Yii::app()->Controller->getDomain() .'/' .Yii::app()->i18n->getCurrentLang() .'/aeplay/home/done?token=' .$this->token .$this->branchurl;
            $this->timer();
        }
		
		 if(!isset($this->skipurl)){
            $this->skipurl = Yii::app()->Controller->getDomain() .'/' .Yii::app()->i18n->getCurrentLang() .'/aeplay/home/done?token=' .$this->token.'&skip=1' .$this->branchurl;
        }

        if($this->isComponent === true){
            $this->donebtn = '';
            $this->skipurl = '';
            $this->doneurl = '';

        } else {
            /* done button (and possibly commenting form) */
            $this->donebtn = $this->doneButton();
        }

        $path = Yii::getPathOfAlias('application.modules.aelogic.packages.action' .ucfirst($this->actiontype) .'.models.' .ucfirst($this->actiontype) .'Model');

        /* if action includes model file, we load it */
        if(file_exists($path .'.php')){
            Yii::import('application.modules.aelogic.packages.action' .ucfirst($this->actiontype) .'.models.*');
        }


    }

    public function getBtnColor($full=true){

        if($full == true){
            $style = 'style="';
        } else {
            $style = '';
        }


        if(isset($this->colors['codes']['button_color'])){
            $style .= 'background:#' .$this->colors['codes']['button_color'].';color:#' .$this->colors['codes']['button_text_color'] .';';
        } else {
            $style .= '';
        }

        if($full == true){
            $style .= '"';
        }

        return $style;
    }
    

	 public function setColors (){

         if(isset($this->colors['codes']) AND $this->colors['codes']) {

            $background = isset($this->colors['codes']['background_color']) ? $this->colors['codes']['background_color'] : '#ffffff';
            $topbar = isset($this->colors['codes']['top_bar_color']) ? $this->colors['codes']['top_bar_color'] : '#ffffff';
            $text = isset($this->colors['codes']['text_color']) ? $this->colors['codes']['text_color'] : '#000000';
            $button = isset($this->colors['codes']['button_color']) ? $this->colors['codes']['button_color'] : '#000000';
            $buttontext = isset($this->colors['codes']['button_text_color']) ? $this->colors['codes']['button_text_color'] : '#ffffff';
            $icon = isset($this->colors['codes']['button_icon_color']) ? $this->colors['codes']['button_icon_color'] : '#ffffff';

		    $this->color_bckg = $background;
            $this->color_bg = $topbar;
            $this->color_text = $text;
            $this->color_btn = $button;
            $this->color_btn_text = $buttontext;
			$this->color_btn_icon = $icon;
        }
   
	
	}



    private function doneButton(){

        if(isset($_POST['AeplayAction']['comment']) AND !isset($_POST['AeplayVariable']['variable1'])){
            $this->CallBeforeDone();
            Yii::app()->request->redirect(Yii::app()->i18n->BaseUrl() .$this->playurl .'done?tid=' .$this->taskdata->id .'&token=' .$_REQUEST['token'] .'&comment=' .$_POST['AeplayAction']['comment']);
            Yii::app()->end();
        }

        $btnTxt='{%complete_task%}';
        if ($this->actiondata->buttontxt!='') $btnTxt=$this->actiondata->buttontxt;
        $points_buttontxt=' (' .$this->actiondata->points .'{%pt%})';
        if ($this->gamedata->hide_points==1 OR $this->actiondata->points == 0) { $points_buttontxt=''; }
        //$this->btntext = $btnTxt.$points_buttontxt;
        $this->btntext = $btnTxt;

        $style = $this->getBtnColor(true);

        if(isset($_REQUEST['branchid'])){ $branch = '&branchid=' .$_REQUEST['branchid']; } else { $branch = false; }

        if(isset($this->actiondata->commenting) AND $this->actiondata->commenting == 1){
            $btn = '{%comment%}';
            $btn .= $this->getCommentForm($btnTxt);
        } elseif($this->clock){
            $btn = '<div class="donebtncontainer"><a href="' .$this->doneurl .'&tid=' .$this->taskdata->id .$this->branchurl .'" id="donebtn" class="btn btn-success"' .$style  .'>'.$btnTxt.$this->clock .'</a></div>';
        } else {
            $btn = '<div class="donebtncontainer"><a href="' .$this->doneurl .'&tid=' .$this->taskdata->id .$this->branchurl .'" id="donebtn" class="btn btn-success"' .$style .'>'.$btnTxt.'</a></div>';
        }

        return $btn;
    }
	
	
	  public static function continueButton($url,$colors){

        $btnTxt='{%continue_play%}';
        $btn = '<a href="' .$url .'" class="btn btn-success"' .$colors .'>'.$btnTxt.'</a><br>';

        return $btn;
    }
	
	

    private function getCommentForm($btnTxt){

        $model = AeplayAction::model()->findByPk($this->ptid);
        $formfields['elements']['comment'] = array('type' => 'divider');
        $formfields['elements']['comment'] = array('type' => 'textarea', 'hint' => $this->actiondata->commenting_brief);
        $formfields['elements']['action'] = array('type' => 'hidden', 'value' => 'done');
        $formfields['elements']['pid'] = array('type'=> 'hidden', 'value' => $this->taskdata->play_id);
        $formfields['elements']['token'] = array('type'=> 'hidden', 'value' => $this->token);
        $formfields['buttons'] = array('save' => array('type' => 'htmlSubmit','label' => $btnTxt));

        $params = Controller::formParameters(false,1);
        $form = TbForm::createForm($formfields,$model,$params);

        return $form;

}


    public static function gotoActionList(){

        if(isset($_REQUEST['branchid'])){ $branchurl = '?branchid=' .$_REQUEST['branchid']; } else { $branchurl = false; }
        Yii::app()->request->redirect(Yii::app()->Controller->getDomain() .'/' .Yii::app()->i18n->getCurrentLang() .'/aeplay/home/gamehometasks' .$branchurl);
        Yii::app()->end();
    }
	
	public function timer(){
		 $this->clock = '';
		}


    private function controllerImport(){
        Yii::import('application.modules.aelogic.packages.ActivationEngineAction');

        if($this->isComponent == true){
            $path = Yii::getPathOfAlias('application.modules.aelogic.packages.action' .ucfirst($this->actiontype) .'.controllers.' .ucfirst($this->actiontype) .'_component');
            if(file_exists($path)){
                Yii::import('application.modules.aelogic.packages.action' .ucfirst($this->actiontype) .'.controllers.' .ucfirst($this->actiontype)  .'_component');
                $classname = $this->actiontype .'Component';
            } else {
                Yii::import('application.modules.aelogic.packages.action' .ucfirst($this->actiontype) .'.controllers.' .ucfirst($this->actiontype));
                $classname = $this->actiontype;
            }
        } else {
            Yii::import('application.modules.aelogic.packages.action' .ucfirst($this->actiontype) .'.controllers.' .ucfirst($this->actiontype));
            $classname = $this->actiontype;
        }

        return $classname;

    }
    /* this launches custom function which is run before action is marked done */
    public function CallBeforeDone(){
        $classname = $this->controllerImport();
        $aetobj = new $classname;

        if(method_exists($aetobj,'beforeDone')){
            return $this->beforeDone();
        } else {
            return true;
        }
    }

    /* handles validating the input */

    public function CallInputValidation(){
        $classname = $this->controllerImport();
        $aetobj = new $classname;

        if(method_exists($aetobj,'inputValidation')){
            return $this->inputValidation();
        } else {
            return true;
        }

    }

    public function saveForm(){

        $class = 'AeplayVariable';
        $model = new AeplayVariable;
        $token = $_REQUEST['token'];

		$avr=0;
		
		if (isset($this->configdata->update_average) AND $this->configdata->update_average==1) $avr=$this->configdata->update_average;
		
        if(isset($_POST[$class]) && isset($_POST['save'])){

            if(!$this->CallInputValidation()){
                return $this->addform();
            }

            if(isset($_POST[$class]['variable1'])){
				
                $num = 1;
                $val = 'variable' .$num;

                if(isset($_POST[$class]['comment'])){
                    AeplayAction::saveComment($this->ptid,$_POST[$class]['comment']);
                    unset($_POST[$class]['comment']);
                }

                while(isset($_POST[$class][$val])){
                    $v_id = 'variable_id' .$num;
                    $v_value = 'variable' .$num;
					$v_visible = 'visible' .$num;
					$v_fieldtype = 'fieldtype' .$num;

                    $id = $_POST[$class][$v_id];
                    $value = $_POST[$class][$val];
					
					//print_r($this->configdata->$v_fieldtype); die;

                   if ($this->configdata->$v_fieldtype=='file') {
					  $this->saveVariable($id,$_FILES[$class], $avr, 'file', $v_value);
					} else {
                      $this->saveVariable($id,$value, $avr, '', $v_value);
					}
					
					if (isset($this->configdata->email_coach) AND $this->configdata->email_coach!='' AND isset($this->configdata->$v_visible) AND ($this->configdata->$v_visible==1)) {
					 
					  $this->sendFeedback ($this->configdata->email_coach, $_POST[$class][$val]);
					  $this->saveVariable($this->configdata->variable2,'');
					  $pto = AeplayAction::model()->findByPk($this->task->id);
			              if ($pto->essay_status!=1) {
							  $pto->expires = NULL;
			                  $pto->essay_status = 1;
                              $pto->update();
			              }
				   } 
					
                    $num++;
                    $val = 'variable' .$num;
                }

                $this->CallBeforeDone();

                $url = '&c=' .$_POST[$class]['variable1'];
                Yii::app()->request->redirect(Yii::app()->i18n->BaseUrl() .$this->playurl .'done?tid=' .$this->taskdata->id .'&token=' .$token .$url .$this->branchurl);
                Yii::app()->end();

            } elseif(isset($this->configdata->variable)) {
                
				if ($this->configdata->fieldtype=='file') {
				   $this->saveVariable($this->configdata->variable,$_FILES[$class], $avr, $this->configdata->fieldtype );
				    $url = '';
                } else {
				    $this->saveVariable($this->configdata->variable,$_POST[$class]['value'], $avr);
                    $url = '&c=' .$_POST[$class]['value'];
				}
                $this->CallBeforeDone();



                Yii::app()->request->redirect(Yii::app()->i18n->BaseUrl() .$this->playurl .'done?tid=' .$this->taskdata->id .'&token=' .$token .$url .$this->branchurl);
               Yii::app()->end();
            }
        } elseif(isset($_REQUEST['c'])){
			
            $this->saveVariable($this->configdata->variable,$_REQUEST['c'], $avr);
            $this->CallBeforeDone();
            $url = '&c=' .$_REQUEST['c'];

            Yii::app()->request->redirect(Yii::app()->i18n->BaseUrl() .$this->playurl .'done?tid=' .$this->taskdata->id .'&token=' .$token .$url .$this->branchurl);
            Yii::app()->end();

        }

        return $this->addForm();

    }



    public function saveVariable($variableid,$value,$avr=false,$filedtype='',$varname =''){
		
		if($variableid == 0){
            return false;
        }

		$variableValue= $value;
		
		if ($filedtype=='file') {
			 $filelink=$this->actionUploadImage($value, $varname);
			 $variableValue= $filelink;
		}
        
        $obj = AeplayVariable::model()->findByAttributes(array('variable_id' => $variableid, 'play_id' => $this->taskdata->play_id));
		
	   if (isset($avr) AND $avr==1) {
				AeplayAction::saveAvrAnswer($obj, $variableValue, $varname);
		} else {

        if(is_object($obj)){
            $obj->value = $variableValue;
            $obj->update();
        } else {
            $model = new AeplayVariable;
            $model->value = $variableValue;
            $model->play_id = $this->taskdata->play_id;
            $model->variable_id = $variableid;
            $model->validate();
            $model->insert();
        }
	  }
    }



    private function addForm(){
        $class = 'AeplayVariable';
        $model = new $class();
        $formfields = array();

        if(isset($this->configdata->variable1)){
            $num = 1;
            $name = 'variable' .$num;

            while(isset($this->configdata->$name)){

                $v_id = 'variable_id' .$num;
                $v_value = 'value' .$num;
                $v_fieldtype = 'fieldtype' .$num;
                $v_variable = 'variable' .$num;
                $v_fieldtitle = 'fieldtitle' .$num;
                $v_fieldhint = 'fieldhint' .$num;
				$v_visible = 'visible' .$num;
		
		   if (($this->configdata->$v_fieldtype!='0') && ($this->configdata->$v_variable!='') ) {
				
			if (!isset($this->configdata->$v_visible) || ($this->configdata->$v_visible==1)) {

                if(isset($this->configdata->$v_fieldtype)){
                    $type = $this->configdata->$v_fieldtype;
                } else {
                    $type = 'textarea';
                }

                if(isset($this->configdata->$v_value)){
                    $v_value = $this->configdata->$v_value;
                } else {
                    $v_value = '';
                }

                if(isset($this->configdata->$v_fieldtitle)){
                    $fieldtitle = $this->configdata->$v_fieldtitle;
                } else {
                    $fieldtitle = '';
                }

                if(isset($this->configdata->$v_fieldhint)){
                    $fieldhint = $this->configdata->$v_fieldhint;
                } else {
                    $fieldhint = '';
                }

                if(isset($_POST['AeplayVariable'][$v_id])) { $model->$v_id = $_POST['AeplayVariable'][$v_id]; }
                if(isset($_POST['AeplayVariable'][$v_variable])) { $model->$v_variable = $_POST['AeplayVariable'][$v_variable]; }
				
				
				$valueVarible='';
		    	if (($this->taskdata->aetask->editable==1 &&  $this->taskdata->status>1) || ($this->taskdata->aetask->remains_visible==1 &&  $this->taskdata->status>1)) {
					
					 $obj = $model::model()->findByAttributes(array('variable_id' => $this->configdata->$v_variable, 'play_id' => $this->taskdata->play_id));
					 $valueVarible= $obj->value;
					 
			    }

                $formfields['elements'][$v_id] = array('type' => 'hidden','value' => $this->configdata->$v_variable);
				if ($fieldtitle!='') {
			      $formfields['elements'][$v_variable] = array('type' => $type, 'value' => $valueVarible,'prepend' => $fieldtitle, 'hint' => $fieldhint); } else {
				  $formfields['elements'][$v_variable] = array('type' => $type, 'value' => $valueVarible, 'hint' => $fieldhint);	
					
				}
			}
			
		   }
                $num++;
                $name = 'variable' .$num;
			 
            }


        } else {
            
            if(isset($this->configdata->fieldtype)){
                $v_fieldtype = $this->configdata->fieldtype;
            } else {
                $v_fieldtype = 'textarea';
            }
            
			
			$confValue='';
			if (isset($this->configdata->variable)) {
				$confValue=$this->configdata->variable;
			}
		    
			
			$valueVarible='';
			if (($this->taskdata->aetask->editable==1 &&  $this->taskdata->status>1) || ($this->taskdata->aetask->remains_visible==1 &&  $this->taskdata->status>1)) {
					
					 $obj = $model::model()->findByAttributes(array('variable_id' => $confValue, 'play_id' => $this->taskdata->play_id));
					 $valueVarible= $obj->value;
			}
				
			
			
            $formfields['elements']['variable_id'] = array('type'=> 'hidden', 'value' => $confValue);
            $formfields['elements']['value'] = array('type'=> $v_fieldtype, 'value' => $valueVarible);
			
		
			
        }

        $formfields['elements']['pid'] = array('type'=> 'hidden', 'value' => $this->taskdata->play_id);
        $formfields['elements']['token'] = array('type'=> 'hidden', 'value' => $this->token);
        
	   
	    $btnTxt='{%play_it%}';
        if ($this->taskdata->aetask->buttontxt!='') $btnTxt=$this->taskdata->aetask->buttontxt;

        $style = $this->getBtnColor(false);
		

        if ($this->taskdata->aetask->editable==1 &&  $this->taskdata->status>1) {
	         $formfields['buttons'] = array('save' => array('type' => 'htmlSubmit','label' => $btnTxt,'id' => 'donebtn',
				 'htmlOptions'=>array('style' => $style)));
		} else if ($this->taskdata->status==1)  {
			$formfields['buttons'] = array('save' => array('type' => 'htmlSubmit','label' => $btnTxt,'id' => 'donebtn', 'htmlOptions'=>array('style' => $style)));

		}
		//print_r($formfields); die;

        if(isset($this->actiondata->commenting) AND $this->actiondata->commenting == 1){
            $formfields['elements']['comment'] = array('type' => 'divider');
            $formfields['elements']['comment'] = array('type' => 'textarea', 'hint' => $this->actiondata->commenting_brief);
        }

        $params = Controller::formParameters(false,1);
        $form = TbForm::createForm($formfields,$model,$params);
		
         // use this for debugging the form
         // $form->render();
         // die();
        
        return $form;
    }

  
  
   public function sendFeedback ($emails, $text) {
	   
	        Yii::import('userGroups.models.UserGroupsUseradmin');

            $userobj = UserGroupsUseradmin::model()->findByPk($this->task->user_id);
			if ( isset($userobj->email) && $userobj->email!='' ) {
				$sender = $userobj->email;
			} else {
				$sender =Yii::app()->params['adminEmail'];
			}
            
			
	  
	        $emails = str_replace(" ","", trim($emails));
	        $emails = explode(',',$emails);
			
			
		 for ($i=0; $i<count($emails); $i++) {
			  
			  $to = $emails[$i];
			
	          $appurl = Yii::app()->Controller->getDomain();
              $link = $appurl .'/'.Yii::app()->i18n->getCurrentLang() .'/aeplay/public/essayreview?token=' .$this->token ;
			
              $buttons = "<a href='".$link."'>review</a>";
              $msg = new YiiMailMessage;
              $indentended = HtmlHelpers::htmlIndententedText($text . '<br><br>' . $buttons);
              $body = HtmlHelpers::htmlTitle("Feedback to coach") .$indentended;
           
              $body = str_replace('src="/', 'style="max-width:550px;" src="' . $appurl . '/', $body);
              $body = str_replace('{{email}}',$sender,$body);
              $body = HtmlHelpers::htmlMail($body);
              $msg->setBody($body, 'text/html');
			  
			  
			  
			  //gid
			  $game = Aegame::model()->findByPk($this->gameid);
              $msg->from =  array($sender => $game->name);
              $msg->subject = str_replace('{{email}}',$sender,"Feedback to coach");
              $msg->addTo($to);
			  
			 
            try {
                Yii::app()->mail->send($msg);
            } catch (Exception $e) {
                Yii::log('Mail failed ' . $e, 'error', 'application.cli.logic');
            }
		
		 }
		 
		 
	  
	   } 
   
     
	 
	 public function actionUploadImage($fileUpload, $varname='value'){
       
        $type = strtolower($fileUpload['type'][$varname]);

        if($type == 'image/png'){
            $extension = '.png';
        } elseif($type == 'image/jpg'){
            $extension = '.jpg';
        }elseif($type == 'image/gif'){
            $extension = '.gif';
        }elseif($type == 'image/jpeg'){
            $extension = '.jpg';
        } else {
            $extension = '';
        }
      
	    $filelink = '';
        if($extension != ''){
			$filepath = Controller::getDocumentsFolder($this->gameid);
			 
			 if(!is_dir($filepath)){
                mkdir($filepath);
            }

            $filepath = $filepath.'/userupload';

            if(!is_dir($filepath)){
                mkdir($filepath);
            }

            $filepath = $filepath .'/images';

            if(!is_dir($filepath)){
                mkdir($filepath);
            }

            $name = explode('.', $fileUpload['name'][$varname]);

            $file = $name[0] ."_".time().$extension;
            copy($fileUpload['tmp_name'][$varname], $filepath .'/' .$file);

            $filelink= '/documents/games/' .$this->gameid .'/userupload/original_images/' .$file;
           
        
        }

      return  $filelink;
    }
	 
   
      public function skipBtn() {

		if($this->isComponent === true){
            return false;
        }
		$style = $this->getBtnColor(false);
		$output = '<a href="'.$this->skipurl.'" class="btn btn-success" style="'.$style.'">{%skip_action%}</a>';
		return $output;
	 }
   
   
   
    public static function audioFile($config, $gid, $actionId) {
	  
	    if(isset($config->audio_autostart) && $config->audio_autostart == 1){
            $autostart = 'true';
            $autostart2 = 'autoplay';
        } else {
            $autostart = 'false';
            $autostart2 = '';
        }

        if(isset($config->audio_showplayer) &&  $config->audio_showplayer == 1){
            $hidden = '';
            $controls2 = 'controls';
        } else {
            $hidden = 'hidden="true"';
            $controls2 = '';
        }

        if(isset($config->audio_loop) &&  $config->audio_loop == 1){
            $loop = 'true';
            $loop2 = 'loop';
        } else {
            $loop = 'false';
            $loop2 = '';
        }

        $file = '/documents/games/' .$gid .'/mp3s/' .$config->audio;
		$html='';
		
		//if (file_exists($file)) {
        
		 $fallback = '<embed src="' .$file .'" width="250" height="240" autostart="' .$autostart .'" loop="' .$loop .'" repeat="true" ' .$hidden .' class="mediaplayer"></embed>';

   
        $html = '<audio '.$controls2 .' ' .$autostart2 .' ' .$loop2 .' preload hidden width="95%">
                    <source src="' .$file .'" type="audio/mpeg" autoplay="autoplay">' .$fallback .'</audio> <br/> <br />';
		//}

        return $html;

	   
  }
   
   
  /* public static function fbShareActions($url, $view=0){
	   
	    $btn='/images/afb.png';
		$float="right";
		$class="fb";
		if ($view==1) {
			$btn='/images/fb_share_big.png';
			$float="left";
			$class="";
		} 
		$html = '';
        $html .="<a href=\"#\" onclick=\"
    window.open(
      'https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent('".$url."'),'facebook-share-dialog','width=626,height=436'); return false;\" class=\"".$class."\" style=\"margin-top:10px; float:".$float.";\"><img src=\"".$btn."\" /></a>";
    
        return $html;
	} */
	
	
	
	 public static function fbShareActions($url, $view=0, $appId=''){
	   
	   
	    $btn='/images/afb.png';
		$float="right";
		$class="fb";
		$html = '';
		if ($view==1) {
			if ($appId=='') {$appId=Yii::app()->params['fbAe'];}
			$html.='<div id="fb-root"></div>
           <script>(function(d, s, id) {
               var js, fjs = d.getElementsByTagName(s)[0];
               if (d.getElementById(id)) return;
               js = d.createElement(s); js.id = id;
               js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&appId='.$appId.'&version=v2.0";
               fjs.parentNode.insertBefore(js, fjs);
           }(document, \'script\', \'facebook-jssdk\'));
		   </script>
		   
		   <div class="fb-share-button" data-href="'.$url.'" data-width="300" data-layout="button"></div>';
		} else {
		
        $html .="<a href=\"#\" onclick=\"
    window.open(
      'https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent('".$url."'),'facebook-share-dialog','width=626,height=436'); return false;\" class=\"".$class."\" style=\"margin-top:10px; float:".$float.";\"><img src=\"".$btn."\" /></a>";
		}
        return $html;
	}
   
   
 
}

?>