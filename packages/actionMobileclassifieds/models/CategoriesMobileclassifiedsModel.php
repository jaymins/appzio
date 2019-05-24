<?php

class CategoriesMobileclassifiedsModel extends ArticleModel {

    public $app_id;
    public $name;

    public function tableName(){
        return 'ae_ext_classifieds_categories';
    }

    public function add($category)
    {
        $model = new CategoriesMobileclassifiedsModel();
        $model->name = $category['name'];
        $model->app_id = $this->factory->playid;

        if ( $model->insert() ) {
            return true;
        }

        return false;
    }

    public function getCategories()
    {
        $model = new CategoriesMobileclassifiedsModel();
        return $model->findAll();
    }

}