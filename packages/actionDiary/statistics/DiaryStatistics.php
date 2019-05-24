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
Yii::import('application.modules.aelogic.packages.actionDiary.models.*');
Yii::import('application.modules.aelogic.packages.actionDiary.widgets.*');

class DiaryStatistics 
{


    public $templatepath = '../modules/aelogic/packages/actionDiary/templates/';
    public $baseurl;
    public $uploadpath;
	public $actiondata;
	public $configdata;

    public $gid;    // game id, set by the game author
    public $aid;    // action id


      // objects
    public $diarymain;
    public $diarywidget;
	
	
    public function render(){
        
		
        $this->baseurl = Yii::app()->Controller->getDomain() .'/' .Yii::app()->i18n->getCurrentLang() .'/aegameauthor/extension/show?ext=diary&tab=tab_statistics&type=statistics';
		
		 $this->initobjects();

        if(isset($_REQUEST['DiaryModel']['a2']) AND $_REQUEST['DiaryModel']['a2'] == 'savecomment'){
			
            $this->addReply();
        } elseif(isset($_REQUEST['a'])){

            switch($_REQUEST['a']){
                case 'delete':
                    $this->deleteentry();
					$queryStr='';
		            if (isset($_REQUEST['ai'])) {
			                 $queryStr.='&ai='.$_REQUEST['ai'];
		            }
                    if (isset($_REQUEST['ui'])) {
			                 $queryStr.='&ui='.$_REQUEST['ui'];
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
			
			if (isset($_REQUEST['ui'])) {
				 $output = $this->renderMain($_REQUEST['ai'],$_REQUEST['ui']);
			} else {
				 $output = $this->usersview($_REQUEST['ai']);
			}
	
		} else {
	          $output = $this->actionsview();
		}
        $path = Yii::getPathOfAlias('application.modules.aelogic.packages.actionDiary');
        $assetUrl = Yii::app()->getAssetManager()->publish($path .'/css/Diary.css');
        Yii::app()->clientScript->registerCssFile($assetUrl);
        return $output;

    }

  
	
	
	private function addReply(){
		
        $model = new DiaryModel();
        $model->date = date('Y-m-d H:i:s');
        $model->parent_id = $_REQUEST['DiaryModel']['parent_id'];
        $model->user_id = Yii::app()->user->id;
        $model->playtask_id = $_REQUEST['ptid'];

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
      
        
		$queryStr='';
		if (isset($_REQUEST['ai'])) {
			$queryStr.='&ai='.$_REQUEST['ai'];
		}
        if (isset($_REQUEST['ui'])) {
			$queryStr.='&ui='.$_REQUEST['ui'];
		}

        Yii::app()->request->redirect($this->baseurl.$queryStr);
        Yii::app()->end();
    }

	

    public function deleteentry(){
        if(isset($_REQUEST['item'])){
            $item = $_REQUEST['item'];
            DiaryModel::model()->deleteByPk($item);
/*            $task = AeplayAction::model()->findByPk($_REQUEST['ai']);

            $task->points = $task->points - $this->configdata->points_per_post;
            $task->update();*/
        }

    }

   
	
	
	
 
    public function actionsview() {
		
		
        $model = DiaryModel::getAllDiaryActions($this->gid);
        $data = array();
        $count = 0;

        while($entry = each($model)){
            $entry = $entry[1];

            $data[$count] = array(
                'actiontitle' => $entry['actiontitle'],
                'date' => $entry['added'],
                'pgid' =>  $entry['play_id'],
				'ptid' =>  $entry['id'],
                'deleteurl' => '/en/aegameauthor/extension/show?ext=diary&tab=tab_statistics&type=statistics&a=delete',
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


	 public function usersview($ai) {
		
		
        $model = DiaryModel::getAllUsersInDiaryAction($ai);
        $data = array();
        $count = 0;

        while($entry = each($model)){
            $entry = $entry[1];

            $data[$count] = array(
                'actiontitle' => $entry['actiontitle'],
                'date' => $entry['added'],
                'pgid' =>  $entry['play_id'],
				'ptid' =>  $entry['id'],
                'ai' => $entry['action_id'],
				'userid' => $entry['userid'],
				'username' => $entry['username'],
				'useremail' => $entry['useremail'],
                'baseurl' => $this->baseurl
            );
            $count++;
        }


        $output = Yii::app()->mustache->GetRender($this->templatepath .'stats-listusersinaction',array(
                'data'=>$data
            ));

        return $output;
		
		
		
   }
	
	  private function renderMain($ai,$ui){
        $messages = DiaryModel::getAllPosts($ai,$ui);
		
		  if(!isset($_REQUEST['a']) OR $_REQUEST['a'] != 'comment'){
            $output = $this->diarywidget->newPostForm();
        }

        $output = $this->diarywidget->showList($messages);
        return $output;
    }
	
	
    
     private function initObjects(){
      

        $this->diarywidget = new DiaryWidget();
        $this->diarywidget->actiondata = $this->actiondata;
        $this->diarywidget->templatepath = $this->templatepath;
		
		$this->diarywidget->configdata = $this->configdata;
		
        
		
        $this->diarywidget->baseurl = $this->baseurl;
        $this->diarywidget->uservariables = DiaryModel::getUserVariables($this->gid);
        $this->diarywidget->loadData();
    }
	
}