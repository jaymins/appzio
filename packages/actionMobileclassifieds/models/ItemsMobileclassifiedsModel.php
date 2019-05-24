<?php

class ItemsMobileclassifiedsModel extends ArticleModel {

    public $id;
    public $play_id;
    public $category_id;
    public $category;
    public $title;
    public $description;
    public $country;
    public $city;
    public $price;
    public $creator;
    public $pictures;
    public $favourite;
    private $current_item;

    public function tableName(){
        return 'ae_ext_classifieds_items';
    }

    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function setPictures($pictures)
    {
        $this->pictures = $pictures;
    }


    public function setItem(){

        if($this->id AND !$this->current_item){
            $this->current_item = ItemsMobileclassifiedsModel::model()->findByPk($this->id);
        }
    }

    /**
     * Store or update item
     */
    public function handleSubmit($mode = 'insert', $id = false)
    {
        $model = new ItemsMobileclassifiedsModel();

        if ($mode != 'insert') {
            $model = $model->findByPk($id);
        }

        $model->play_id = $this->factory->playid;
        $model->creator = $this->factory->playid;

        foreach ($this->factory->submitvariables as $key => $value) {
            $property = array_search($key, $this->factory->vars);

            if($property == 'price') {
                $value *= 100;
            }

            if (property_exists('ItemsMobileclassifiedsModel', $property)) {
                $model->$property = $value;
            }

            $modelPictures = json_decode($model->pictures);

            foreach ($this->pictures as $index => $picture) {
                if (empty($picture)) {
                    $this->pictures[$index] = $modelPictures[$index];
                }
            }

            $model->pictures = json_encode($this->pictures);
        }

        if ( $model->$mode() ) {
            return true;
        }

        return false;
    }

    public function getItems($searchterm = null)
    {
        $model = new ItemsMobileclassifiedsModel();
        $criteria = new CDbCriteria();
        $criteria->select = 't.*, fi.favourite';
        $criteria->join = 'LEFT JOIN ae_ext_classifieds_favourite_items fi on fi.item_id = t.id AND fi.play_id = :play_id';
        $criteria->params[':play_id'] = $this->factory->playid;
        $criteria->order = 'created_at DESC';

        if (!empty($searchterm)) {
            $match = addcslashes(strtolower($searchterm), '%_');

            $criteria->condition = "(title LIKE :title OR description LIKE :title) ";
            $criteria->params = [
                ':title' => "%$match%",
                ':play_id' => $this->factory->playid
            ];
        }

        return $model->findAll($criteria);
    }

    public function getFavouriteItems($searchterm = null)
    {
        $model = new ItemsMobileclassifiedsModel();
        $criteria = new CDbCriteria();
        $criteria->select = 't.*, fi.favourite';
        $criteria->join = 'INNER JOIN ae_ext_classifieds_favourite_items fi on fi.item_id = t.id AND fi.play_id = :play_id';
        $criteria->params[':play_id'] = $this->factory->playid;
        $criteria->order = 'created_at DESC';
        $criteria->condition = 'fi.favourite = 1';

        if (!empty($searchterm)) {
            $match = addcslashes(strtolower($searchterm), '%_');

            $criteria->condition = "AND (title LIKE :title OR description LIKE :description)";
            $criteria->params = [
                ':title' => "%$match%",
                ':description' => "%$match%"
            ];

        }

        return $model->findAll($criteria);
    }

    public function getMyItems($searchterm = null)
    {
        $model = new ItemsMobileclassifiedsModel();
        $criteria = new CDbCriteria();
        $criteria->addCondition('creator = :creator');
        $criteria->params[':creator'] = $this->factory->playid;

        if (!empty($searchterm)) {
            $match = addcslashes(strtolower($searchterm), '%_');
            $criteria->addCondition('(title LIKE :title OR description LIKE :description)');
            $criteria->params[':title'] = "%$match%";
            $criteria->params[':description'] = "%$match%";
        }

        return $model->findAll($criteria);
    }

    public function getItem($id) {

        $model = new ItemsMobileclassifiedsModel();
        $criteria = new CDbCriteria();
        $criteria->select = 't.*, fi.favourite';
        $criteria->join = 'LEFT JOIN ae_ext_classifieds_favourite_items fi on fi.item_id = t.id AND fi.play_id = :play_id';
        $criteria->addCondition('t.id = :id');
        $criteria->params[':id'] =  $id;
        $criteria->params[':play_id'] = $this->factory->playid;

        return $model->find($criteria);
    }

    public function deleteItem($id) {

        $model = new ItemsMobileclassifiedsModel();

        return $model->deleteByPk($id);
    }

    public function deletePicture($id, $picrureIndex) {

        $model = new ItemsMobileclassifiedsModel();
        $model = $model->findByPk($id);
        $pictures = json_decode($model->pictures);
        $pictures[$picrureIndex - 1] = false;
        $model->pictures = json_encode($pictures);
        $model->save();

        return $model;
    }

    public function bumpUpItemPics()
    {

        $this->setItem();
        $pics = @json_decode($this->current_item->pictures,true);
        $i = 0;

        if(!is_array($pics)){
            return false;
        }

        /* create an array of all pics in sequential order */
        foreach ( $pics as $key=>$field){
            if($field) {
                $actual_pics[] = $field;
            }
            $i++;
        }

        if(!isset($actual_pics)){
            return false;
        }

        /* if actual pictures and original pictures are not the same, save */
        if($actual_pics != $pics){
            $this->current_item->pictures = json_encode($actual_pics);
            $this->current_item->update();
        }

    }
}