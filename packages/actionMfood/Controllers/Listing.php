<?php

/**
 * This example shows a simple registration form. Usually this action would be used in conjuction with
 * Mobilelogin action, which provides login and logout functionalities.
 *
 * Default controller for your action. If no other route is defined, the action will default to this controller
 * and its default method actionDefault() which must always be defined.
 *
 * In more complex actions, you would include different controller for different modes or phases. Organizing
 * the code for different controllers will help you keep the code more organized and easier to understand and
 * reuse.
 *
 * Unless controller has $this->no_output set to true, it should always return the view file name. Data from
 * the controller to view is passed as a second part of the return array.
 *
 * Theme's controller extends this file, so usually you would define the functions as public so that they can
 * be overriden by the theme controller.
 *
 */

namespace packages\actionMfood\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMfood\Models\Model as FoodModel;
use packages\actionMfood\Views\Listing as ArticleView;

class Listing extends BootstrapController
{

    /**
     * @var ArticleView
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
    private $calendar_id;

    public function actionDefault()
    {
        $data = [];
        $categories = @json_decode($this->model->getSavedVariable('categories', []));
        $ingredients = @json_decode($this->model->getSavedVariable('key_ingredients', []));
        $sorting = @json_decode($this->model->getSavedVariable('sorting', ['ASC']));

        if ($this->model->getConfigParam('calendar_replace')) {
            $data['calendar_id'] = $this->model->sessionGet('calendar_item_id');
        }

        $data['selected_ingredients'] = $ingredients;
        $data['selected_categories'] = $categories;
        $data['count_selected_categories'] = count($categories);
        $data['count_selected_ingredients'] = count($ingredients);
        $data['sorting'] = $sorting;
        $data['offset'] = isset($_REQUEST['next_page_id']) ? $_REQUEST['next_page_id'] + 15 : 15;

        $filter['type_id'] = $categories;
        $filter['ingredient_id'] = $ingredients;

        $data['recipe_data'] = $this->model->getNutritionList($filter, $sorting);
        $data['filter_status'] = $filter;
        $data['list_categories'] = $this->model->getRecipeCategories($ingredients);
        $data['list_ingredients'] = $this->model->getListIngredients($categories);

        return ['Listing', $data];
    }

    public function actionAdd()
    {
        $recipe_id = $this->getRecipeId();
        $this->no_output = true;

        if (!$recipe_id) {
            return false;
        }

        $calendar_id = $this->model->getSubmittedVariableByName('calendar_id', null);

        if ($calendar_id) {
            $this->model->editCalendar($calendar_id, $recipe_id);
            return true;
        } else {
            $date = $this->model->getSubmittedVariableByName('food_start_date', time());
        }

        $hour = $this->model->getSubmittedVariableByName('meal_time-hour', '00');
        $minute = $this->model->getSubmittedVariableByName('meal_time-minute', '00');
        $date = strtotime(date('Y-m-d', $date) . ' ' . $hour . ':' . $minute);
        $this->model->addToCalendar($recipe_id, $date);
        return true;
    }

    public function actionUpdate()
    {
        $this->no_output = true;
        $recipe_id = $this->getRecipeId();

        $calendar_id = $this->model->getSubmittedVariableByName('calendar_id', null);

        if (empty($recipe_id) || empty($calendar_id)) {
            return false;
        }

        $this->model->editCalendar($calendar_id, $recipe_id);
        return true;
    }

    private function getRecipeId()
    {
        $id = explode('recipe_', $this->getMenuId());
        if (isset($id[1])) {
            return $id[1];
        } else {
            return false;
        }
    }

    /* apply sorting from the div */
    public function actionSorting()
    {
        $sorting = $this->model->getSubmittedVariableByName('sorting');
        $this->model->saveVariable('sorting', $sorting);
        return $this->actionDefault();
    }

    public function actionSavefilters()
    {
        $this->model->saveFilters();
        return $this->actionDefault();
    }

    /* apply category filter */
    public function actionMeal()
    {
        $categories = $this->getSubmitedList($this->model->getAllSubmittedVariables(), 'meal');
        $this->model->saveVariable('categories', $categories);
        $this->model->deleteVariable('key_ingredients');
        return $this->actionDefault();
    }

    /* apply key ingredients filter */
    public function actionIngredients()
    {
        $ingredients = $this->getSubmitedList($this->model->getAllSubmittedVariables(), 'ingredient');
        $this->model->saveVariable('key_ingredients', $ingredients);
        return $this->actionDefault();
    }

    public function actionReset()
    {
        $this->model->deleteVariable('key_ingredients');
        $this->model->deleteVariable('categories');
        $this->model->deleteVariable('recipe_filter_keyword');
        return $this->actionDefault();
    }

    private function getSubmitedList($list, $var)
    {

        $result = [];
        foreach ($list as $key => $item) {
            $key = explode('-', $key, 2);
            if ($item != '' && $key[0] == $var) {
                $result[] = $item;
            };
        }
        return $result;

    }

}