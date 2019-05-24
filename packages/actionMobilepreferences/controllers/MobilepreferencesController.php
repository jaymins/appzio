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

class MobilepreferencesController extends ArticleController {

    public $toplist;
    public $special;
    public $data;

    public $error = false;
    public $error_messages = array();

    public $fontstyle = array(
        'font-style' => 'normal',
        'font-size' => '12'
    );

    public function tab1(){

        if ( $this->getConfigParam('article_action_theme') ) {
            $theme = $this->getConfigParam('article_action_theme');

            Yii::import('application.modules.aelogic.packages.actionMobilepreferences.themes.'. $theme .'.controllers.*');

            $method = strtolower($theme);

            $this->data = new StdClass();
            if ( method_exists($this, $method) ) {
                $this->$method();
            }

            return $this->data;
        }

    }

    protected function addField($name,$title,$hint){
        $param = $this->getVariable($name);

        $styles = array(
            'text-color' => '#7d7d7d',
            'variable' => $this->vars[$name],
            'hint' => $hint,
            'margin' => '0 30 0 10',
            'padding' => '3 5 3 5',
            'border-width' => '1',
            'border-radius' => '8',
            'height' => '35',
            'border-color' => '#42bb43',
            'width' => '150',
            'text-align' => 'left'
        );

        $styles = array_merge( $styles, $this->fontstyle );

        $column1[] = $this->getText($title,$this->fontstyle+array('width'=>'70','text-align' => 'right'));
        $column1[] = $this->getFieldtext(
            $param,
            $styles
        );
        $output = $this->getRow($column1,array('margin' => '0 10 5 30'));

        return $output;
    }


    public function addTitle_imate($title){
        $output[] = $this->getText(strtoupper($title),array('style' => 'form-field-section-title'));
        $output[] = $this->getText('',array('height' => '1','background-color' => '#b5b5b5','margin' => '0 0 10 0'));
        return $this->getColumn($output);

    }

    protected function addSlider_imate($title,$variablename,$defaultvalue,$minvalue,$maxvalue,$step){

        $val = $this->getSavedVariable($variablename) ? $this->getSavedVariable($variablename) : $defaultvalue;
        $variableid = $this->getVariableId($variablename);
        
        $col[] = $this->getText(strtoupper($title),array('style' => 'form-field-titletext'));

        $row[] = $this->getRangeslider('', array(
            'variable' => $variableid,
            'min_value' => $minvalue,
            'max_value' => $maxvalue,
            'step' => $step,
            'value' => $val,
            'left_track_color' => '#a4c97f',
            'right_track_color' => '#000000',
            'width' => '70%', 
            'margin' => '0 10 0 15',
            'track_height' => '1',
            'vertical-align' => 'middle'
        ));

        $row[] = $this->getText($val, array( 'style' => 'profile-field-label','variable' => $variableid ));

        $col[] = $this->getRow($row,array('margin' => '15 0 15 0','height' => '30','vertical-align' => 'middle'));
        $col[] = $this->getText('',array('style' => 'form-field-separator'));
        return $this->getColumn($col,array('style' => 'form-field-row'));

    }

    public function getCheckboxImate($varname, $title, $error = false, $params = false){

        $row[] = $this->getText(strtoupper($title), array('style' => 'form-field-textfield-onoff'));

        $row[] = $this->getFieldonoff($this->getSavedVariable($varname),array(
                'value' => $this->getSavedVariable($varname),
                'variable' => $this->getVariableId($varname),
                'margin' => '0 15 9 0',
                'floating' => '1',
                'float' => 'right'
            )
        );

        $columns[] = $this->getRow($row);
        $columns[] = $this->getText('',array('style' => 'form-field-separator'));

        return $this->getColumn($columns, array('style' => 'form-field-row'));
    }

    public function addField_imate($name,$title,$hint,$type=false,$error=false){
        $param = $this->getVariableId($name);
        if($error){
            $content = $this->getSubmittedVariableByName($name);
        } else {
            $content = $this->getSavedVariable($name);
        }
        $col[] = $this->getText(strtoupper($title),array('style' => 'form-field-titletext'));

        if($error){
            $style = 'form-field-textfield';
            $style_separator = 'form-field-separator-error';
        } else {
            $style = 'form-field-textfield';
            $style_separator = 'form-field-separator';
        }

        if($type){
            $col[] = $this->getFieldtext($content,array('variable' => $param,'hint' => $hint,'style' => $style,'input_type' => $type));
        } else {
            $col[] = $this->getFieldtext($content,array('variable' => $param,'hint' => $hint,'style' => $style));
        }

        $col[] = $this->getText('',array('style' => $style_separator));
        return $this->getColumn($col,array('style' => 'form-field-row'));
    }

