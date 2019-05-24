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

class ResetgameModel extends ActivationEngineAction {

    public $gid;
    public $playid;
    public $action;

    /* this is a custom function which is called in case action is invisible */
    public function ResetgameLogicMagick(){

        $sql = "DELETE ae_game_play_action FROM ae_game_play_action
                 WHERE ae_game_play_action.play_id = :playId";

        Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':playId' => $this->playid))
            ->query();

        $sql = "DELETE ae_game_play_branch FROM ae_game_play_branch
                 WHERE ae_game_play_branch.play_id = :playId";

        Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':playId' => $this->playid))
            ->query();


        // also clear variables if its defined
        $config = @json_decode($this->action['taskconfig']);

        if(is_object($config)){
            if(isset($config->clear_variables) AND $config->clear_variables == 1) {
                $sql = "DELETE ae_game_play_variable FROM ae_game_play_variable
                 WHERE ae_game_play_variable.play_id = :playId";

                Yii::app()->db
                    ->createCommand($sql)
                    ->bindValues(array(':playId' => $this->playid))
                    ->query();
            }
        }

        $play = Aeplay::model()->findByPk($this->playid);
        $play->level = 1;
        $play->update();

        // this will check whether game has "show branches enabled" and creates the actions for them as needed
		Aegame::iterateBranches($this->playid,$play->game_id);

    }
}