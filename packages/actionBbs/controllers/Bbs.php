<?php

/*

action within a module is denoted with a variable which is set either
on request or by form post

*/

Yii::import('application.modules.aelogic.packages.*');
Yii::import('application.modules.aelogic.packages.actionBbs.widgets.*');

class Bbs extends ActivationEngineAction {

    public $validationError = '';
    public $datastorage = 'bbs';
    public $templatepath = '../modules/aelogic/packages/actionBbs/templates/';
    public $uploadpath;
    public $baseurl;

    // objects
    public $bbsmain;
    public $bbswidget;
    public $playaction;

    public $userid;

    // a stands for action ...


    public function disableScripts(){
        if(isset($this->configdata->simplified_feed) AND $this->configdata->simplified_feed == 1 AND isset($_SESSION['mobile']) AND $_SESSION['mobile']){
            return array('disableBootstrap' => true, 'disableDefaultCss' => true, 'disableJquery' => true);
        }
    }

    public function render(){
        parent::init();

        // weak authentication here
        if(isset(Yii::app()->user->id) AND is_numeric(Yii::app()->user->id)){
            $this->userid = Yii::app()->user->id;
        } else {
            $this->userid = $this->taskdata->user_id;
        }

        if(isset($this->configdata->simplified_feed) AND $this->configdata->simplified_feed == 1 AND isset($_SESSION['mobile']) AND $_SESSION['mobile']){
            $csspath = Yii::getPathOfAlias('application.modules.aelogic.packages.actionBbs.css');
            $assetUrl = Yii::app()->getAssetManager()->publish($csspath .'/simplified-feed.css');
            Yii::app()->clientScript->registerCssFile($assetUrl);

        }

        $this->uploadpath = '/games/' .$this->gameid .'/userupload/useractions/' .$this->usertaskid .'/';

        if(isset($_SESSION['mobile']) AND $_SESSION['mobile']){
            $this->baseurl = Yii::app()->Controller->getDomain() .'/' .Yii::app()->i18n->getCurrentLang() .'/aeplay/home/showactionforapi?ptid=' .$this->ptid .'&token=' .$this->token;
        } else {
            $this->baseurl = Yii::app()->Controller->getDomain() .'/' .Yii::app()->i18n->getCurrentLang() .'/aeplay/home/showtask?ptid=' .$this->ptid .'&token=' .$this->token;

        }

        $this->initobjects();

        if(isset($_REQUEST['BbsModel']['a'])){
            $a = $_REQUEST['BbsModel']['a'];
        } elseif(isset($_REQUEST['a'])){
            $a = $_REQUEST['a'];
        }

        if(isset($a)){
            switch($a){

                // redactor
                case 'getimages':
                    Helper::returnRedactorJson($this->uploadpath);
                    break;

                // redactor
                case 'upload':
                    Helper::doRedactorUpload($this->uploadpath);
                    break;

                // file upload field
                case 'getimages':
                    $this->uploadpath = '/games/' .$this->gid .'/userupload/useractions/' .$_REQUEST['actionid'] .'/';
                    Helper::returnRedactorJson($this->uploadpath);
                    break;


                case 'addentry':
                    $this->addEntry();
                    break;

                case 'delete':
                    $this->deleteEntry();
                    break;

                case 'savecomment':
                    $this->addReply();
                    break;

                case 'comment':
                    return $this->renderMain();
                    break;

                case 'votecomment':
                    $this->voteComment();
                    break;

                default:
                    return $this->renderMain();
                break;
            }
        } else {
            return $this->renderMain();
        }
    }

