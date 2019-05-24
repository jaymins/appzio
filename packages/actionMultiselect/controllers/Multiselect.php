<?php Yii::import('aeapi.controllers.ImagesController');

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


class Multiselect extends ActivationEngineAction {
    
	public $changeColorOfCorrect=1;

	 public function disableScripts(){
        return array('disableBootstrap' => true, 'disableDefaultCss' => true, 'disableJquery' => false);
    }
    
    public function render(){

        $this->init();
        $pto = AeplayAction::model()->with('aeplay','aetask')->findByAttributes(array('shorturl'=>$_REQUEST['token']));
		
        $this->output = '';
		
		if (isset($this->configdata->show_graph) && ($this->configdata->show_graph==1) && (isset($_REQUEST['showgraph'])) ) {
		    $aeVar=Aevariable::model()->findByPK($this->configdata->variable);
			if (is_object($aeVar)) {
		      $stats = new StatsContent();
			  $stats->playid=$this->playid;
			  $stats->taskid=$pto->action_id;
			  $stats->tasktype=$this->actiontype;
		      $this->output .=$stats->processContent("{{stats:".$aeVar->name.",barchart,1}}");
	    	 
			}
			$this->output .= '</br></br>';
			if(isset($_REQUEST['branchid'])){ $branch = '&branchid=' .$_REQUEST['branchid']; } else { $branch = ''; }
			$url=$this->doneurl .'&tid=' .$this->taskdata->id .$branch;

            /* get button colors style css */
            $style = $this->getBtnColor(true);

            $this->output .= ActivationEngineAction::continueButton($url,$style);
	    }  else {
	   
        /* main content */


		if (isset($this->configdata->msg)) {
        $this->output .= $this->configdata->msg .'<br><br>';
		}
		
	
		
       
	   
	    if (($pto->aetask->editable!=1) && ($pto->status >1) && (($pto->aetask->remains_visible==1))) {
	
		$answer = AeplayVariable::model()->findByAttributes(array('variable_id' => $this->configdata->variable, 'play_id' => $pto->play_id));
		$this->output .= '{%your_value_is%}: '.$answer->value;   
		   
	    } else {
		  
		   $pointsTypeToSubtract='';
		   if (isset($this->configdata->which_points_to_subtract) && ($this->configdata->which_points_to_subtract!='')) 
		                         $pointsTypeToSubtract=$this->configdata->which_points_to_subtract;
		   
		   $answersToHide=0;
		   if (isset($this->configdata->answers_to_hide) && ($this->configdata->answers_to_hide!=''))
		                         $answersToHide=$this->configdata->answers_to_hide;
					   
		   $correctAnswer='all';	  
		   if (isset($this->configdata->correct_answer) && ($this->configdata->correct_answer!='')) {
			                      $correctAnswer=$this->configdata->correct_answer;
								  $bImg='choice'.$correctAnswer.'_img';
								  $configbImg='';
								
								  if (isset($this->configdata->$bImg))
								      $configbImg=$this->configdata->$bImg;
			                  
							      if (($configbImg!='') && ($configbImg!=0) && ($configbImg!=1))  $this->changeColorOfCorrect=0;
			
		        
		   }
		   
		   
		   
		   $pointsToSubstract=0;			
		   if (isset($this->configdata->points_to_subtract) && ($this->configdata->points_to_subtract!='')) 		  
		                           $pointsToSubstract=$this->configdata->points_to_subtract;	
			
		    $configShowCorrectAnswer=false;					   
	        if(isset($this->configdata->show_the_correct_answer))
                $configShowCorrectAnswer=$this->configdata->show_the_correct_answer;
           
            
			$configOptsRandomly=false;
            if(isset($this->configdata->show_options_randomly))
                 $configOptsRandomly=$this->configdata->show_options_randomly;
            

           $configShowTwoButtOnRow=0;
            if(isset($this->configdata->show_2_buttons_on_row))
                 $configShowTwoButtOnRow=$this->configdata->show_2_buttons_on_row;
            
		   $view='selector';
           if ($configShowTwoButtOnRow==1)
								$view='selectorTwoButtonsOnRow';		  
		 
		 
		   $userPoints=0;
		   switch($pointsTypeToSubtract) {
			   case 'primary':   $typePoints='points'; $userPoints=@Aeplay::getUserPlayPoints($pto->play_id); break;
			   case 'secondary': $typePoints='secondary_points'; $userPoints=@Aeplay::getUserSecondaryPoints($pto->play_id); break;
			   case 'tertiary':  $typePoints='tertiary_points'; $userPoints=@Aeplay::getUserTertiaryPoints($pto->play_id);  break;
			   
		   }
		  
		  
		  $choices=array(1,2,3,4,5); 
		  $choicesAvailable=array();
		  $subtract=0;
		 
		  if (isset($_REQUEST['subtract']) && $_REQUEST['subtract']==1) {
		     $subtract=1;
		  
		    // to subtract array random 
			 shuffle($choices);
			 $choicesAvailable=array_slice($choices, $answersToHide);
			
			 asort($choicesAvailable);
			 
			 if (!in_array($correctAnswer,$choicesAvailable) && $correctAnswer!='all') {
				 
				 $choicesAvailable[count($choicesAvailable)-1]=$correctAnswer;
				 asort($choicesAvailable);
				 
			 }
			 asort($choices);
			
			 //subtrackpoints
			    
				if ($pointsToSubstract<=$userPoints) {
				
				  $playtaskobj = @AeplayAction::model()->findByPk($pto->id);
				  
				  $playtaskobj->$typePoints = $playtaskobj->$typePoints - $pointsToSubstract; 
		          $playtaskobj->update();
			
				 }
		   } 
		  
	  	  $cChoicesAvailable=count($choicesAvailable);
		 
		 
		    if (($answersToHide>0) && ($pointsToSubstract<=$userPoints) && (!isset($_REQUEST['done'])) ) {
				 
		             $this->output .=$this->subtractOptionsButton($pto->id,$_REQUEST['token'],$pointsToSubstract, $subtract);
			}
		
		
		    if (isset($configOptsRandomly) && ($configOptsRandomly==1))   shuffle($choices);
			 
			
			$path= 'documents/games/'.$pto->aeplay->game_id.'/original_images/';
			
           
			for ($i=0; $i<count($choices); $i++){
				if ($cChoicesAvailable>0) {
				
				  $active=0;
				  if (in_array($choices[$i],$choicesAvailable)) 
					                          $active=1;
				  $this->output .= $this->$view($choices[$i],$pto->status,1,$active,$path);
				} else {
                 
				  $this->output .= $this->$view($choices[$i],$pto->status,0,1,$path);
				}
			}
	 }

		if (isset($this->configdata->skip_action_posibility) && ($this->configdata->skip_action_posibility==1)) {
		$this->output .='<br />';
		$this->output .= $this->skipBtn();
		}
        $this->output .= '<br><br>';

			   
		}
			   
			   
		return $this->output;
    }

  
  


