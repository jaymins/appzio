<?php

namespace packages\actionMusersettings\themes\adidas\Controllers;

use packages\actionMusersettings\themes\adidas\Views\Main;
use packages\actionMusersettings\themes\adidas\Views\View as ArticleView;
use packages\actionMusersettings\themes\adidas\Models\Model as ArticleModel;
use packages\actionMitems\Models\ItemCategoryModel;

class Statuses extends \packages\actionMusersettings\Controllers\Controller {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

    /* this is the default action inside the controller. This gets called, if
        nothing else is defined for the route */
    public function actionDefault(){

        $data = array();

        $name = $this->getMenuId();

        if ( !empty($name) ) {
            $this->model->sessionSet('tmp_popup_value', $name);
        } else {
            $name = $this->model->sessionGet('tmp_popup_value');
        }
        
        $nameParts = explode('-', $name);

        if (count($nameParts) == 1) {

            $nameParts = explode('|', $name);

            $name = $nameParts[0];
            $prefix = '';
            if (isset($nameParts[1])) {
                $data['prefix'] = $nameParts[1];
                $prefix = $nameParts[1];
            }

            $data['name_list'] = $name;

            $triggers = [
                'category', 'category_mm', 'my_styles'
            ];

            if (in_array($name, $triggers)) {
                $categories = ItemCategoryModel::model()->findAll(array('condition' => 'app_id = ' .  $this->model->appid ,"order" => "name"));
                $list = [];

                if ( $name == 'category_mm' ) {
                    $list[] = 'Not Sure';

                    // Revert the name so we preserve the keys
                    $name = 'category';
                    $data['name_list'] = $name;
                }

                foreach ($categories as $category) {
                    $list[] = $category->name;
                }

                $data['list_items'] = $list;
            } else {
                $data['list_items'] = explode(',', $this->model->getGlobalVariableByName($name));
            }

            $data['type'] = 'checkbox';

            if ($this->getMenuId() == 'level') {
                $data['type'] = 'radio';
            }

            $values = $this->model->getSavedVariable($prefix.$name);
            $allValues = json_decode($values, 1);

            if (is_array($allValues)) {
                foreach ($allValues as $value) {
                    $this->model->submitvariables[$prefix.$name . '-' . $value] = 1;
                }
            }

            return ['Checkboxes', $data];
        } else {
            $this->no_output = true;

            $nameParts = explode('|', $nameParts[1]);

            $nameList = $nameParts[0];

            $prefix = '';
            if (isset($nameParts[1])) {
                $prefix = $nameParts[1];
            }

            $this->model->saveCheckboxes($nameList, $prefix);
        }

    }

}