    private function initObjects(){
       // $this->bbsmain = BbsModel::model()->findByAttributes(array('playtask_id'=>$this->ptid));

        /*if(!is_object($this->bbsmain)){
            $this->bbsmain = New BbsModel();
            $this->bbsmain->playtask_id = $this->ptid;
            $this->bbsmain->date = date('Y-m-d H:i:s');
            $this->bbsmain->insert();
        }*/

        //$this->bbsposts = BbsPostModel::getPosts($this->taskid);

        /* html widget is used for drawing forms and rendering views
        */
         if(isset($this->configdata->simplified_feed) AND $this->configdata->simplified_feed == 1 AND isset($_SESSION['mobile']) AND  $_SESSION['mobile']){
           $this->bbswidget = new BbssimplifiedWidget();
		 } else {
		    $this->bbswidget = new BbsWidget();
		}
		
        $this->bbswidget->actiondata = $this->actiondata;
        $this->bbswidget->templatepath = $this->templatepath;
        $this->bbswidget->configdata = $this->configdata;
        $this->bbswidget->baseurl = $this->baseurl;
        $this->bbswidget->uservariables = BbsModel::getUserVariables($this->gameid);
        if(isset($this->configdata->simplified_feed)){
            $this->bbswidget->simplified_feed = $this->configdata->simplified_feed;
        }else {
            $this->bbswidget->simplified_feed = false;

        }

        if(isset($this->colors['codes']) AND $this->colors['codes']) {
			
		    $this->bbswidget->color_bckg = $this->colors['codes']['background_color'];
			$this->bbswidget->color_text_main = $this->colors['codes']['text_color'];
            $this->bbswidget->color_bg = $this->colors['codes']['top_bar_color'];
            $this->bbswidget->color_text = $this->colors['codes']['top_bar_text_color'];
            $this->bbswidget->color_btn = $this->colors['codes']['button_color'];
            $this->bbswidget->color_btn_text = $this->colors['codes']['button_text_color'];
			$this->bbswidget->color_btn_icon = $this->colors['codes']['button_icon_color'];
        }


        $this->bbswidget->loadData();

        $this->playaction = AeplayAction::model()->findByPk($this->ptid);
    }


    private function deleteEntry(){
        $item = $_REQUEST['item'];
        $obj = BbsModel::model()->findByPk($item, 'user_id = ' .$this->userid);
        $this->task->points = $this->task->points - $this->configdata->points_per_post;
        $this->task->update();
		if ( $obj ) {
		        $obj->msg = '{%post_deleted%}';
                $obj->update();
		}
        Yii::app()->request->redirect($this->baseurl);
        Yii::app()->end();
    }


    private function fileEmbed($file){
        $type = $_FILES['BbsModel']['type']['file'];

        if(stristr($type,'image')){
            return "<img src='$file'>";
        } elseif(stristr($type,'video')){
            return "<video id='Movie' src='$file' controls></video>";
        } else {
            return "<a href='$file'>Download File</a>";
        }
    }


    private function addReply(){
       
        $model = new BbsModel();
        $model->date = date('Y-m-d H:i:s');
        $model->parent_id = $_REQUEST['BbsModel']['parent_id'];
        $model->user_id = $this->userid;
        $model->playtask_id = $this->usertaskid;

        $serial = $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
        $serial = $serial .'.jpg';

        if(!empty($_FILES['BbsModel']['tmp_name']['file'])){
            $f = Helper::doGeneralUpload($_FILES['BbsModel']['tmp_name']['file'],$serial,$this->uploadpath);
            $model->msg = $_REQUEST['BbsModel']['msg'] .$this->fileEmbed($f);
        } else {
            $model->msg = $_REQUEST['BbsModel']['msg'];
        }

        $model->insert();
        $raw_content = strip_tags($_REQUEST['BbsModel']['msg']);
        // award points on posting
        if($this->configdata->points_per_reply > 0
            AND $this->configdata->max_points > $this->task->points
            AND $this->configdata->minimum_post_lenght_for_points < $raw_content){
            $this->task->points = $this->task->points + $this->configdata->points_per_reply;
            $this->task->update();
        }

        // if maximum number of points is gathered, we will mark the action done
        if($this->task->points >= $this->configdata->complete_on_points){
            AeplayAction::taskDone($this->ptid);
            $this->gotoActionList();
        }

        // update flags
        $model->updateFlags();

        Yii::app()->request->redirect($this->baseurl);
        Yii::app()->end();
    }



