<?php

class MobilegolfholeuserModel extends ArticleModel {

    public $play_id;
    public $event_info;

    public $holeobj;
    public $errors;
    public $user_current_holeobj;
    public $round_strokes;
    public $course_par;
    public $end_of_round = false;

    public $hole_count;

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ae_ext_golf_hole_user';
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


    public function getStrokes(){
        if(isset($this->user_current_holeobj->strokes)){
            return $this->user_current_holeobj->strokes;
        } else {
            return false;
        }
    }

    public function getCurrentHoleNumber(){
        if(isset($this->holeobj->number)){
            return $this->holeobj->number;
        } else {
            return false;
        }
    }

    public function getUserHoleId(){
        if(isset($this->user_current_holeobj->id)){
            return $this->user_current_holeobj->id;
        } else {
            return false;
        }
    }

    public function getHoleId(){
        if(isset($this->holeobj->id)){
            return $this->holeobj->id;
        } else {
            return false;
        }
    }



    public function nextHole($skip=false){

        if($skip){
            $this->user_current_holeobj->strokes = 10;
            $this->user_current_holeobj->done = 1;
            $this->user_current_holeobj->update();
        } else {
            /* mark done before switching to new object */
            $this->user_current_holeobj->done = 1;
            $this->user_current_holeobj->update();
        }

        /* check that there are more holes left */
        $this->getCurrentHoleNumber();
        $holobj = MobilegolfholeModel::model()->findByAttributes(array('place_id' => $this->event_info['placeid'],'number' => $this->holeobj->number+1));

        if(is_object($holobj)){

            $this->holeobj = $holobj;
            $userobj = MobilegolfholeuserModel::model()->findByAttributes(array('event_id' => $this->event_info['eventid'],'hole_id' => $this->holeobj->id,'play_id' => $this->play_id));

            if(!is_object($userobj)){
                $obj = new MobilegolfholeuserModel();
                $obj->event_id = $this->event_info['eventid'];
                $obj->play_id = $this->play_id;
                $obj->hole_id = $this->holeobj->id;
                $obj->insert();
                $this->user_current_holeobj = $obj;
            }
        } else {
            $this->end_of_round = true;
        }

    }


    public function addOne(){
        $this->user_current_holeobj->strokes = $this->user_current_holeobj->strokes+1;
        $this->user_current_holeobj->update();
        $this->setRoundStrokes();
    }

    public function minusOne(){
        if($this->user_current_holeobj->strokes > 0){
            $this->user_current_holeobj->strokes = $this->user_current_holeobj->strokes-1;
            $this->user_current_holeobj->update();
            $this->setRoundStrokes();
        }
    }

    /* looks for current hole and if the first doesn't exist yet it will create it automatically for user */
    public function setCurrentHole($eventid){
        $create = false;
        $this->hole_count = MobilegolfholeModel::model()->countByAttributes(array('place_id' => $this->event_info['placeid']));

        /* look at how many rounds are set, that gives a clue about the current hole */
        $count = MobilegolfholeuserModel::model()->countByAttributes(array('event_id' => $eventid,'play_id' => $this->play_id));

        if($count == 0){
            $count = 1;
            $create = true;
        }

        $this->holeobj = MobilegolfholeModel::model()->findByAttributes(array('place_id' => $this->event_info['placeid'],'number' => $count));

        if(isset($this->holeobj->id)){
            $this->user_current_holeobj = MobilegolfholeuserModel::model()->findByAttributes(array('hole_id' => $this->holeobj->id, 'event_id' => $eventid,'play_id' => $this->play_id));
        } else {
            $this->user_current_holeobj = MobilegolfholeuserModel::model()->findByAttributes(array('event_id' => $eventid,'play_id' => $this->play_id));
        }

        if(!is_object($this->user_current_holeobj)){
            $create = true;
            $current_hole = 1;
            $this->holeobj = MobilegolfholeModel::model()->findByAttributes(array('place_id' => $this->event_info['placeid'],'number' => $current_hole));
        } else {
            $this->holeobj = MobilegolfholeModel::model()->findByPk($this->user_current_holeobj->hole_id);
        }

        /* means the round is completed for this user */
        if(isset($this->holeobj->number) AND $this->holeobj->number == $this->hole_count AND isset($this->user_current_holeobj->done) AND $this->user_current_holeobj->done == 1){
            $this->end_of_round = true;
        }

        if(!is_object($this->holeobj)){
            $this->errors[] = '{#place_doesnt_have_holes_defined#}';
            return false;
        }

        if($create AND is_object($this->holeobj)){
            $obj = new MobilegolfholeuserModel();
            $obj->event_id = $eventid;
            $obj->play_id = $this->play_id;
            $obj->hole_id = $this->holeobj->id;
            $obj->insert();
            $this->user_current_holeobj = $obj;
        }

        $this->setRoundStrokes();

        if(is_object($this->user_current_holeobj)){
            return $this->user_current_holeobj;
        }

        return false;
    }

    public function getCoursePar(){

        if($this->course_par){
            return $this->course_par;
        }

        $sql = 'SELECT sum(par) AS coursepar FROM ae_ext_golf_hole WHERE place_id = :placeId';
        $result = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':placeId' => $this->event_info['placeid']
            ))
            ->queryAll();

        if(isset($result[0]['coursepar'])){
            $this->course_par = $result[0]['coursepar'];
            return $this->course_par;
        } else {
            return false;
        }


    }

    public function setRoundStrokes(){
        $sql = 'SELECT sum(strokes) AS totalstrokes FROM ae_ext_golf_hole_user WHERE event_id = :eventId AND play_id = :playId';
        $result = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':eventId' => $this->event_info['eventid'],
                ':playId' => $this->play_id
            ))
            ->queryAll();

        if(isset($result[0]['totalstrokes'])){
            $this->round_strokes = $result[0]['totalstrokes'];
        } else {
            $this->round_strokes = 0;
        }
    }

    public function getScoreCard($id=false,$placeid=false){
        if(!$id){
            $id = $this->event_info['eventid'];
        }

        if(!$placeid){
            $placeid = $this->event_info['placeid'];
        }

        $sql = 'SELECT * FROM ae_ext_golf_hole 
              LEFT JOIN ae_ext_golf_hole_user ON ae_ext_golf_hole_user.hole_id = ae_ext_golf_hole.id AND ae_ext_golf_hole_user.play_id = :playId AND ae_ext_golf_hole_user.event_id = :eventId
              WHERE ae_ext_golf_hole.place_id = :placeId';
        $result = Yii::app()->db
            ->createCommand($sql)
            ->bindValues(array(
                ':eventId' => $id,
                ':playId' => $this->play_id,
                ':placeId' => $placeid
            ))
            ->queryAll();

        return $result;

    }




}