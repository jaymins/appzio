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

class CookifyhomeController extends ArticleController {


/*    public $tabsmenu_images = array(
        '1' => array('leaderboard.png','50%'),
        '2' => array('categories.png','50%'),

    );*/

    public $toplist;
    public $special;
    public $titlestyle = array(
        'style' => 'toplist_title'
    );



    public function tab1(){

        $data = new StdClass();
        $output[] = $this->getSwipearea($this->getActionsSwipe());
        $this->loadBranchList();

        if(isset($this->available_branches[$this->configobj->my_cookify_branch])){
            $mycookifyid = $this->getConfigParam('my_cookify_branch');
        } elseif(isset($this->available_branches[$this->configobj->create_profile_branch])) {
            $mycookifyid = $this->requireConfigParam('create_profile_branch');
        } else {
            $this->setError('Cookify home action is not configured');
            $data->scroll = $output;
            return $data;
        }

        /* make sure branch is available */
        if(isset($this->available_branches[$this->configobj->inspiration_branch])){
            $output[] = $this->getImagebutton(
                'inspiration.png',$this->configobj->inspiration_branch,false,
                array('margin' => '30 0 0 0','action' => 'open-branch','config' => $this->configobj->inspiration_branch));
        } else {
            $this->setError('Cookify home action is not configured');
        }

        $output[] = $this->getImagebutton(
            'mycookify.png',$mycookifyid,false,
            array('margin' => '30 0 0 0','action' => 'open-branch','config' => $mycookifyid));
        
        if($this->rerun_list_branches){
            $menu = new StdClass();
            $menu->action = 'list-branches';
            $data->onload[] = $menu;
        }

        $data->scroll = $output;
        return $data;
    }

    private function publishNewRecipe($date,$recursion=false){
        $action = Aeaction::model()->findByAttributes(array('branch_id' => $this->configobj->publishing_queue),array('order' => '`order`'));
        $conf = @json_decode($action->config);

        if(is_object($action) AND is_object($conf)){
            $action = $this->rewriteConfigs($action,$conf);
            $action->update();
            return $action;
        } elseif($recursion==false) {

            $action = Aeaction::model()->findByPk($this->action_id);
            $conf = json_decode($action->config);
            /* we will switch these */
            $oldrecipes = $conf->old_recipes;
            $que = $conf->publishing_queue;

            $conf->publishing_queue = $oldrecipes;
            $conf->old_recipes = $que;
            $action->config = json_encode($conf);
            $action->update();
            return $this->publishNewRecipe($date,true);
        }

        return false;
    }

    /* this will make sure that the action has all required configs set */
    private function rewriteConfigs($action,$config){
        $conf_params = array(
            'chat_content' => '',
            'user_images' => '',
            'bookmarks' => 3,
            'author' => $this->getConfigParam('admin_user_id')
        );

        $conf_params_override = array(
            'backarrow' => 1,
            'dynamic' => 1,
            'gallery_item_id' => $this->getConfigParam('gallery_item_id'),
            'publish_day' => (date('z') + 1) .date('Y'),
        );

        $trigger = Aetrigger::model()->findByAttributes(array('shortname' => 'branchactive'));

        $params = array(
            'trigger_id' => $trigger->id,
            'branch_id' => $this->requireConfigParam('recipe_source','Cookify home not configured'),
        );

        /* override */
        foreach($params as $key => $value){
            $action->$key = $value;
        }

        foreach($conf_params_override AS $key => $value){
            $config->$key = $value;
        }

        /* set if not found */
        foreach($conf_params AS $key => $value){
            if(!isset($config->$key)){
                $config->$key = $value;
            }
        }

        $action->config = json_encode($config);
        return $action;

    }


    private function moveRecipeToOlds($id){
        $action = Aeaction::model()->findByPk($id);
        if(is_object($action)){
            $action->branch_id = $this->requireConfigParam('old_recipes','Cookify home not configured');
            $action->update();
        }
    }