    protected function getRangeSelector($title = false){
        if ( $title ) {
            $columns[] = $this->getText($title,array('width'=>'120','text-align'=>'left')+$this->fontstyle);
        }

        $range_start = $this->getSavedVariable('filter_range_start') ? $this->getSavedVariable('filter_range_start') : '5 KM';
        $range_end = $this->getSavedVariable('filter_range_end') ? $this->getSavedVariable('filter_range_end') : '55 KM';

        $columns[] = $this->getFieldlist('1;1 KM;2;2 KM;3;3 KM;4;4 KM;5;5 KM;6;6 KM;7;7 KM;8;8 KM;9;9 KM;10;10 KM;11;11 KM;12;12 KM;13;13 KM;14;14 KM;15;15 KM;16;16 KM;17;17 KM;18;18 KM;19;19 KM;20;20 KM;21;21 KM;22;22 KM;23;23 KM;24;24 KM;25;25 KM;26;26 KM;27;27 KM;28;28 KM;29;29 KM;30;30 KM;31;31 KM;32;32 KM;33;33 KM;34;34 KM;35;35 KM;36;36 KM;37;37 KM;38;38 KM;39;39 KM;40;40 KM;41;41 KM;42;42 KM;43;43 KM;44;44 KM;45;45 KM;46;46 KM;47;47 KM;48;48 KM;49;49 KM',
            array('variable' => $this->getVariableId('filter_range_start'),'value'  => $range_start,'style' => 'datepicker'));

        $columns[] = $this->getFieldlist('50;50 KM;51;51 KM;52;52 KM;53;53 KM;54;54 KM;55;55 KM;56;56 KM;57;57 KM;58;58 KM;59;59 KM;60;60 KM;61;61 KM;62;62 KM;63;63 KM;64;64 KM;65;65 KM;66;66 KM;67;67 KM;68;68 KM;69;69 KM;70;70 KM;71;71 KM;72;72 KM;73;73 KM;74;74 KM;75;75 KM;76;76 KM;77;77 KM;78;78 KM;79;79 KM;80;80 KM;81;81 KM;82;82 KM;83;83 KM;84;84 KM;85;85 KM;86;86 KM;87;87 KM;88;88 KM;89;89 KM;90;90 KM;91;91 KM;92;92 KM;93;93 KM;94;94 KM;95;95 KM;96;96 KM;97;97 KM;98;98 KM;99;99 KM',
            array('variable' => $this->getVariableId('filter_range_end'),'value'  => $range_end,'style' => 'datepicker'));

        return $this->getRow($columns,array('style' => 'age_selector'));
    }

    protected function getAgeSelector($title = false){

        $output[] = $this->addSlider_imate('{#min_age#}','filter_age_start','18','18','110','1');
        $output[] = $this->addSlider_imate('{#max_age#}','filter_age_end','110','18','110','1');

        return $this->getColumn($output);
    }

    /*
    * This function would validate the name and email fields
    * returns TRUE if valid, FALSE if invalid
    */
    public function validateCommonVars() {

        $email_var = $this->getVariableId( 'email' );
        $name_var = $this->getVariableId( 'real_name' );

        $validator = new CEmailValidator;
        $validator->checkMX = true;

        if ( empty($this->submitvariables[$email_var]) ) {
            $this->error = true;
            $this->error_messages[] = '{#the_email_field_could_not_be_blank#}';
        } else if ( !$validator->validateValue($this->submitvariables[$email_var]) ) {
            $this->error = true;
            $this->error_messages[] = '{#please_enter_a_valid_email_address#}';
        }

        if ( empty($this->submitvariables[$name_var]) ) {
            $this->error = true;
            $this->error_messages[] = '{#the_name_field_could_not_be_blank#}';
        }

    }

    public function displayErrors() {

        if ( empty($this->error_messages) OR !is_array($this->error_messages) ) {
            return false;
        }

        foreach ($this->error_messages as $message) {
            $this->data->footer[] = $this->getText($message, array('style' => 'register-text-step-2'));
        }

        return true;
    }

    public function doResetting(){
        $this->initMobileMatching();

        if(!$this->getSavedVariable('matches_cleanup')){
            $this->mobilematchingobj->resetUnmatches();
            $this->saveVariable('matches_cleanup',1);
        } else {
            /* these are unmatches that are "helpers" for the current filter set */
            $this->mobilematchingobj->resetAutoUnmatches();
        }
    }

    public function validateAndSave() {

        $this->doResetting();

        if ( $this->menuid != 'save-data' ) {
            return false;
        }

        $this->validateCommonVars();

        if ( empty($this->error) ) {
            $this->saveVariables();
            $this->loadVariableContent(true);
        } else {
            // Display the error messages in the footer section
            $this->displayErrors();
        }

    }

}