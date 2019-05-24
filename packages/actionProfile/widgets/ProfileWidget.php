<?php

/* renders individual task

    $this->task->icon
    $this->task->aetask->config
    $this->task->id
    $this->token
    $this->task->aetask->negative_points
    $this->task->aetask->type_id
    $this->taskdata->aetask->taskid
    $this->taskdata->id
    $this->taskdata->added
    $this->taskdata->aetask->timelimit
    $this->taskdata->play_id

*/

class ProfileWidget extends CWidget
{

    public $task;
    public $token;
	public $variable;

    public function run(){
		

        $html = '';
        $icon = Yii::app()->Controller->getDomain() .'/images/milky/32/' .$this->task->icon;

        $config = json_decode($this->task->aetask->config);
		$user = UserGroupsUser::model()->findByPk($this->task->user_id);

		$answer = AeplayVariable::model()->findByAttributes(array('variable_id' => $config->variable1, 'play_id' => $this->task->play_id));
		$feedback = AeplayVariable::model()->findByAttributes(array('variable_id' => $config->variable2, 'play_id' => $this->task->play_id));

        /* top row */
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		                          // 'action' => 'donefeedback?token='.$this->token,
                                   'id'=>'verticalForm',
                                   'htmlOptions'=>array('class'=>'well'),
                                   'enableClientValidation'=>true,
                                   'clientOptions'=>array(
                                   'validateOnSubmit'=>true,
	     ),
        ));
		
		  
       echo '<div class="tasktitle"><div class="tasktitletext">{%essay_review%}:</div></div>';
	   echo '<strong>{%answer_from_user%}</strong>: '.$user->username.'<br /><br />';
	   echo $answer->value.'<br /><br />';
	   if ($this->task->essay_status!=2) {
	    if (isset($config->type_points) AND $config->type_points==1) {
         echo $form->textFieldRow($this->task, 'points', array('class'=>'span2', 'value' =>'', 'hint' => '{%put_here_points_between%} '.$config->min_flexible_points.'-'.$config->max_flexible_points));
	    } else {
	     echo $form->textFieldRow($this->task, 'points', array('class'=>'span2', 'readonly' => 'readonly', 'value' =>$config->fixed_points));
	    }
		
		if ($config->coach_feedback==1) {
	      echo $form->redactorRow($feedback, 'value', array());
	    }
		
	  $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'{%accept%}'));
      $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'button', 
	                                                    'label'=>'{%decline%}', 
	                                                    'htmlOptions'=>array( 
	                                                           'onclick'=>'javascript: window.location = "?token='.$this->token.'&deslineessay=1"')));
		
	  
	   } else {
		   if (isset($feedback->value) && $feedback->value!='') {
		       echo '<strong>{%coach_feedback%}</strong>:<br />'.$feedback->value.'<br /><br />'; 
		    }
		echo '<strong>{%thanks_for_review_coach%}!</strong><br /><br />';   
	    }
	    
       echo '<br /> <br />';

      

      $this->endWidget();

        return true;
    }


   


}