    private function handlePublishing(){

        $actions = Aeaction::model()->findAllByAttributes(array('branch_id' => $this->configobj->recipe_source),array('order' => '`order`'));

        $date = (date('z') + 1) .date('Y');
        $updatebranches = false;
        $output = array();
        $count = 0;

        foreach($actions as $action) {
            $id = $action->id;
            $conf = @json_decode($action->config);
            if (is_object($conf)) {
                if (isset($conf->publish_day) AND $conf->publish_day == $date) {
                    $output[] = $action;
                } elseif (!isset($conf->publish_day) OR !$conf->publish_day) {
                    // not set
                    $conf->publish_day = $date;
                    $act = Aeaction::model()->findByPk($id);
                    $act->config = json_encode($conf);
                    $act->update();
                    $output[] = $action;
                } else {
                    $this->moveRecipeToOlds($id);
                    $newaction = $this->publishNewRecipe($date);

                    if (is_object($newaction)) {
                        $output[] = $newaction;
                    }

                    $updatebranches = true;
                }
            }

            $count++;
        }

        if($count < $this->configobj->number_of_recipes_day){

            while($count < $this->configobj->number_of_recipes_day){
                $newaction = $this->publishNewRecipe($date);
                $updatebranches = true;
                $output[] = $newaction;
                $count++;
            }
        }

        if($updatebranches){
            Aeaction::renumberAllBranchActions($this->configobj->recipe_source);
            Aeaction::renumberAllBranchActions($this->configobj->publishing_queue);
            Aeaction::renumberAllBranchActions($this->configobj->old_recipes);
            Appcaching::flushAppCache($this->gid);
            $this->rerun_list_branches = true;
            $this->runLogic();
        }

        return $output;

    }

    public function tab2(){
        $data = new StdClass();
        $output[] = $this->getText('Error fetching toplists');

        $data->scroll = $output;

        return $data;
    }

    private function getActionsSwipe(){

        if(!isset($this->configobj->recipe_source)){
            $out[] = $this->getError('Application configuration missing');
            return $out;
        }

        $output = $this->setActions();
        return $output;
    }


    private function setActions(){
        $actions = $this->handlePublishing();

        $items = array();

        $textstyle = array(
            'font-size'=> '12',
            'color' => '#ffffff',
            'padding' => '4 4 4 4',
        );

        $totalcount = count($actions);
        $count = 1;

        foreach($actions as $action){
            if ( !isset($action->id) ) {
                continue;
            }

            $id = $action->id;

            $conf = @json_decode($action->config);

            if ( !is_object($conf) OR !isset($conf->image_portrait) ) {
                continue;
            }

            $row[] = $this->getImagebutton(
                $conf->image_portrait, 29293239 + $count, false,
                array(
                    'margin' => '0 0 0 0',
                    'action' => 'open-action',
                    'config' => $id,
                    'width' => '100%',
                    'branchid' => '666666'
                )
            );

            $row[] = $this->getSwipeNavi($totalcount,$count,array('navicolor' => 'black','text-align'=> 'center','margin' => '-78 0 0 0','height' => 40));

            $bottom[] = $this->getText($conf->subject,$textstyle+array('width' => '73%','strlen' => 35,'margin' => '0 0 0 5'));
            $bottom[] = $this->getImage('timer.png',array('width' => '10%','margin' => '0 0 0 0','priority' => 1));
            $bottom[] = $this->getText($this->getConfigParam('time') .'min',$textstyle+array('text-align' => 'left'));
            $row[] = $this->getRow($bottom,array('margin' => '-12 0 0 0','background-color' => '#59000000','height' => 50,'text-align' => 'center','vertical-align' => 'middle'));
            $data[] = $this->getColumn($row,array('width'=>'100%','vertical-align' => 'middle'));

            unset($row);
            unset($bottom);
            $count++;

            $items[] = $this->getColumn($data,array('width'=>'100%'));
            unset($data);
        }

        return $items;
    }

}