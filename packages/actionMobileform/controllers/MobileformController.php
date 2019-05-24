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

class MobileformController extends ArticleController {

    public $toplist;
    public $special;
    public $tabsmenu_images = array();
    public $data;


    public function tab1(){
        $this->data = new StdClass();
        $this->data->scroll[] = $this->getSpacer('30');
        $this->data->scroll[] = $this->getText($this->getConfigParam('headertext'),array('style' => 'mobileform_notetext'));
        $this->data->scroll[] = $this->getSpacer('10');

        $count = 1;
        $error = false;

        while($count < 10){
            $ret = $this->addField($count);
            if($ret){
                $error = true;
            }

            $count++;
        }

        $this->data->scroll[] = $this->getSpacer('20');
        $this->data->scroll[] = $this->getTextbutton('Tallenna',array('style' => 'mobileform_button','id' => 'save'));

        if(isset($this->menuid) AND $this->menuid == 'save'){
            if($error === false){
                $this->saveVariables();
                $onload['action'] = 'complete-action';
                $this->data->onload[] = $onload;
            }
        }

        return $this->data;
    }


    /*'config[field1_title]' => array('type'=>'text', 'title'=>'{%title%}','style' => 'background:#c4e5f3'),
    'config[field1_variable]' => HtmlHelpers::VarField($this->gid,'{%delete_this_variable%}','variable1','varediting','background:#c4e5f3','setvar'),
    'config[field1_type]' => array('type'=>'dropdownlist', 'items' => $fieldtype, 'title'=>'{%field_type%}','style' => 'background:#c4e5f3'),
    'config[field1_required]' => array('type'=>'checkbox', 'title'=>'{%is_field_required%}','style' => 'background:#c4e5f3'),*/

    private function addField($count){

        $error = false;

        $title = $this->getConfigParam('field'.$count.'_title');
        $var = $this->getConfigParam('field'.$count.'_variable');
        $type = $this->getConfigParam('field'.$count.'_type');
        $required = $this->getConfigParam('field'.$count.'_required');

        if(isset($this->submitvariables[$var])){
            $value = $this->submitvariables[$var];
        } else {
            $value = '';
        }

        if($title AND $var AND $type){
            switch($type){
                case 'text':
                    $this->data->scroll[] = $this->getFieldtext($value,array('style' => 'mobileform_formfield','hint' => $title,'variable' => $var));
                    break;

                case 'checkbox':
                    $row[] = $this->getFieldonoff($value,array('style' => 'mobileform_checkbox','hint' => $title,'variable' => $var));
                    $row[] = $this->getText($title,array('style' => 'mobileform_checkbox_text'));
                    $this->data->scroll[] = $this->getRow($row);
                    break;

                case 'textarea':
                    $this->data->scroll[] = $this->getFieldtextarea($value,array('style' => 'mobileform_formfield','hint' => $title,'variable' => $var));
                    break;

            }

        }

        if(isset($this->menuid) AND $this->menuid == 'save'){
            if($required == 1){
                if(!isset($this->submitvariables[$var]) OR !$this->submitvariables[$var]){
                    $this->data->scroll[] = $this->getText('Tämä kenttä on pakollinen',array('style' => 'mobileform_error'));
                    $error = true;
                    //$this->data->scroll[] = $this->getText($this->menuid);
                }
            }
        }


        return $error;

    }


}
?>