  private function flashingButtonJS($number,$correctAnswer,$doneUrl,$changeColor=1) {
	   
	
	   $output="<script type=\"text/javascript\">
                  $('#choice".$number."').click(function(){";
	   
	   if ($this->changeColorOfCorrect==1) 
                 $output.="$('#choice".$correctAnswer."').addClass('lightgreen');";
	 
	   if (($correctAnswer!=$number) && ($changeColor==1))
		          $output.="$('#choice".$number."').addClass('red');";
		
	  
	   $output.="$('#choice".$correctAnswer."').fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);";
	   
	   if ($this->changeColorOfCorrect==1) 
				 $output.="$('#choice".$correctAnswer."').addClass('blue');";
                      
     
	  $output.="setTimeout(window.location.href = \"".$doneUrl."\",3000); 

                });
                </script>";
	  
	   return $output;
	}



    private function imgDimension($which){

        if(isset($this->configdata->$which) AND $this->configdata->$which){
            return $this->configdata->$which;
        } else {
            return 200;
        }

    }
	


    private function selector($number,$status=1,$subtract=0,$active=1,$path){

	    $var = 'choice' .$number;
		$addClass='';
		$tag='a';	
		$class='btn';
		$doneUrl=$this->doneurl .'&c=' .$number;
		if (isset($this->configdata->show_graph) && ($this->configdata->show_graph==1) && (!isset($_REQUEST['showgraph'])) ) {
		  $doneUrl.='&showgraph=1';
		}
		$href='href="' .$doneUrl .'"';
	    
		$configShowCorrectAnswer=0;					   
	    if(isset($this->configdata->show_the_correct_answer))
                $configShowCorrectAnswer=$this->configdata->show_the_correct_answer;
	
		$confCorrectAnswer=0;					   
	    if(isset($this->configdata->correct_answer))
                $confCorrectAnswer=$this->configdata->correct_answer;
		
		if ($active==0) {
			
			$tag='div';	
		    $class='asdiv';
		    $href='';
		     
		}
		
		if ($configShowCorrectAnswer==1) {
					
					  if (($confCorrectAnswer!='') && ($confCorrectAnswer!='all') )   $href='href="#"';
						
					  if (($subtract==1) && ($active==0)) $addClass='grey';
								 
		 }
		
	
		$backImg=$var.'_img';
		$backImgStyle='';
	    $changeColor=1;


        /* get image parameters from action */
        $width = $this->imgDimension('image_width');
        $height = $this->imgDimension('image_height');

        /* get button colors style css */
        $style = $this->getBtnColor(false);

        if(isset($this->configdata->image_round) AND $this->configdata->image_round == 1){
            $crop = 'round';
        } else {
            $crop = 'yes';
        }

		if (isset($this->configdata->$backImg) && ($this->configdata->$backImg!='')) {
			if (($this->configdata->$backImg!=0) && ($this->configdata->$backImg!=1)) {

                $ic = new ImagesController('multiselect');
                $pic = $ic->getImageUrl($width,$height,$path .$this->configdata->$backImg,'90',$crop,'filepath');

                $backImgStyle="background: url('".$pic."') left top no-repeat;";
			   $changeColor=0;
			}
		}

        if(isset($this->configdata->$backImg) AND $this->configdata->$backImg){

            $output = '<'.$tag.' id="choice'.$number.'" class="'.$class.' multiselect '.$addClass.'" '.$href.' style="width:' .$width .'px; height:' .$height .'px; margin-bottom:5px; '.$backImgStyle.'">';
            $output .= $this->configdata->$var;
            $output .= '</'.$tag.'><br>';
				
		   if (($configShowCorrectAnswer==1) ) {
			  if (($confCorrectAnswer!='') && ($confCorrectAnswer!='all') )
			         $output .=$this->flashingButtonJS($number,$confCorrectAnswer,$doneUrl,$changeColor);
		   }
           
		    return $output;
        } elseif(isset($this->configdata->$var) AND strlen($this->configdata->$var) > 0){


            $output = '<'.$tag.' id="choice'.$number.'" class="'.$class.' multiselect '.$addClass.'" '.$href.' style="width:80%; margin-bottom:5px; '.$backImgStyle .$style .'">';
            $output .= $this->configdata->$var;
            $output .= '</'.$tag.'><br>';

            if (($configShowCorrectAnswer==1) ) {
                if (($confCorrectAnswer!='') && ($confCorrectAnswer!='all') )
                    $output .=$this->flashingButtonJS($number,$confCorrectAnswer,$doneUrl,$changeColor);
            }

            return $output;
        }


    }
	
	
    private function selectorTwoButtonsOnRow($number,$status=1,$subtract=0,$active=1,$path){
       
	    $var = 'choice' .$number;
		$addClass='';
		$tag='a';	
		$class='btn';
		$doneUrl=$this->doneurl .'&c=' .$number;
		$href='href="' .$doneUrl .'"';
	    
		$configShowCorrectAnswer=0;					   
	    if(isset($this->configdata->show_the_correct_answer))
                $configShowCorrectAnswer=$this->configdata->show_the_correct_answer;
	
		$confCorrectAnswer=0;					   
	    if(isset($this->configdata->correct_answer))
                $confCorrectAnswer=$this->configdata->correct_answer;
		
		if ($active==0) {
			
			$tag='div';	
		    $class='asdiv';
		    $href='';
		     
		}
		
		if ($configShowCorrectAnswer==1) {
					
					  if (($confCorrectAnswer!='') && ($confCorrectAnswer!='all') )   $href='href="#"';
						
					  if (($subtract==1) && ($active==0)) $addClass='grey';
								 
		 }
		
	
		$backImg=$var.'_img';
		$backImgStyle='';
	    $changeColor=1;
		$linkContent='&nbsp';
		$generalClass=' multiselect imgbutt ';
		if(isset($this->configdata->$var) AND strlen($this->configdata->$var) > 0){
		$linkContent=$this->configdata->$var;
		 }

        /* get image parameters from action */
        $width = $this->imgDimension('image_width');
        $height = $this->imgDimension('image_height');

        if(isset($this->configdata->image_round) AND $this->configdata->image_round == 1){
            $crop = 'round';
        } else {
            $crop = 'yes';
        }


        if (isset($this->configdata->$backImg) && ($this->configdata->$backImg!='')) {
			if (($this->configdata->$backImg!=0) && ($this->configdata->$backImg!=1)) {
			    $pic=$path.$this->configdata->$backImg;
				
			    $ic = new ImagesController('multiselect');
                $pic = $ic->getImageUrl($width,$height,$pic,'90',$crop,'filepath');
			   
			    $linkContent='<img src="'.$pic.'" />';
			    $changeColor=0;
				$generalClass=' imgbutt ';
				$backImgStyle='background: transparent !important;';
			}
		}


        if(isset($this->configdata->$backImg) AND $this->configdata->$backImg){
			$tooltip=$this->configdata->$var;
            $output = '<'.$tag.' id="choice'.$number.'" class="'.$class. $generalClass .$addClass.'" '.$href.' style="'.$backImgStyle.'" title="'.$tooltip.'" rel="tooltip">';
            $output .= $linkContent;
            $output .= '</'.$tag.'>';
				
		   if (($configShowCorrectAnswer==1) ) {
			  if (($confCorrectAnswer!='') && ($confCorrectAnswer!='all') )
			         $output .=$this->flashingButtonJS($number,$confCorrectAnswer,$doneUrl,$changeColor);
		   }
           
		    return $output;
        } elseif(isset($this->configdata->$var) AND strlen($this->configdata->$var) > 0){
            $tooltip=$this->configdata->$var;
            $output = '<'.$tag.' id="choice'.$number.'" class="'.$class. $generalClass .$addClass.'" '.$href.' style="'.$backImgStyle.'" title="'.$tooltip.'" rel="tooltip">';
            $output .= $linkContent;
            $output .= '</'.$tag.'>';

            if (($configShowCorrectAnswer==1) ) {
                if (($confCorrectAnswer!='') && ($confCorrectAnswer!='all') )
                    $output .=$this->flashingButtonJS($number,$confCorrectAnswer,$doneUrl,$changeColor);
            }

            return $output;
        }



    }
		
	
	
	private function subtractOptionsButton ($ptid, $token, $points, $subtract=0) {
		     
		    $tag='a';	
			$class='btn';
			$addClass='green';
			$href='href="'.Yii::app()->Controller->getDomain() .'/' . Yii::app()->i18n->getCurrentLang().'/aeplay/home/showtask?ptid='.$ptid.'&token='.$token.'&subtract=1"';
			if ($subtract==1) {
			  $tag='div';	
			  $class='asdiv';	
			  $addClass='grey';
			  $href='';
			}
		   
		    $output = '<'.$tag.' class="'.$class.' multiselect '.$addClass.'" '.$href.' style="width:80%; margin-bottom:5px;">';
            $output .= '{%subtract_choices%}';
			if ($points!=0) {
			   $output .= ' -'.$points;
			}
            $output .= '</'.$tag.'><br><br><br>';
            return $output;
	}
	
	
	
    public function skipBtn() {
        $style = $this->getBtnColor(true);

        $output = '<a href="'.$this->skipurl.'"' .$style .' class="btn btn-success">{%skip_action%}</a>';
		return $output;
		}
		
	
	

}

?>

