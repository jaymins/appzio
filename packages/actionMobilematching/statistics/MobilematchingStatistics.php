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
Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

class MobilematchingStatistics
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

    public $statisticsmodelobj;
	
	
    public function render(){

        $this->doInit();
        $output = 'Hello World!';
        return $output;
    }

    public function doInit(){
        $this->baseurl = Yii::app()->Controller->getDomain() .'/' .Yii::app()->i18n->getCurrentLang() .'/aegameauthor/extension/show?ext=mobilematching&tab=tab_statistics&type=statistics';
        $game = Aegame::model()->findByPk($this->gid);
        $this->adminuserid = $game->user_id;
        $this->statisticsmodelobj = new StatisticsModel();
        $this->statisticsmodelobj->gid = $this->gid;

    }


}