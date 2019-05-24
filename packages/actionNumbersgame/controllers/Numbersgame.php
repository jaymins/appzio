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

class Numbersgame extends ActivationEngineAction {

     public function disableScripts(){
        return array('disableBootstrap' => false, 'disableDefaultCss' => false, 'disableJquery' => false);
    }

    public function render(){

        $this->init();
        $this->output = '';

		 ///colors
		

        /* load css */
        $csspath = Yii::getPathOfAlias('application.modules.aelogic.packages.actionNumbersgame.css');
        $assetUrl1 = Yii::app()->getAssetManager()->publish($csspath .'/main.scss');
        $assetUrl2 = Yii::app()->getAssetManager()->publish($csspath .'/helpers.scss');
        $assetUrl3 = Yii::app()->getAssetManager()->publish($csspath .'/fonts/clear-sans.css');

        Yii::app()->clientScript->registerCssFile($assetUrl1);
        Yii::app()->clientScript->registerCssFile($assetUrl2);
        Yii::app()->clientScript->registerCssFile($assetUrl3);

        $this->loadJs();

        Yii::app()->clientScript->registerMetaTag('apple-mobile-web-app-capable', 'yes');
        Yii::app()->clientScript->registerMetaTag('apple-mobile-web-app-status-bar-style', 'black');
        Yii::app()->clientScript->registerMetaTag('HandheldFriendly', 'True');
        Yii::app()->clientScript->registerMetaTag('viewport', 'width=device-width, target-densitydpi=160dpi, initial-scale=1.0, maximum-scale=1, user-scalable=no, minimal-ui');

        if (isset ($this->configdata->msg)) {$msg=$this->configdata->msg;} else { $msg = ''; }

        /*  render using mustache.  */
        $path = '../modules/aelogic/packages/actionNumbersgame/templates/';
        $this->output = Yii::app()->mustache->GetRender($path .'Numbersgame',array('form' => '','msg' => $msg));
        $this->output .= '<br><br><div style="width:100%;text-align:center;">' .$this->donebtn .'</div>';

        return $this->output;
    }

    private function loadJs(){
        /* load js */
        $jspath = Yii::getPathOfAlias('application.modules.aelogic.packages.actionNumbersgame.js');

        if ($handle = opendir($jspath)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $assetUrl = Yii::app()->getAssetManager()->publish($jspath .'/' .$entry);
                    Yii::app()->clientScript->registerScriptFile($assetUrl);

                }
            }
            closedir($handle);
        }
    }
    

}

?>

