<?php

/*

action within a module is denoted with a variable which is set either
on request or by form post

*/

Yii::import('application.modules.aelogic.packages.*');
Yii::import('application.modules.aelogic.packages.actionPhotostream.widgets.*');

class Photostream extends ActivationEngineAction {

    public $validationError = '';
    public $datastorage = 'photostream';
    public $templatepath = '../modules/aelogic/packages/actionPhotostream/templates/';
    public $uploadpath;
    public $baseurl;

    // objects
    public $photostreammain;
    public $photostreamwidget;
    public $playaction;

    // a stands for action ...

	 public function disableScripts(){
        return array('disableBootstrap' => true, 'disableDefaultCss' => true, 'disableJquery' => false);
    }

    public function render(){
        parent::init();

		 ///colors
		$this->setColors();

        $this->uploadpath = '/games/' .$this->gameid .'/userupload/useractions/' .$this->usertaskid .'/';
        $this->baseurl = Yii::app()->Controller->getDomain() .'/' .Yii::app()->i18n->getCurrentLang() .'/aeplay/home/showtask?ptid=' .$this->ptid .'&token=' .$this->token;

        $this->initobjects();

        if(isset($_REQUEST['PhotostreamModel']['a'])){
            $a = $_REQUEST['PhotostreamModel']['a'];
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
       // $this->photostreammain = PhotostreamModel::model()->findByAttributes(array('playtask_id'=>$this->ptid));

        /*if(!is_object($this->photostreammain)){
            $this->photostreammain = New PhotostreamModel();
            $this->photostreammain->playtask_id = $this->ptid;
            $this->photostreammain->date = date('Y-m-d H:i:s');
            $this->photostreammain->insert();
        }*/

        //$this->photostreamposts = PhotostreamPostModel::getPosts($this->taskid);

        /* html widget is used for drawing forms and rendering views
        */

        $this->photostreamwidget = new PhotostreamWidget();
        $this->photostreamwidget->actiondata = $this->actiondata;
        $this->photostreamwidget->templatepath = $this->templatepath;
        $this->photostreamwidget->configdata = $this->configdata;
        $this->photostreamwidget->baseurl = $this->baseurl;
        $this->photostreamwidget->uservariables = PhotostreamModel::getUserVariables($this->gameid);
        $this->photostreamwidget->loadData();

        $this->playaction = AeplayAction::model()->findByPk($this->ptid);
    }


    private function deleteEntry(){
        $item = $_REQUEST['item'];
        $obj = PhotostreamModel::model()->findByPk($item, 'user_id = ' .Yii::app()->user->id);
        $this->task->points = $this->task->points - $this->configdata->points_per_post;
        $this->task->update();
        $obj->msg = '{%post_deleted%}';
        $obj->update();
        Yii::app()->request->redirect(Yii::app()->Controller->getDomain() .'/' .Yii::app()->i18n->getCurrentLang() .'/aeplay/home/showtask?ptid=' .$this->ptid .'&token=' .$this->token);
        Yii::app()->end();
    }


    private function fileEmbed($file){
        $type = $_FILES['PhotostreamModel']['type']['file'];

        if(stristr($type,'image')){
            return "<img src='$file'>";
        } elseif(stristr($type,'video')){
            return "<video id='Movie' src='$file' controls></video>";
        } else {
            return '<a href="'.$file.'" style="background:#'.$this->color_btn.';color:#'.$this->color_btn_text.';">{%download_file%}</a>';
        }
    }


    private function addReply(){
       
        $model = new PhotostreamModel();
        $model->date = date('Y-m-d H:i:s');
        $model->parent_id = $_REQUEST['PhotostreamModel']['parent_id'];
        $model->user_id = Yii::app()->user->id;
        $model->playtask_id = $this->usertaskid;

        $serial = $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
        $serial = $serial .'.jpg';

        if(!empty($_FILES['PhotostreamModel']['tmp_name']['file'])){
            $f = Helper::doGeneralUpload($_FILES['PhotostreamModel']['tmp_name']['file'],$serial,$this->uploadpath);
            $model->msg = $_REQUEST['PhotostreamModel']['msg'] .$this->fileEmbed($f);
        } else {
            $model->msg = $_REQUEST['PhotostreamModel']['msg'];
        }

        $model->insert();
        $raw_content = strip_tags($_REQUEST['PhotostreamModel']['msg']);
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
        $model = new PhotostreamModel();

/*        if($model->checkPermissions($this->ptid) == false){
            Yii::app()->user->setFlash('error', '<strong>{%unknown_error%}!</strong> ');
            Yii::app()->request->redirect(Yii::app()->Controller->getDomain() .'/' .Yii::app()->i18n->getCurrentLang() .'/aeplay/public/showgame?gid=' .$this->gameid);
            Yii::app()->end();
        }*/
       
        $model->playtask_id = $this->ptid;
        $model->user_id = Yii::app()->user->id;
        $model->playtask_id = $this->usertaskid;
        $model->date = date('Y-m-d H:i:s');
        $serial = $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
        $serial = $serial .'.jpg';

        if(!empty($_FILES['PhotostreamModel']['tmp_name']['file'])){
            $f = Helper::doGeneralUpload($_FILES['PhotostreamModel']['tmp_name']['file'],$serial,$this->uploadpath);
            $model->msg = $this->fileEmbed($f);
        } /*else {
            $model->msg = $_REQUEST['PhotostreamModel']['msg'];
        }*/

        if($model->msg){
            $model->insert();
        }

        // handle points logic
        $raw_content = strip_tags($_REQUEST['PhotostreamModel']['msg']);

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
        $messages = PhotostreamModel::getAllPosts($this->taskid);

        $output = '';


        if(!isset($_REQUEST['a']) OR $_REQUEST['a'] != 'comment'){
            $output .= $this->photostreamwidget->newPostForm();
        }

        $output .= $this->photostreamwidget->showList($messages);

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

        $posts = PhotostreamPostModel::getPosts($this->ptid);
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
        return $this->photostreamwidget->commentBox($id,$actionid);
    }
    
	
    public function skipBtn() {
		
		$output = '<a href="'.$this->skipurl.'" class="btn btn-success" style="background:#'.$this->color_btn.';color:#'.$this->color_btn_text.';">{%skip_action%}</a>';
		return $output;
		}
   

}

?>