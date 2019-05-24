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

class MobileinviteController extends ArticleController {

    public $toplist;
    public $special;
    public $buttonstyle = array(
        'text-color' => '#eff6ef',
        //'background_color' => '#50ca50',
        'font-style' => 'bold',
        'font-size' => '18',
        'padding' => '8 8 8 8',
        'radius' => '10'
    );

    public $fontstyle = array(
        'font-style' => 'normal',
        'font-size' => '12'
    );

    public $data;

    public function tab1(){

        if($this->getConfigParam('article_action_theme')){
            $theme = $this->getConfigParam('article_action_theme');
            $this->data = new StdClass();
            $name = $theme .'Invite';
            $this->$name();
            return $this->data;
        }

        $data = new StdClass();
        $imgstyle['crop'] = 'round';
        $imgstyle['margin'] = '20 90 10 90';
        $imgstyle['text-align'] = 'center';
        $imgstyle['variable'] = $this->vars['profilepic'];

        if(isset($this->menuid) AND $this->menuid == '5555') {
            $this->saveVariables();
        }

        if(isset($this->varcontent['profilepic'])){
            $profilepic = $this->varcontent['profilepic'];
        } else {
            $profilepic = 'anonymous2.png';
        }

        $output[] = $this->getImage($profilepic,$imgstyle);
        $output[] = $this->getFieldupload('{#change_image#}',array('type' => 'image','style' => 'general_button','variable' => $this->vars['profilepic']));

        $titlestyle['width'] = '60%';

       // $columns[] = $this->getImage('actionimage1',array('actionimage' => true,'width' => '10%'));

        $output[] = $this->addField('invite_from','{#from#}:','{#your_real_name#}');
        $output[] = $this->addField('invite_subject','{#subject#}:','{#your_phone#}');
        $output[] = $this->addField('email','{#email#}','{#your_email#}');
//        $output[] = $this->getImage('hairline-divider.png',array('height' => '1px'));

        $columns[] = $this->getText('{#notify#}',array('width'=>'70','text-align'=>'right')+$this->fontstyle);
        $columns[] = $this->getFieldonoff('1',array('alignment' => 'left','margin'=>'0 30 0 30'));

        $output[] = $this->getRow($columns,array('margin' => '5 10 5 30'));

        if(isset($this->menuid) AND $this->menuid == '5555'){
            $this->saveVariables();
        }

        //$output[] = $this->getImagebutton('save-button.png',5555,false,array('margin' => '15 0 0 0','sync_upload' => 1));


        $data->scroll = $output;
        $data->footer[] = $this->getTextbutton('{#save#}',array('id' => 5555));
        return $data;

    }

    public function addField($name,$title,$hint){
        $param = $this->getVariable($name);
        $column1[] = $this->getText($title,$this->fontstyle+array('width'=>'70','text-align' => 'right'));
        $column1[] = $this->getFieldtext(
            $param,array('text_color' => '#7d7d7d', 'variable' => $this->vars[$name],'value' => $param,
                      'hint' => $hint,'margin' => '0 30 0 10','padding' => '3 5 3 5',
            'border-width' => '1','border-radius' => '8',"height"=>'35',
            'border-color' => '#42bb43','width' => '150',
                      'text-align' => 'left')+$this->fontstyle
                );
        $output = $this->getRow($column1,array('margin' => '0 10 5 30'));

        return $output;
    }

    public function addField_imate($name,$title,$hint,$val=false){
        $param = $this->getVariable($name);
        $column1[] = $this->getText($title,array('width'=>'120','text-align' => 'left','font-weight' => 'bold')+$this->fontstyle);

        $value = ($val == false) ? $param : $val;
        $column1[] = $this->getFieldtext(
            $value,array('text_color' => '#7d7d7d', 'variable' => $this->vars[$name],'value' => $param,
                'hint' => $hint,'margin' => '0 30 0 10','padding' => '3 5 3 5',
                "height"=>'35',
                'width' => '150',
                'text-align' => 'left')+$this->fontstyle
        );
        $output = $this->getRow($column1,array('margin' => '0 10 5 30'));

        return $output;
    }


    public function addTextarea_imate($name,$title,$hint,$val=false){
        $param = $this->getVariable($name);
        $column1[] = $this->getText($title,array('text-align' => 'center','padding'=> '10 0 0 0', 'vertical-align' => 'top','font-weight' => 'bold')+$this->fontstyle);

        $value = ($val == false) ? $param : $val;
        $column1[] = $this->getFieldtextarea(
            $value,array('text_color' => '#7d7d7d', 'variable' => $this->vars[$name],'value' => $param,
                'hint' => $hint,'margin' => '0 30 0 10','padding' => '3 5 3 5',
                "height"=>'70',
                'width' => '150',
                'text-align' => 'left')+$this->fontstyle
        );
        $output = $this->getColumn($column1,array('margin' => '8 30 5 30','border-color' => '#e4e4e4','border-width' => 1,'border-radius' => '8'));

        return $output;
    }




    public function imateInvite(){
        $this->fontstyle = array(
            'font-style' => 'normal',
            'font-size' => '14',
            'text-align' => 'center'
        );

        $data = new StdClass();


/*        if(isset($this->menuid) AND $this->menuid == '5555') {
            $this->saveVariables();
        } elseif(stristr($this->menuid,'distance_')){
            $this->saveVariables();
        }*/

        if(isset($this->menuid) AND $this->menuid == '5555') {
            $output[] = $this->getSpacer(150);
            $output[] = $this->getText('{#message_sent#}.', $this->fontstyle);
        } else {

            $titlestyle['width'] = '60%';
            //$output[] = $this->getText('Contact',array('style' => 'settings_title'));

            // $columns[] = $this->getImage('actionimage1',array('actionimage' => true,'width' => '10%'));

            $output[] = $this->addField_imate('invite_from', '{#from#}:', '{#your_real_name#}', $this->getSavedVariable('real_name'));
            $output[] = $this->addField_imate('invite_subject', '{#subject#}:', '{#message_subject#}');
            $output[] = $this->addTextarea_imate('invite_msg', '{#phone_numbers_separate_with_comma#}', '+358505002232,
+359882430311');
            $output[] = $this->addTextarea_imate('invite_msg', '{#emails_separate_with_comma#}', 'timo@appzio.com,
branimir@appzio.com');
            $output[] = $this->addTextarea_imate('invite_msg', '{#message#}', '{#my_message#}');

            //$output[] = $this->getFacebookRegisterOrInvite();

            if (isset($this->menuid) AND $this->menuid == '5555') {
                $this->saveVariables();
            }

            //$output[] = $this->getImagebutton('save-button.png',5555,false,array('margin' => '15 0 0 0','sync_upload' => 1));

            $output[] = $this->getText('{#note_your_information_needs_to_be_validated_before_invitations_are_sent#}.', $this->fontstyle);
        }

        if(isset($this->menuid) AND $this->menuid == '5555'){
            $data->footer[] = $this->getTextbutton('OK!',array('id' => 5555,'action' => 'complete-action'));
        } else {
            $data->footer[] = $this->getTextbutton('{#send#}',array('id' => 5555));
        }

        $data->scroll = $output;

        $this->data = $data;
        return true;
    }

}