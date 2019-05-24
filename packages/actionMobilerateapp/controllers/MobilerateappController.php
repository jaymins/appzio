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

class MobilerateappController extends ArticleController {

    public $data;

    public function tab1(){

        if($this->getConfigParam('article_action_theme')){
            $theme = $this->getConfigParam('article_action_theme');

            Yii::import('application.modules.aelogic.packages.actionMobilerateapp.themes.'. $theme .'.controllers.*');

            $this->data = new StdClass();
            $this->$theme();

            return $this->data;
        }

    }

}