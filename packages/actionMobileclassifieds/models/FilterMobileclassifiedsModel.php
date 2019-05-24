<?php

class FilterMobileclassifiedsModel extends ArticleModel {

    public $id;
    public $play_id;
    public $category;
    public $location;
    public $distance;
    public $price_min;
    public $price_max;

    public function tableName(){
        return 'ae_ext_classifieds_filter';
    }

    public function addFilter()
    {
        $model = new FilterMobileclassifiedsModel();
        $model->play_id = $this->factory->playid;
        $mode = 'insert';
        $modelExists = $model->findByAttributes(['play_id' => $this->factory->playid]);

        if ($modelExists) {
            $model = $modelExists;
            $mode = 'update';
        }

        foreach ($this->factory->submitvariables as $key => $value) {
            $property = array_search($key, $this->factory->vars);

            if($property == 'price_min' || $property == 'price_max') {
                $value *= 100;
            }

            if (property_exists('FilterMobileclassifiedsModel', $property)) {
                $model->$property = $value;
            }
        }

        if ( $model->$mode() ) {
            return true;
        }

        return false;
    }

    public function getFilter()
    {
        $model = new FilterMobileclassifiedsModel();
        $modelExists = $model->findByAttributes(['play_id' => $this->factory->playid]);

        if ($modelExists) {
            $model = $modelExists;
        }

        return $model;
    }

}