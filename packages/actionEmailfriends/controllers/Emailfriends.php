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

class Emailfriends extends ActivationEngineAction {

    public $validationError = '';
	public $gameid;

	 public function disableScripts(){
        return array('disableBootstrap' => true, 'disableDefaultCss' => true, 'disableJquery' => true);
    }

    public function render(){

        $this->init();

        if(isset($_POST['AeplayVariable'])){
            $this->inputValidation();
        }

        $this->output = '';

        /* main content */

        if($this->validationError){
            $this->output .= '<div class="validationError">' .$this->validationError .'</div>';
        }

        if(isset($this->configdata->msg)){
            $this->output .= $this->configdata->msg .'<br>';
        }
        $this->output .= $this->saveForm();
        $this->output .= '<br><br>';

        $this->output .= '{%this_is_what_your_friends_see%}:<div class="friendmsg">';
        if(isset($this->configdata->subject_tofriend)){
            $this->output .= '<b>{%subject%}:</b><br>' .$this->configdata->subject_tofriend .'<br>';

        }

        if(isset($this->configdata->msg_tofriend)){
            $this->output .= '<b>{%message%}:</b><br>' .$this->configdata->msg_tofriend .'<br>';
        }
        $this->output .= '</div>';

        // $this->output .= $this->donebtn;
		
		
			
		if (isset($this->configdata->skip_action_posibility) && ($this->configdata->skip_action_posibility==1)) {
		$this->output .='<br />';
		$this->output .= $this->skipBtn();
		}
        return $this->output;
    }



    /* this will save the friends answer and return the thank you msg
        note: this is called from aeplay/homecontroller which means, that the parent
        class init is not done
    */

    public function friendanswer(){

        /* request data */
        $user = urldecode($_REQUEST['e']);
        $choice = $_REQUEST['c'];
        $token = $_REQUEST['token'];

        $playtaskobj = AeplayAction::model()->with('aetask','aeplay')->findByAttributes(array('shorturl' => $token));

        if(!$user OR !$choice OR !$token){
            return '{%unknown_error%}';
        }

        /* get the data based on the token */
        $config = json_decode($playtaskobj->aetask->config);
        $userobj = UserGroupsUseradmin::model()->findByPk($playtaskobj->aeplay->user_id);
        $msg = str_replace('{{email}}', $userobj->username, $config->friendthanks);

        /* ids */
        $playid = $playtaskobj->aeplay->id;

        /* variable id's from action setup */
        $emails = $config->variable1;
        $data = $config->variable2;
		

        /* objects */
        $emailsdata = AeplayVariable::model()->findByAttributes(array('variable_id' => $emails, 'play_id' => $playid));
        $dataobj = AeplayVariable::model()->findByAttributes(array('variable_id' => $data, 'play_id' => $playid));
		

        if(!is_object($emailsdata) OR !is_object($dataobj)){
            return '{%unknown_error%}';
        }

        /* check that email matches */
        $emails = $this->cleanEmails($emailsdata->value);

        if(!in_array($user,$emails)){
            return '{%unknown_error%}';
        }

        /* check whether user has answered already */

        /* save answer */
        if($dataobj->value){
            $this->saveAnswer($dataobj);
        } else {
            /* if you end up here, it means that something has gone wrong when saving the answer */
            return '{%unknown_error%}' .__LINE__;
        }

        return $msg;
    }


    public function saveAnswer($dataobj){
        /* request data */
		
        $user = urldecode($_REQUEST['e']);
        $choice = $_REQUEST['c'];
        $token = $_REQUEST['token'];

        $playtaskobj = AeplayAction::model()->with('aetask','aeplay')->findByAttributes(array('shorturl' => $token));
	    $playid = $playtaskobj->aeplay->id;
        $config = json_decode($playtaskobj->aetask->config);
       
		$average = $config->average_save;
		$averageobj =  AeplayVariable::model()->findByAttributes(array('variable_id' => $average, 'play_id' => $playid));

       

        $return = array();
		 if(isset($averageobj->parameters)){
        $dataarray = json_decode($averageobj->parameters);
		 }

        $answer = str_replace('choice', '', $_REQUEST['c']);

        if(isset($dataarray->user)){
            $return['user'] = (array) $dataarray->user;
        }
       
        /* user has already answered */
        if(isset($dataarray->user->$user->answered)){
            return '{%answer_already_recorded%}';
        } else {
            $return['user'][$user]['answered'] = 1;
        }

        if(isset($dataarray->answercount)){
            $return['answercount'] = $dataarray->answercount+1;
            $return['totalsum'] = $answer+$dataarray->totalsum;
        } else {
            $return['answercount'] = 1;
            $return['totalsum'] = $answer;
        }

        $return['user'][$user]['answer'] = $answer;

        if(isset($dataarray->custommsg)){
            $return['custommsg'] = $dataarray->custommsg;
        } else {
            $return['custommsg'] = '';
        }

        $return['average'] = $return['totalsum'] / $return['answercount'];





        /* save average to variable & award extra points if needed */
        if($return['answercount'] >= $config->how_many_friends){
            /* saving average variable */
            $dataobj = AeplayVariable::model()->findByAttributes(array('variable_id' => $average, 'play_id' => $playid));

            if(is_object($dataobj)){


             $dataarray = json_decode($dataobj->parameters);

       
            if(isset($dataarray->answercount)){
			  //echo $answercount." ".$totalsum." ".$average. "<br />"; 
			  
              $return['answercount'] = $dataarray->answercount+1;
              $return['totalsum']  = $answer+$dataarray->totalsum;
              $return['average'] = round(( $return['totalsum'] / $return['answercount']),2);
            
			 

	 	      } else {
		      $return['answercount'] =2;
	     	  $return['totalsum']=$dataobj->value+$answer;
		      $return['average']=round($return['totalsum']/2, 2);
			  
            	
		
		    }

      
		   

			
           
			
			
			} else {
                $dataobj = new AeplayVariable();
                $dataobj->variable_id = $average;
                $dataobj->play_id = $playid;
				$dataobj->parameters = $return['average'];
                $dataobj->value = $return['average'];
                $dataobj->save();
            }

            /* awarding points */
            if(isset($config->extrapoints) AND !isset($return['extrapointssave'])){
                $pto = AeplayAction::model()->findByPk($playtaskobj->id);
                $pto->points = $playtaskobj->points+$config->extrapoints;
                $return['extrapointssave'] = 1;
                $pto->update();
            }
        }


         $dataobj->value = $return['average'];
	     $dataobj->parameters = json_encode($return);
         $dataobj->save();

    }



