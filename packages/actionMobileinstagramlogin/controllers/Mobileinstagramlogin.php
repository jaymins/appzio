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


class Mobileinstagramlogin extends ActivationEngineAction {

	 public function disableScripts(){
        return array('disableBootstrap' => true, 'disableDefaultCss' => true, 'disableJquery' => true);
    }

    /* abf8991ff508405eac4b336d8dd33d70 */

    public function render(){
        parent::init();
        $apikey = Aemobile::getConfigParam($this->gameid,'instagram_apikey');

/*        $appapikey = $this->gamedata->api_key;

        if($apikey){
            $url = Yii::app()->params['siteURLssl'];
            if(substr($url, -1) != '/'){
                $url = $url.'/';
            }
            $url = $url.'api/'.$appapikey.'/social/instagram';
        } else {
            $url = $this->doneurl;
        }*/

        //echo($url);die();

        if(isset($this->configdata->mode) AND $this->configdata->mode == 'logout'){
            header('location:https://www.instagram.com/accounts/logout/');
        } else {
            $apikey = Aemobile::getConfigParam($this->gameid,'instagram_apikey');
            $return = urlencode($this->simpledoneurl);
            header('location:https://api.instagram.com/oauth/authorize/?client_id='.$apikey.'&redirect_uri='.$return.'&response_type=code');
        }

        die();
    }

}