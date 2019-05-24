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

Yii::import('application.modules.aelogic.packages.actionMobilelocation.models.*');


class MobilelanguageController extends ArticleController {

    public $toplist;
    public $special;
    public $data;


    public function tab1(){

        $this->data = new StdClass();

        $languages = explode(',',$this->mobilesettings->languages);
        if(!is_array($languages) OR empty($languages)){
            $this->data->scroll[] = $this->getText('Language configuration missing!',array('style' => 'language-title'));
            return $this->data;
        }

        if(strstr($this->menuid,'lang_')){
            $code = str_replace('lang_','',$this->menuid);
            $this->saveVariable('user_language',$code);
            $action = new StdClass();
            $action->action = 'complete-action';
            $this->data->onload[] = $action;
            $this->data->scroll[] = $this->getFullPageLoader();
            return $this->data;
        }

        $image = $this->getConfigParam('actionimage1','language-top-logo.png');

        $this->data->scroll[] = $this->getSpacer('20');
        $this->data->scroll[] = $this->getImage($image);
        if($this->getConfigParam('msg')){
            $this->data->scroll[] = $this->getText('{#choose_a_language_to_continue#}',array('style' => 'language-title'));
            $this->data->scroll[] = $this->getSpacer('15');
        }

        foreach($languages as $lang_code){
            $langname = ThirdpartyServices::getLanguageName($lang_code);
            $this->getLangButton($lang_code,$langname);
        }

        return $this->data;
    }

    public function getLangButton($lang_code,$lang_name){
        $onclick = new StdClass();
        $onclick->action = 'submit-form-content';
        $onclick->id = 'lang_'.$lang_code;

        if($this->getConfigParam('show_flags')){
            $col[] = $this->getImage(strtoupper($lang_code).'.png',array('width' => '40','margin' => '0 20 0 20'));
            $col[] = $this->getText($lang_name,array('width' => '100','font-size' => '14'));
            $this->data->scroll[] = $this->getRow($col,array('style' => 'general_language_button','onclick' => $onclick));
        } else {
            $this->data->scroll[] = $this->getText($lang_name,array('style' => 'general_language_button','onclick' => $onclick));
        }


    }

}


?>