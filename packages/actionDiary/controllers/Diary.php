<?php

/*

action within a module is denoted with a variable which is set either
on request or by form post

*/

Yii::import('application.modules.aelogic.packages.*');
Yii::import('application.modules.aelogic.packages.actionDiary.widgets.*');

class Diary extends ActivationEngineAction {

    public $validationError = '';
    public $datastorage = 'diary';
    public $templatepath = '../modules/aelogic/packages/actionDiary/templates/';
    public $uploadpath;
    public $baseurl;

    // objects
    public $diarymain;
    public $diaryidget;

    // a stands for action ...

	 public function disableScripts(){
        if(isset($_SESSION['mobile']) AND $_SESSION['mobile']){
            return array('disableBootstrap' => true, 'disableDefaultCss' => true, 'disableJquery' => true);
        }
    }

    public function render(){
        parent::init();
        $this->uploadpath = '/games/' .$this->gameid .'/userupload/useractions/' .$this->usertaskid .'/';
        $this->baseurl = Yii::app()->Controller->getDomain() .'/' .Yii::app()->i18n->getCurrentLang() .'/aeplay/home/showtask?ptid=' .$this->ptid .'&token=' .$this->token;

        $this->initobjects();

        if(isset($_REQUEST['DiaryModel']['a'])){
            $a = $_REQUEST['DiaryModel']['a'];
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
      

        $this->diarywidget = new DiaryWidget();
        $this->diarywidget->actiondata = $this->actiondata;
        $this->diarywidget->templatepath = $this->templatepath;
        $this->diarywidget->configdata = $this->configdata;
        $this->diarywidget->baseurl = $this->baseurl;
        $this->diarywidget->uservariables = DiaryModel::getUserVariables($this->gameid);
        $this->diarywidget->loadData();
    }


    private function deleteEntry(){
        $item = $_REQUEST['item'];
        $obj = DiaryModel::model()->findByPk($item, 'user_id = ' .Yii::app()->user->id);
        $this->task->points = $this->task->points - $this->configdata->points_per_post;
        $this->task->update();
        $obj->msg = '{%post_deleted%}';
        $obj->update();
        Yii::app()->request->redirect(Yii::app()->Controller->getDomain() .'/' .Yii::app()->i18n->getCurrentLang() .'/aeplay/home/showtask?ptid=' .$this->ptid .'&token=' .$this->token);
        Yii::app()->end();
    }


    private function fileEmbed($file){
        $type = $_FILES['DiaryModel']['type']['file'];

        if(stristr($type,'image')){
            return "<img src='$file'>";
        } elseif(stristr($type,'video')){
            return "<video id='Movie' src='$file' controls></video>";
        } else {
            return "<a href='$file'>Download File</a>";
        }
    }


    private function addReply(){
       
        $model = new DiaryModel();
        $model->date = date('Y-m-d H:i:s');
        $model->parent_id = $_REQUEST['DiaryModel']['parent_id'];
        $model->user_id = Yii::app()->user->id;
        $model->playtask_id = $this->usertaskid;

        $serial = $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
        $serial = $serial .'.jpg';

        if(!empty($_FILES['DiaryModel']['tmp_name']['file'])){
            $f = Helper::doGeneralUpload($_FILES['DiaryModel']['tmp_name']['file'],$serial,$this->uploadpath);
            $model->msg = $_REQUEST['DiaryModel']['msg'] .$this->fileEmbed($f);
        } else {
            $model->msg = $_REQUEST['DiaryModel']['msg'];
        }

        $model->insert();
        $raw_content = strip_tags($_REQUEST['DiaryModel']['msg']);
        // award points on posting
        if(isset($this->configdata->points_per_reply) && $this->configdata->points_per_reply > 0
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

        Yii::app()->request->redirect($this->baseurl);
        Yii::app()->end();
    }



    private function addEntry(){
        $model = new DiaryModel();

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

        if(!empty($_FILES['DiaryModel']['tmp_name']['file'])){
            $f = Helper::doGeneralUpload($_FILES['DiaryModel']['tmp_name']['file'],$serial,$this->uploadpath);
            $model->msg = $_REQUEST['DiaryModel']['msg'] .$this->fileEmbed($f);
        } else {
            $model->msg = $_REQUEST['DiaryModel']['msg'];
        }

        if($model->msg){
            $model->insert();
        }

        // handle points logic
        $raw_content = strip_tags($_REQUEST['DiaryModel']['msg']);

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

        Yii::app()->request->redirect($this->baseurl);
        Yii::app()->end();
    }

    private function renderMain(){
        $messages = DiaryModel::getAllPosts($this->taskid,Yii::app()->user->id);

        $output = '';

        if(!isset($_REQUEST['a']) OR $_REQUEST['a'] != 'comment'){
            $output .= $this->diarywidget->newPostForm();
        }
        
		
        $output .= $this->diarywidget->showList($messages);
	
		
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

        $posts = DiaryPostModel::getPosts($this->ptid);
        $data = array();
        $counter = 0;
        $posttracker = array();


        $output = '<div class="mainposts">';

        while($entry = each($posts)){

            $entry = $entry[1];
            $entryid = $entry['id'];

            if(isset($_REQUEST['a']) AND $_REQUEST['a'] == 'comment' AND isset($_REQUEST['id']) AND $_REQUEST['id'] == $entry['entryid']){
                $commentbox = $this->commentBox($entry['entryid'],$this->ptid,$entry['userid']);
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


    public function commentBox($id,$actionid,$userid){
        return $this->diarywidget->commentBox($id,$actionid,$userid);
    }

   
   public function skipBtn() {
		
		$output = '<a href="'.$this->skipurl.'" class="btn btn-success">{%skip_action%}</a>';
		return $output;
		}
   
   
   
   
}

?>