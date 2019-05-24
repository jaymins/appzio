<?php

/**
 * This is the model class for table "ae_game".
 *
 * The followings are the available columns in table 'ae_game':
 * @property string $id
 * @property string $user_id
 * @property string $category_id
 * @property string $name
 * @property integer $active
 * @property string $icon
 * @property string $length
 * @property integer $timelimit
 * @property string $description
 *
 * The followings are the available model relations:
 * @property User $user
 * @property AeCategory $category
 * @property AeGameRole[] $aeGameRoles
 * @property AeGameUser[] $aeGameUsers
 * @property Aebranch[] $aeLevels
 */
Yii::import('application.modules.aelogic.packages.*');
Yii::import('application.modules.aelogic.packages.actionBbs.models.*');
Yii::import('application.modules.aelogic.packages.actionBbs.widgets.*');

class BbsStatistics 
{

    public $templatepath = '../modules/aelogic/packages/actionBbs/templates/';
    public $baseurl;
    public $uploadpath;
	public $actiondata;
	public $configdata;

    public $gid;    // game id, set by the game author
    public $aid;    // action id

    public $adminuserid;

      // objects
    public $bbsmain;
    public $bbswidget;
	
	
    public function render(){

        $this->baseurl = Yii::app()->Controller->getDomain() .'/' .Yii::app()->i18n->getCurrentLang() .'/aegameauthor/extension/show?ext=bbs&tab=tab_statistics&type=statistics';

        $game = Aegame::model()->findByPk($this->gid);
        $this->adminuserid = $game->user_id;

        $this->initobjects();


        if(isset($_REQUEST['BbsModel']['a2']) AND $_REQUEST['BbsModel']['a2'] == 'savecomment'){
			
            $this->addReply();
        } elseif(isset($_REQUEST['a'])){

            switch($_REQUEST['a']){
                case 'delete':
                    $this->deleteentry();
					$queryStr='';
		            if (isset($_REQUEST['ai'])) {
			               $queryStr='&ai='.$_REQUEST['ai'];
		            }
      
                    Yii::app()->request->redirect($this->baseurl.$queryStr);
                    Yii::app()->end();


                    break;

                case 'comment':
                    break;

                case 'savecomment':
                    $this->addReply();
                    break;

                case 'getimages':
                    $this->uploadpath = '/games/' .$this->gid .'/userupload/useractions/' .$_REQUEST['actionid'] .'/';
                    Helper::returnRedactorJson($this->uploadpath);
                    break;

                case 'upload':
                    $this->uploadpath = '/games/' .$this->gid .'/userupload/useractions/' .$_REQUEST['actionid'] .'/';

                    Helper::doRedactorUpload($this->uploadpath);
                    break;

                default:
                    break;
            }
        }

       // $output = $this->mainview();
	    if (isset($_REQUEST['ai'])) {
		      $output = $this->renderMain($_REQUEST['ai']);
		} else {
	          $output = $this->actionsview();
		}
        $path = Yii::getPathOfAlias('application.modules.aelogic.packages.actionBbs');
        $assetUrl = Yii::app()->getAssetManager()->publish($path .'/css/Bbs.css');
        Yii::app()->clientScript->registerCssFile($assetUrl);
        return $output;

    }

	
	private function addReply(){
		
        $model = new BbsModel();
        $model->date = date('Y-m-d H:i:s');
        $model->parent_id = $_REQUEST['BbsModel']['parent_id'];
        $model->user_id = Yii::app()->user->id;
        $model->playtask_id = $_REQUEST['ptid'];
        $model->admin_comment = 1;

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

		$queryStr='';
		if (isset($_REQUEST['ai'])) {
			$queryStr='&ai='.$_REQUEST['ai'];
		}

        // update flags
        $model->updateFlags();

        Yii::app()->request->redirect($this->baseurl.$queryStr);
        Yii::app()->end();
    }



    private function deleteEntry(){
        $item = $_REQUEST['item'];
        $obj = BbsModel::model()->findByPk($item);
        $playaction = AeplayAction::model()->findByPk($obj->playtask_id);
        $action = Aeaction::model()->findByPk($playaction->action_id);
        $config = json_decode($action->config);

        if($obj->parent_id > 0){
            $playaction->points = $playaction->points - $config->points_per_reply;
        } else {
            $playaction->points = $playaction->points - $config->points_per_post;
        }

        $playaction->update();
        $obj->msg = '{%post_deleted%}';
        $obj->update();
    }




    public function actionsview() {
		
		
        $model = BbsModel::getAllBbsActions($this->gid);
        $data = array();
        $count = 0;

        while($entry = each($model)){
            $entry = $entry[1];

            $data[$count] = array(
                'actiontitle' => $entry['actiontitle'],
                'date' => $entry['added'],
                'pgid' =>  $entry['play_id'],
				'ptid' =>  $entry['id'],
                'deleteurl' => '/en/aegameauthor/extension/show?ext=bbs&tab=tab_statistics&type=statistics&a=delete',
                'ai' => $entry['action_id'],
                'baseurl' => $this->baseurl
            );
            $count++;
        }


        $output = Yii::app()->mustache->GetRender($this->templatepath .'stats-listactions',array(
                'data'=>$data
            ));

        return $output;
		
		
		
   }



	
	
	  private function renderMain($ai){
        $messages = BbsModel::getAllPosts($ai);
		
		  if(!isset($_REQUEST['a']) OR $_REQUEST['a'] != 'comment'){
            $output = $this->bbswidget->newPostForm();
        }

        $output = $this->bbswidget->showList($messages);
        return $output;
    }
	
	
	
	/* public function commentBox($id,$actionid){
        return $this->bbswidget->commentBox($id,$actionid);
    } */
    
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

        $this->bbswidget = new BbsWidget();
        $this->bbswidget->actiondata = $this->actiondata;
        $this->bbswidget->templatepath = $this->templatepath;
		$this->bbswidget->configdata = $this->configdata;
        $this->bbswidget->adminuserid = $this->adminuserid;
		
        
		
        $this->bbswidget->baseurl = $this->baseurl;
        $this->bbswidget->admin = true;
        $this->bbswidget->uservariables = BbsModel::getUserVariables($this->gid);
        $this->bbswidget->loadData();
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


}