<?php

namespace packages\actionMusersettings\themes\uikit\Controllers;

use packages\actionMusersettings\themes\uikit\Views\Main;
use packages\actionMusersettings\themes\uikit\Views\View as ArticleView;
use packages\actionMusersettings\themes\uikit\Models\Model as ArticleModel;

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
            $data['list_items'] = explode(',', $this->model->getGlobalVariableByName($name));
            $data['type'] = 'checkbox';

            if ($this->getMenuId() == 'level') {
                $data['type'] = 'radio';
            }

            $values = $this->model->getSavedVariable($prefix.$name);
            $allValues = explode(',', $values);
            foreach ($allValues as $value) {
                $this->model->submitvariables[$prefix.$name . '-' . $value] = 1;
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