    public function generateButtons($email){
        $btncount = 1;
        $choice = 'choice' .$btncount;
        $output = '';

        while(isset($this->configdata->$choice)){
            $appurl = Yii::app()->Controller->getDomain();
            $link = $appurl .'/' .Yii::app()->i18n->getCurrentLang() .'/aeplay/public/friendanswer?token=' .$this->token .'&c=' .$btncount .'&e=' .urlencode($email);
            $output .= HtmlHelpers::button($this->configdata->$choice,$link);
            $btncount++;
            $choice = 'choice' .$btncount;
        }

        return $output;

    }


    public function beforeDone(){

        $emails = $this->extractEmails();

        Yii::import('userGroups.models.UserGroupsUseradmin');

        $userobj = UserGroupsUseradmin::model()->findByPk($this->task->user_id);
        $sender = $userobj->username;

        /* reformat the custom message */


            if(isset($_POST['AeplayVariable']['variable2'])){
                    $custommsg = $_POST['AeplayVariable']['variable2'];
            } else {
                    $custommsg = '';
            }


                $arr = array();
                $arr['custommsg'] = $custommsg;
                $data = json_encode($arr);

                /* objects */
                $dataobj = AeplayVariable::model()->findByAttributes(array('variable_id' => $this->configdata->variable2, 'play_id' => $this->playid));
                $dataobj->value = $data;
                $dataobj->save();


        while($email = each($emails)){
            $to = $email[1];
            $buttons = $this->generateButtons($to);
            $msg = new YiiMailMessage;
            $indentended = HtmlHelpers::htmlIndententedText($this->configdata->msg_tofriend . '<br><br>' . $custommsg . '<br><br>' . $buttons);
            $body = HtmlHelpers::htmlTitle($this->configdata->subject_tofriend) .$indentended;
            $appurl = Yii::app()->Controller->getDomain();
            $body = str_replace('src="/', 'style="max-width:550px;" src="' . $appurl . '/', $body);
            $body = str_replace('{{email}}',$sender,$body);
            $body = HtmlHelpers::htmlMail($body);
            $msg->setBody($body, 'text/html');

            /// 
            $game = Aegame::model()->findByPk($this->gameid);
            if(stristr($sender,'@')){
                $msg->from =  array($sender => $game->name) ;
				 
   
            } else {
				
				$msg->from =  array('engine@aengine.net' => $game->name) ;
                
            }
            
            $msg->subject = str_replace('{{email}}',$sender,$this->configdata->subject_tofriend);
            $msg->addTo($to);

            try {
                Yii::app()->mail->send($msg);
            } catch (Exception $e) {
                Yii::log('Mail failed ' . $e, 'error', 'application.cli.logic');
            }
        }

        return true;
    }

    public function cleanEmails($emails){
        $emails = str_replace(chr(0),'',$emails);
        $emails = str_replace(chr(9),'',$emails);
        $emails = str_replace(chr(10),'',$emails);
        $emails = str_replace(chr(11),'',$emails);
        $emails = str_replace(chr(13),'',$emails);
        $emails = str_replace(chr(32),'',$emails);
        $emails = explode(',',$emails);
        return $emails;
    }

    public function extractEmails(){
        return $this->cleanEmails($_POST['AeplayVariable']['variable1']);
    }

    public function inputValidation(){
        Yii::import('application.validation.*');
        $validator = new CEmailValidator;
        $validator->checkMX = true;

        $emails = $this->extractEmails();

        if(count($emails) > 6){
            $this->validationError .= '{%sorry_max_6_emails%}<br>';
        }

        while($email = each($emails)){
            $email = $email[1];
            if(!$validator->validateValue($email)){
                $this->validationError .= '<b>' .$email .'</b> {%doesnt_look_valid_email%} <br>';
            }
        }

        if($this->validationError == ''){
            return true;
        } else {
            return false;
        }
    }
	
	
	
	 public function skipBtn() {
		
		$output = '<a href="'.$this->skipurl.'" class="btn btn-success" style="background:#'.$this->color_btn.';color:#'.$this->color_btn_text.';">{%skip_action%}</a>';
		return $output;
		}
	
	

}

?>