    private function addEntry(){
        $model = new BbsModel();

/*        if($model->checkPermissions($this->ptid) == false){
            Yii::app()->user->setFlash('error', '<strong>{%unknown_error%}!</strong> ');
            Yii::app()->request->redirect(Yii::app()->Controller->getDomain() .'/' .Yii::app()->i18n->getCurrentLang() .'/aeplay/public/showgame?gid=' .$this->gameid);
            Yii::app()->end();
        }*/
       
        $model->playtask_id = $this->ptid;
        $model->user_id = $this->userid;
        $model->playtask_id = $this->usertaskid;
        $model->date = date('Y-m-d H:i:s');
        $serial = $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
        $serial = $serial .'.jpg';

        if(!empty($_FILES['BbsModel']['tmp_name']['file'])){
            $f = Helper::doGeneralUpload($_FILES['BbsModel']['tmp_name']['file'],$serial,$this->uploadpath);
            $model->msg = $_REQUEST['BbsModel']['msg'] .$this->fileEmbed($f);
        } else {
            $model->msg = $_REQUEST['BbsModel']['msg'];
        }

        if($model->msg){
            $model->insert();
        }

        // handle points logic
        $raw_content = strip_tags($_REQUEST['BbsModel']['msg']);

        // award points on posting
        if($this->configdata->points_per_post > 0
            AND $this->configdata->max_points > $this->task->points
            AND $this->configdata->minimum_post_lenght_for_points < $raw_content){
                $this->task->points = $this->task->points + $this->configdata->points_per_post;
                $this->task->update();
        }

        // if maximum number of points is gathered, we will mark the action done
        if($this->task->points >= $this->configdata->complete_on_points){
            AeplayAction::taskDone($this->ptid);
            $this->gotoActionList();
        }

        // update flags
        $model->updateFlags();

        Yii::app()->request->redirect($this->baseurl);
        Yii::app()->end();
    }

    private function renderMain(){
        $messages = BbsModel::getAllPosts($this->taskid);

        $output = '';
		

       if(isset($this->configdata->simplified_feed) AND $this->configdata->simplified_feed == 1 AND isset($_SESSION['mobile']) AND  $_SESSION['mobile']){
        $output .= $this->bbswidget->showList($messages);
        }
		
		if(!isset($_REQUEST['a']) OR $_REQUEST['a'] != 'comment'){
            $output .= $this->bbswidget->newPostForm();
        }

        if(!isset($this->configdata->simplified_feed) OR $this->configdata->simplified_feed == 0 ){
        $output .= $this->bbswidget->showList($messages);
        }

        $this->playaction->newcount = 0;
        $this->playaction->update();
		
		
        
		/// if skip action is active show button
		if (isset($this->configdata->skip_action_posibility) && ($this->configdata->skip_action_posibility==1)) {
		  $output .='<br/><br/>';
		  $output .= $this->skipBtn();
		}
		
        return $output;
    }


    private function renderPost($data,$commentbox=false){
        $output = Yii::app()->mustache->GetRender($this->templatepath .'entry',array(
                'data'=>$data,
                'allow_delete' => $this->configdata->allow_delete,
                'baseurl' => $this->baseurl,
                'deleteurl' => $this->baseurl .'&a=delete',
            ));

        return $output;

    }

    private function getEntries(){

        $posts = BbsPostModel::getPosts($this->ptid);
        $data = array();
        $counter = 0;
        $posttracker = array();


        $output = '<div class="mainposts">';

        while($entry = each($posts)){

            $entry = $entry[1];
            $entryid = $entry['id'];

            if(isset($_REQUEST['a']) AND $_REQUEST['a'] == 'comment' AND isset($_REQUEST['id']) AND $_REQUEST['id'] == $entry['entryid']){
                $commentbox = $this->commentBox($entry['entryid'],$this->ptid);
            } else {
                $commentbox = false;
            }

            if($entry['reply']){
                $output .= $this->renderPost($entry,$commentbox);
            } elseif(!isset($posttracker[$entryid])) {
                $posttracker[$entryid] = true;
                $output .= '<div class="diaryentry" class="diaryentry">';
                $output .= $this->renderPost($entry,$commentbox);
                $output .= '</div>';
            }

        }

        $output .= '</div>';
        return $output;

    }


    public function commentBox($id,$actionid){
        return $this->bbswidget->commentBox($id,$actionid);
    }
    
	
    public function skipBtn() {
		
		$output = '<a href="'.$this->skipurl.'" class="btn btn-success">{%skip_action%}</a>';
		return $output;
		}

   public function showBrief(){
        return $this->bbswidget->commentBox($id,$actionid);
    }
   

}

?>