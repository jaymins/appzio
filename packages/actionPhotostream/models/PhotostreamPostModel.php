<?php


/* this is the modules own model file,
    which is used by both controller & other components
 */


class PhotostreamPostModel extends PhotostreamModel
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
		return 'ae_ext_photostream_post';
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
            'newheadboard'          => '{%newheadboard%}',
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
			api_key,api_secret_key,api_enabled,api_callback_url
			', 'safe'),
		);
	}




	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'photostream' => array(self::BELONGS_TO, 'PhotostreamModel', 'photostream_id'),
		);
    }



    public static function getPosts($actionid){
        $sql = 'SELECT *,
                      ae_ext_photostream.id as entryid,
                      ae_game_branch_action.name AS actiontitle,
                      ae_game_play_action.id AS actionid,
                      ae_ext_photostream_post.msg AS `reply`,
                      ae_ext_photostream.msg AS msg,
                      ae_ext_photostream.date AS maindate,
                      (SELECT * FROM ae_ext_photostream_post WHERE photostream_id = entryid)
                    FROM ae_ext_photostream
                    LEFT JOIN ae_ext_photostream_post ON
                    LEFT JOIN ae_game_play_action ON ae_ext_photostream.playtask_id = ae_game_play_action.id
                    LEFT JOIN ae_game_play ON ae_game_play_action.play_id = ae_game_play.id
                    LEFT JOIN usergroups_user ON ae_game_play.user_id = usergroups_user.id
                    LEFT JOIN ae_game_branch_action ON ae_game_play_action.action_id = ae_game_branch_action.id
                    LEFT JOIN ae_game_play_variable ON ae_game_play.id = ae_game_play_variable.play_id
                    LEFT JOIN ae_game_variable ON ae_game_play_variable.variable_id = ae_game_variable.id
                    WHERE ae_ext_photostream.playtask_id = :actionId
                    GROUP BY entryid
                    ORDER BY ae_ext_photostream.date, ae_ext_photostream_post.date DESC

                    ';

        // ORDER BY ae_game_play.user_id, ae_game_play_action.id, ae_ext_photostream.date DESC

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':actionId' => $actionid))
            ->queryAll();

        return $rows;

    }
	

    public static function getGamePhotostream($gid){
        $sql = 'SELECT *,
                      ae_ext_photostream.id as entryid,
                      ae_game_branch_action.name AS actiontitle,
                      ae_game_play_action.id AS actionid

                    FROM ae_ext_photostream
                    LEFT JOIN ae_game_play_action ON ae_ext_photostream.playtask_id = ae_game_play_action.id
                    LEFT JOIN ae_game_play ON ae_game_play_action.play_id = ae_game_play.id
                    LEFT JOIN usergroups_user ON ae_game_play.user_id = usergroups_user.id
                    LEFT JOIN ae_game_branch_action ON ae_game_play_action.action_id = ae_game_branch_action.id
                    WHERE ae_game_play.game_id = :gameId
                    ORDER BY ae_ext_photostream.date DESC
                    ';

        // ORDER BY ae_game_play.user_id, ae_game_play_action.id, ae_ext_photostream.date DESC

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(':gameId' => $gid))
            ->queryAll();

        return $rows;

    }

    public function deleteGame($gid){
            $sql = "DELETE FROM ae_game
				WHERE ae_game.id = :gameId AND
					  ae_game.user_id = :userId
				";

            $this->connection
                ->createCommand($sql)
                ->bindValues(array(':gameId' => $gid, ':userId' => $this->userid))
                ->query();

            return true;
        }


	/* ae_game_play contains active games 
		id is referred to as playid everywhere within an app
	*/



	public function myGames($role = 'admin'){
		$sql = "	SELECT ae_game.*,ae_game_play.*,ae_game_play.id AS playid, ae_game.id AS gameid, ae_category.name AS categoryname,
	                sum(ae_game_play_action.points) AS points,
	                    (SELECT count(ae_game_play_action.id) FROM ae_game_play_action WHERE ae_game_play_action.play_id = playid AND ae_game_play_action.status = '1') AS taskcount

					FROM ae_game_play
                        LEFT JOIN ae_game ON ae_game_play.game_id = ae_game.id
						LEFT JOIN ae_role ON ae_game_play.role_id = ae_role.id
						LEFT JOIN usergroups_user ON ae_game_play.user_id = usergroups_user.id
						LEFT JOIN ae_category ON ae_game.category_id = ae_category.id
						LEFT JOIN ae_game_play_action ON ae_game_play.id = ae_game_play_action.play_id

					WHERE ae_role.title = '$role'
					AND ae_game_play.status > '0'
			        AND usergroups_user.id = :userId
			        GROUP BY ae_game_play.id
			        ORDER BY added
					";

        $rows = $this->connection
            ->createCommand($sql)
            ->bindValues(array(':userId' => $this->userid))
            ->queryAll();

		return $rows;
	}


	public static function gameCategories(){
		$sql = "	SELECT id,name
					FROM ae_category
					WHERE visible = '1'
					ORDER BY name
					";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->queryAll();
		return $rows;
	}


    public static function getVariables($gid){
        $sql = "	SELECT id,name
					FROM ae_game_variable
					WHERE game_id = '$gid'
					ORDER BY name
					";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->queryAll();

        return $rows;
    }


    public static function getBranches($gid){
        $sql = "	SELECT id,CONCAT_WS(' - ', `order`,name) AS title,`order`,name
					FROM ae_game_branch
					WHERE game_id = '$gid'
					AND `name` <> 'GLOBAL ACTIONS' AND `name` <> 'COMPONENTS'
					ORDER BY `order`
					";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->queryAll();

        return $rows;
    }

    public function listGames($category){
		
		if($category == 'Featured'){
			$sql = "	SELECT *, ae_game.name AS gamename, ae_game.id AS gameid
						FROM ae_game
						WHERE 	active = '1' AND
								featured = '1'
						ORDER BY last_update
						";
		} else {
			$sql = "	SELECT *, ae_game.name AS gamename, ae_game.id AS gameid
						FROM ae_game
						LEFT JOIN ae_category ON category_id = ae_category.id
						WHERE 	ae_game.active = '1' AND
								ae_category.name = '$category'
						ORDER BY last_update
						";
		}
		
		$rows = $this->connection
            ->createCommand($sql)
            ->queryAll();

        return $rows;
		
	}

    /* answers whether user is playing a game or not */
    public function isPlaying(){
		
		
        $sql = "SELECT * FROM ae_game_play
				WHERE game_id = :gameId AND user_id = :userId AND role_id = '2' AND status = '1'";
				
		
        
        $ret = $this->connection
            ->createCommand($sql)
            ->bindValues(array(':gameId' => $this->gid, ':userId' => $this->userid))
            ->queryAll();

        if(count($ret) > 0){
            return true;
        } else {
            return false;
        }

    }


	public function playGame(){
       
		$sql = "INSERT INTO ae_game_play
				SET game_id = :gameId, user_id = :userId, role_id = '2', alert = '{%get_started%}',
				created = NOW()";
       // echo $this->userid; echo $sql; die;
        $this->connection
            ->createCommand($sql)
            ->bindValues(array(':gameId' => $this->gid, ':userId' => $this->userid))
            ->query();

        /* pointer for logic script to look for this play, so that user is served
        possible next action right away */
        $id = Yii::app()->db->lastInsertID;
        $controller = new LogicController(__FILE__,__LINE__);
        $controller->onlyplay_id = $id;
        $controller->runLogic();

        return true;
	}
	
	
	public function stopPlaying(){
        
        $sql = "DELETE FROM ae_game_play
				WHERE id = :playId
				";
				
        $this->connection
            ->createCommand($sql)
            ->bindValues(array(':playId' => $this->pid))
            ->query();
			
		return true;
	}



	public function addGame($data){
    	$game = new Aegame;
		
		$game->name=$data['gamename'];
		$game->category_id=$data['gamecategory'];
		$game->user_id=$this->userid;
		$game->save();

		$this->gid = $game->getPrimaryKey();
		$key = $this->gid;

		$sql = "INSERT INTO ae_game_play SET
				role_id='1', game_id = '$key', user_id = :userId, status='1'";
		
		$this->connection
			->createCommand($sql)
			->bindValues(array(':userId' => $this->userid))
			->query();

        $this->playGame();
		$this->addInitialGameData();

		return true;
	}
	
	
	/* this adds initial data for the newly created game */
	private function addInitialGameData(){
    	$alert = "{%check_your_first_level%}";
    	$levels = '1';
    	$headboard_portrait = 'changeme.jpg';
    	$sql = "UPDATE ae_game SET alert = '$alert', levels = '$levels', headboard_portrait = '$headboard_portrait'
    			WHERE id = :Id AND user_id = :userId";
		$rows=$this->connection
			->createCommand($sql)
			->bindValues(array(':Id' => $this->gid, ':userId' => $this->userid))
			->query();
		
		$sql = "INSERT INTO ae_game_branch SET name = 'First level', `order` = '1', game_id = :gameId";
		
		$rows=$this->connection
			->createCommand($sql)
			->bindValues(array(':gameId' => $this->gid))
			->query();
		
		return true;
    
	}
	
	public function getGame(){
		$sql = "SELECT ae_game.*, SUM(ae_game_branch_action.points) as maxpoints, COUNT(ae_game_branch_action.id) as totaltasks FROM ae_game
                LEFT JOIN ae_game_branch on ae_game.id = ae_game_branch.game_id
                LEFT JOIN ae_game_branch_action on ae_game_branch.id = ae_game_branch_action.branch_id
		WHERE ae_game.id = :gameID AND user_id = :userId";
		$rows=$this->connection
			->createCommand($sql)
			->bindValues(array(':userId' => $this->userid,':gameID' => $this->gid))
			->queryAll();

		$this->setVars($rows);
		return $rows;
	}


    public function setVars($rows){
		while($row = each($rows[0])){
			$this->$row['key'] = $row['value'];
		}		
	}

	public function saveIcon($icon){
		$sql = "UPDATE ae_game SET icon = '$icon' WHERE
				id = :Id
				AND
				user_id = :userId";
				
		$rows=$this->connection
			->createCommand($sql)
			->bindValues(array(':Id' => $this->gid,':userId' => $this->userid))
			->query();
		
		return true;
	}



	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('category_id',$this->category_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('active',$this->active);
		$criteria->compare('icon',$this->icon,true);
		$criteria->compare('length',$this->length,true);
		$criteria->compare('timelimit',$this->timelimit);
		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    // check that user really has access permission
    public static function checkPermissions($ptid){
        $check = AeplayAction::model()->with('aeplay')->findByPk($ptid);

        if($check->aeplay->userid != Yii::app()->user->id){
           return false;
        } else {
            return true;
        }

    }
	
}