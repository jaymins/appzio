<?php


/* this is the modules own model file,
    which is used by both controller & other components
 */


class BbsModel extends CActiveRecord
{

    public $msg;
    public $subject;
    public $connection;
    public $userid;
    public $datetime;
    public $comment;
    public $coach;
    public $playtask_id;
    public $name;
    public $admin_comment;

    /* this really should thrown an error instead of returning false ... */

    public function init(){

		$this->connection = Yii::app()->db;
		if(!isset($this->userid) AND isset($_SERVER['SERVER_NAME'])){ $this->userid = Yii::app()->user->id; }

	}

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return aegame the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ae_ext_bbs';
	}

    public static function sayboo(){
        echo('boo');
        die();
    }

/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'category_id' => 'Category',
			'name' => 'Name',
			'active' => 'Active',
			'icon' => 'Icon',
			'length' => 'Length',
			'timelimit' => 'Timelimit',
			'description' => '{%description%}',
			'gamename' => '{%name%}',
			'gamecategory' => '{%main_category%}',
            'name' => '{%name%}',
            'maxactions'            => '{%maxactions%}',
            'newheadboard_portrait'          => '{%newheadboard_portrait%}',
            'show_toplist'          => '{%show_toplist%}',
            'register_email'        => '{%register_email%}',
            'register_sms'          => '{%register_sms%}',
            'choose_playername'     => '{%choose_playername%}',
            'choose_avatar'         => '{%choose_avatar%}',
            'home_instructions'     => '{%home_instructions%}',
			'show_logo'             => '{%show_logo%}',
			'show_social'           => '{%show_social%}',
			'show_branches'         => '{%show_branches%}',

            'keen_api_enabled'      => '{%keen_api_enabled%}',
            'keen_api_master_key'      => '{%keen_api_master_key%}',
            'keen_api_write_key'      => '{%keen_api_write_key%}',
            'keen_api_read_key'      => '{%keen_api_read_key%}',
            'keen_api_config'      => '{%keen_api_config%}',
            'google_api_enabled'      => '{%google_api_enabled%}',
            'google_api_code'      => '{%google_api_code%}',
            'google_api_config'      => '{%google_api_config%}',

		);
	}

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
            array('shorturl,api_key,api_secret_key', 'unique'),
			array('active, timelimit', 'numerical', 'integerOnly'=>true),
			array('user_id, category_id', 'length', 'max'=>11),
			array('name, icon, length', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, category_id, name, active, icon, length, timelimit, featured,description,maxactions,
			show_toplist,register_email,register_sms,choose_playername,choose_avatar,home_instructions,shorturl,show_logo,show_social,show_branches,
			api_key,api_secret_key,api_enabled,api_callback_url,admin_comment
			', 'safe'),
		);
	}
   
    public static function getAllPosts($actionid){
        $sql = 'SELECT *,
                      ae_ext_bbs.id as entryid,
                      ae_game_branch_action.name AS actiontitle,
                      ae_ext_bbs.msg AS msg,
                      ae_ext_bbs.date AS maindate,
                      ae_ext_bbs.parent_id AS commentparentid,
					  ae_game.user_id AS gameauthor,
                      usergroups_user.id AS userid,
					  usergroups_user.email AS useremail
                    FROM ae_ext_bbs
                    LEFT JOIN usergroups_user ON ae_ext_bbs.user_id = usergroups_user.id
					LEFT JOIN ae_game_play_action ON ae_ext_bbs.playtask_id = ae_game_play_action.id
                    LEFT JOIN ae_game_branch_action ON ae_game_play_action.action_id = ae_game_branch_action.id
                    LEFT JOIN ae_game_branch ON ae_game_branch_action.branch_id = ae_game_branch.id
				    LEFT JOIN ae_game ON ae_game.id = ae_game_branch.game_id
                    WHERE ae_game_branch_action.id = :actionId
                    GROUP BY entryid
                    ORDER BY commentparentid DESC, ae_ext_bbs.date DESC
                    ';

        // ORDER BY ae_game_play.user_id, ae_game_play_action.id, ae_ext_bbs.date DESC

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':actionId' => $actionid))
            ->queryAll();

        return $rows;
    }
	
	

    public function updateFlags(){
        $obj = AeplayAction::model()->findByPk($this->playtask_id);
        $actionid = $obj->action_id;

        $sql = 'UPDATE ae_game_play_action SET newcount = newcount+1 WHERE action_id = :actionId';

        Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':actionId' => $actionid))
            ->query();

    }
	
	
    
	
	 public static function getAllBbsActions($gameid){

        $obj = Aeactiontypes::model()->findByAttributes(array('shortname' => 'bbs'));


        $sql = 'SELECT *,
                    ae_game_branch_action.name AS actiontitle
                    FROM ae_game_play_action
                    LEFT JOIN ae_game_branch_action ON ae_game_play_action.action_id = ae_game_branch_action.id
                    LEFT JOIN ae_game_branch ON ae_game_branch_action.branch_id = ae_game_branch.id
					LEFT JOIN ae_game_play ON ae_game_branch.game_id = ae_game_play.game_id
                    WHERE ae_game_play.game_id = :playId
					AND ae_game_branch_action.type_id = :typeId
					GROUP BY ae_game_play_action.action_id
                    ORDER BY ae_game_play_action.id DESC
                    ';

        // ORDER BY ae_game_play.user_id, ae_game_play_action.id, ae_ext_bbs.date DESC

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':playId' => $gameid, ':typeId' => $obj->id))
            ->queryAll();

        return $rows;
    }

    public static function getUserVariables($gameid){

        $sql = 'SELECT *,
                    ae_game_play_variable.id AS playvariableid,
                    ae_game_variable.name AS variablename,
                    ae_game_play_variable.value AS variablevalue,
                    ae_game_variable.id AS variableid
                    FROM ae_game_play
                    LEFT JOIN usergroups_user ON ae_game_play.user_id = usergroups_user.id
                    LEFT JOIN ae_game_play_variable ON ae_game_play_variable.play_id = ae_game_play.id
                    LEFT JOIN ae_game_variable ON ae_game_play_variable.variable_id = ae_game_variable.id
                    LEFT JOIN ae_ext_bbs ON usergroups_user.id = ae_ext_bbs.user_id
                    WHERE ae_game_play.game_id = :gameId
                    AND ae_ext_bbs.id IS NOT NULL
                    GROUP BY playvariableid
                    ';

        // ORDER BY ae_game_play.user_id, ae_game_play_action.id, ae_ext_bbs.date DESC

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':gameId' => $gameid))
            ->queryAll();

        $ret = array();

        while($play = each($rows)){
            $play = $play[1];
            $variableid = $play['variableid'];
            $id = $play['user_id'];
            $ret[$id][$variableid] = $play;
        }
       
        return $ret;
    }
    
	
	
	 public static function getGameBbs($gid){
        $sql = 'SELECT *,
                      ae_ext_bbs.id as entryid,
                      ae_game_branch_action.name AS actiontitle,
                      ae_game_play_action.id AS actionid

                    FROM ae_ext_bbs
                    LEFT JOIN ae_game_play_action ON ae_ext_bbs.playtask_id = ae_game_play_action.id
                    LEFT JOIN ae_game_play ON ae_game_play_action.play_id = ae_game_play.id
                    LEFT JOIN usergroups_user ON ae_game_play.user_id = usergroups_user.id
                    LEFT JOIN ae_game_branch_action ON ae_game_play_action.action_id = ae_game_branch_action.id
                    WHERE ae_game_play.game_id = :gameId
                    GROUP BY entryid
                    ORDER BY ae_ext_bbs.date DESC
                    ';

      
        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':gameId' => $gid))
            ->queryAll();

        return $rows;

    }


    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'aeaction' => array(self::BELONGS_TO, 'Aeaction', 'playtask_id'),
		);
    }

    // check that user really has access permission
    public static function checkPermissions($userid){

        if($userid != Yii::app()->user->id){
            return false;
        } else {
            return true;
        }

    }


}