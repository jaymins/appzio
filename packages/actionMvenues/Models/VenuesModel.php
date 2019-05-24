<?php

namespace packages\actionMvenues\Models;

use CActiveRecord;

class VenuesModel extends CActiveRecord {

    public $id;
    public $playid;
    public $game_id;
    public $places_wantsee;
    public $places_havebeen;
    public $import_date;

    public $lat;
    public $lon;
    public $type = 'venue';
    public $last_udpate;
    public $name;
    public $phone;
    public $address;
    public $zip;
    public $city;
    public $country;
    public $county;
    public $info;
    public $logo;
    public $images;
    public $premium;
    public $headerimage1;
    public $headerimage2;
    public $headerimage3;
    public $last_update;

    public $hex_color;
    public $code;

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ae_ext_mobileplaces';
    }

    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'aegame' => array(self::BELONGS_TO, 'aegame', 'game_id'),
        );
    }

    public function loadWishListAndVisited(){
        $vars = AeplayVariable::getArrayOfPlayvariables($this->playid);

        if(isset($vars['places_havebeen'])){
            $this->places_havebeen = json_decode($vars['places_havebeen'],true);
        }

        if(isset($vars['places_wantsee'])){
            $this->places_wantsee = json_decode($vars['places_wantsee'],true);
        }

    }

    public function getMyClubs(){
        if(empty($this->places_havebeen) OR !is_array($this->places_havebeen)){
            $this->loadWishListAndVisited();
        }

        if(empty($this->places_havebeen) OR !is_array($this->places_havebeen)){
            return false;
        }


        $where = $this->getWhere($this->places_havebeen);

        if($where){
            return $this->dosearch('',150,$where);
        }

        return array();
    }


    public function getMyWishlist(){
        if(empty($this->places_wantsee) OR !is_array($this->places_wantsee)){
            $this->loadWishListAndVisited();
        }

        if(empty($this->places_wantsee) OR !is_array($this->places_wantsee)){
            return array();
        }

        $where = $this->getWhere($this->places_wantsee);

        if($where){
            return $this->dosearch('',150,$where);
        }

        return array();
    }

    public function getWhere($array){
        $sql = '';

        foreach($array as $key=>$myplace){
            $sql .= $key .',';
        }

        if(strlen($sql) < 2){
            return false;
        }

        $sql = substr($sql,0,-1);
        $where = 'AND id IN ('.$sql.')' ;
        $where .= ' AND game_id = '.$this->game_id;
        return $where;
    }

    public function dosearch($term,$limit=15,$where=false){
        $vars = AeplayVariable::getArrayOfPlayvariables($this->playid);

        if(!isset($vars['lat']) OR !isset($vars['lon'])) {
            return array();
        }

        $lat = $vars['lat'];
        $lon = $vars['lon'];

        Yii::app()->db->createCommand("set @orig_lat=$lat")->execute();
        Yii::app()->db->createCommand("set @orig_long=$lon")->execute();
        Yii::app()->db->createCommand("set @bounding_distance=360")->execute();

        $sql = "SELECT *,
              ((ACOS(SIN(@orig_lat * PI() / 180) * SIN(`lat` * PI() / 180) + COS(@orig_lat * PI() / 180) * COS(`lat` * PI() / 180) * COS((@orig_long - `lon`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS `distance`
              FROM ae_ext_mobileplaces WHERE `name` LIKE '%$term%' $where ORDER BY distance ASC  LIMIT 0,$limit";

        //print_r($sql);die();
        $rows = Yii::app()->db
            ->createCommand($sql)
            /*            ->bindValues(array(
                            ':gameId' => $this->gid,
                            ':userId' => $this->user_id,
                            ':score' => $score,
                            ':scoregroup' => $scoregroup,
                            ':sex' => $sex
                            ))*/
            ->queryAll();


        return $rows;



    }


}