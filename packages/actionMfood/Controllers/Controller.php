<?php

namespace packages\actionMfood\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMfood\themes\uikit\Models\Model as FoodModel;
use packages\actionMfood\Views\View as FoodView;

class Controller extends BootstrapController
{

    /**
     * @var FoodView
     */
    public $view;

    /**
     * Your model and Bootstrap model methods are accessible through this variable
     * @var FoodModel
     */
    public $model;

    /**
     * This is the default action inside the controller. This function gets called, if
     * nothing else is defined for the route
     * @return array
     */

    public $id;

    public function actionDefault()
    {
        $data = array();
        $action = $this->model->getCurrentActionPermaname();
        $this->setRecipeID();
        $recipe_id = $this->model->sessionGet('recipe_id');

        if ($action == 'nutritionpreview') {
            $data['calendar_item_id'] = $this->model->sessionGet('calendar_item_id');
        }

        $data['recipe_info'] = $this->model->getRecipe($recipe_id);
        $data['summary_bar'] = $this->model->getSummaryBarInfo($recipe_id);
        $data['ingredient_list'] = $this->model->getRecipeIngredients($recipe_id);
        $data['step_list'] = $this->model->getRecipeSteps($recipe_id);

        return ['View', $data];
    }

    /* change units across the app */
    public function actionChangeunits()
    {
        $this->model->swithcUnits();
        return $this->actionDefault();
    }

    public function actionSetjsonvariable()
    {
        $info = $this->getMenuId();
        $info = explode('--', $info);

        if (isset($info[1])) {
            $var_name = $info[0];
            $key = $info[1];
            $this->model->sessionSet($var_name, $key);
        }

        $this->no_output = true;

        return true;
    }

    private function setRecipeID()
    {
        if (stristr($this->getMenuId(), 'calendar_item')) {
            $calendar_item_id = str_replace('calendar_item-', '', $this->getMenuId());
            $this->model->sessionSet('recipe_id', $this->model->getRecipeIDFromCalendar($calendar_item_id));
            $this->model->sessionSet('calendar_item_id', $this->model->getRecipeIDFromCalendar($calendar_item_id));
        } elseif (is_numeric($this->getMenuId())) {
            $this->model->sessionSet('recipe_id', $this->getMenuId());
        }
    }

}