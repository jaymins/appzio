<?php

class MobileeventsModel extends ArticleModel {

    public $play_id;
    public $game_id;
    public $place_id;
    public $date;
    public $time;
    public $time_of_day;
    public $notes;
    public $status;

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ae_ext_mobileevents';
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'mobileplaces' => array(self::BELONGS_TO, 'mobileplaces', 'place_id'),
            'play' => array(self::BELONGS_TO, 'aeplay', 'play_id'),
        );
    }

    public function markOld($id){
        $ob = MobileeventsModel::model()->findByPk($id);
        if(is_object($ob)) {
            $ob->status = 'archived';
            $ob->update();
            return true;
        }

        return false;
    }

    public function setStatus($id,$playid,$status){
        $ob = MobileeventparticipantsModel::model()->findByAttributes(array('event_id' => $id,'play_id' => $playid));
        if(is_object($ob)) {
            $ob->status = $status;
            $ob->update();
            return true;
        } else {
            $ob = new MobileeventparticipantsModel();
            $ob->play_id = $this->play_id;
            $ob->status = $status;
            $ob->event_id = $id;
            $ob->insert();
        }

        return false;
    }


    public function getEvent($id){
        $sql = "SELECT *,ae_ext_mobileevents.id as eventid, ae_ext_mobileplaces.id as placeid, ae_ext_mobileevents.play_id as eventplayid,
                ae_ext_mobileevents_participants.play_id AS participantplayid, ae_ext_mobileevents.status AS eventstatus
                FROM ae_ext_mobileevents
                LEFT JOIN ae_ext_mobileevents_participants ON ae_ext_mobileevents.id = ae_ext_mobileevents_participants.event_id
                LEFT JOIN ae_ext_mobileplaces ON ae_ext_mobileevents.place_id = ae_ext_mobileplaces.id
                WHERE ae_ext_mobileevents.id = :id
                ORDER BY `date`,`time` DESC
                ";

        $rows = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':id' => $id))
            ->queryAll();

        if(isset($rows[0]['eventid']) AND $rows[0]['eventid'] == $id){
            return $rows[0];
        }

        return false;


    }

    public function getMyEvents($which='my'){

        if(isset($this->varcontent['lat']) AND isset($this->varcontent['lon'])){
            $lat = $this->varcontent['lat'];
            $lon = $this->varcontent['lon'];
            Yii::app()->db->createCommand("set @orig_lat=$lat")->execute();
            Yii::app()->db->createCommand("set @orig_long=$lon")->execute();
            Yii::app()->db->createCommand("set @bounding_distance=360")->execute();
            $distancequery = ',((ACOS(SIN(@orig_lat * PI() / 180) * SIN(`lat` * PI() / 180) + COS(@orig_lat * PI() / 180) * COS(`lat` * PI() / 180) * COS((@orig_long - `lon`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS `distance`';
        } else {
            $distancequery = false;
        }

        switch($which){
            case 'my':
                $sql = "SELECT *,ae_ext_mobileevents.id as eventid
                $distancequery
                FROM ae_ext_mobileevents
                LEFT JOIN ae_ext_mobileplaces ON ae_ext_mobileevents.place_id = ae_ext_mobileplaces.id
                WHERE ae_ext_mobileevents.play_id = $this->play_id
                AND `status` <> 'archived'
                ORDER BY `date`,`time` DESC
                ";
                break;

            case 'invited':
                $sql = "SELECT *,ae_ext_mobileevents.id as eventid
                $distancequery
                FROM ae_ext_mobileevents
                LEFT JOIN ae_ext_mobileevents_participants ON ae_ext_mobileevents.id = ae_ext_mobileevents_participants.event_id
                LEFT JOIN ae_ext_mobileplaces ON ae_ext_mobileevents.place_id = ae_ext_mobileplaces.id
                WHERE ae_ext_mobileevents_participants.play_id = :playID
                AND ae_ext_mobileevents_participants.`status` = 'invited'
                ORDER BY `date`,`time` DESC
                ";
                break;

            case 'participating':
                $sql = "SELECT *,ae_ext_mobileevents.id as eventid
                $distancequery
                FROM ae_ext_mobileevents
                LEFT JOIN ae_ext_mobileevents_participants ON ae_ext_mobileevents.id = ae_ext_mobileevents_participants.event_id
                LEFT JOIN ae_ext_mobileplaces ON ae_ext_mobileevents.place_id = ae_ext_mobileplaces.id
                WHERE ae_ext_mobileevents_participants.play_id = :playID
                AND ae_ext_mobileevents_participants.`status` = 'coming'
                AND ae_ext_mobileevents.play_id <> $this->play_id
                ORDER BY `date`,`time` DESC
                ";
                break;

            case 'open':
                $sql = "SELECT *,ae_ext_mobileevents.id as eventid
                $distancequery
                FROM ae_ext_mobileevents
                LEFT JOIN ae_ext_mobileplaces ON ae_ext_mobileevents.place_id = ae_ext_mobileplaces.id
                LEFT JOIN ae_ext_mobileevents_participants ON ae_ext_mobileevents.id = ae_ext_mobileevents_participants.event_id AND ae_ext_mobileevents_participants.play_id = :playID
                WHERE 
                ae_ext_mobileevents.play_id <> $this->play_id
                AND ae_ext_mobileevents.`status` = 'open-all'
                ORDER BY `date`,`time` DESC
                ";
                break;

            case 'today':
                $sql = "SELECT *,ae_ext_mobileevents.id as eventid
                $distancequery
                FROM ae_ext_mobileevents
                LEFT JOIN ae_ext_mobileevents_participants ON ae_ext_mobileevents.id = ae_ext_mobileevents_participants.event_id
                LEFT JOIN ae_ext_mobileplaces ON ae_ext_mobileevents.place_id = ae_ext_mobileplaces.id
                WHERE ae_ext_mobileevents_participants.play_id = :playID
                AND ae_ext_mobileevents_participants.`status` = 'coming'
                AND ae_ext_mobileevents.date = DATE(NOW())
                ORDER BY `date`,`time` DESC
                ";
                break;

            case 'on-going':
                $sql = "SELECT *,ae_ext_mobileevents.id as eventid
                FROM ae_ext_mobileevents
                LEFT JOIN ae_ext_mobileevents_participants ON ae_ext_mobileevents.id = ae_ext_mobileevents_participants.event_id
                LEFT JOIN ae_ext_mobileplaces ON ae_ext_mobileevents.place_id = ae_ext_mobileplaces.id
                WHERE ae_ext_mobileevents_participants.play_id = :playID
                AND ae_ext_mobileevents_participants.`status` = 'participating'
                AND ae_ext_mobileevents.date = DATE(NOW())
                ORDER BY `date`,`time` DESC
                ";
                break;

            case 'archived':
                $sql = "SELECT *,ae_ext_mobileevents.id as eventid
                FROM ae_ext_mobileevents
                LEFT JOIN ae_ext_mobileevents_participants ON ae_ext_mobileevents.id = ae_ext_mobileevents_participants.event_id
                LEFT JOIN ae_ext_mobileplaces ON ae_ext_mobileevents.place_id = ae_ext_mobileplaces.id
                WHERE ae_ext_mobileevents_participants.play_id = :playID
                AND ae_ext_mobileevents_participants.`status` = 'finished'
                ORDER BY `date`,`time` DESC
                ";
                break;

        }

        //echo($this->play_id);die();
        
        if(isset($sql)){
            $rows = Yii::app()->db
                ->createCommand($sql)
                ->bindValues(array(
                    ':playID' => $this->play_id))
                ->queryAll();

            return $rows;
        }

    }

}