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
use packages\actionMfood\Views\Listing as ArticleView;
use packages\actionMfood\Models\Model as FoodModel;

class Shoppinglist extends Controller
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
    public function actionDefault()
    {
        $data = array();

        // TODO: This should go under /sql/variables.php
        $this->model->saveVariable('ingredient_category', 'placeholder');
        $this->model->deleteVariable('ingredient_category');

        $params['from_date'] = $this->model->getSavedVariable('start_date', time());
        $params['to_date'] = $this->model->getSavedVariable('to_date', strtotime('+7 days', time()));
        $data['category'] = $this->model->getIngredientCategory();
        $data['ingredient_list'] = $this->model->getShoppingList($params);
        $data['filters'] = $params;

        return ['Shoppinglist', $data];
    }

    /* apply sorting from the div */
    public function actionComplete()
    {
        $params['from_date'] = $this->model->getSavedVariable('start_date', time());
        $params['to_date'] = $this->model->getSavedVariable('to_date', strtotime('+7 days', time()));
        $data['category'] = $this->model->getIngredientCategory();

        $ingredient_list = $this->model->getShoppingList($params);
        $data['filters'] = $params;
        $ingredient_id = $this->getMenuId();

        if ($ingredient_id) {
            $key = explode('-',$ingredient_id);
            $record = $ingredient_list[$key[0]]['item'][$key[1]];

            if ($record['quantity']>0){
                $data['id'] = ($record['ingredient_id'])?$record['ingredient_id']:$record['custom_id'];
                $data['quantity'] = $record['quantity'];
                $data['date_from'] = $params['from_date'];
                $data['date_to'] = $params['to_date'];
                $this->model->addCompleteRecord($data);
            }else{
                $this->model->removeCompleteRecord($record['complete_id']);
            }
        }
        $data['ingredient_list'] = $this->model->getShoppingList($params);
        return ['Shoppinglist', $data];
    }

    /* async to add temporary category information */
    public function actionSetingredientcategory(){
        $id = $this->getMenuId();
        $this->model->sessionSet('temp_category', $id);
        $this->no_output = true;
        return true;
    }

    /* apply sorting from the div */
    public function actionAdd()
    {

        $name = $this->model->getSubmittedVariableByName('name', false);
        $type = $this->model->sessionGet('temp_category');

        if ($name && $type) {
            $this->model->addCustomIngredient($name, $type);
            $this->model->sessionUnset('temp_category');
        }

        $this->no_output = true;

        return true;
    }


    /* apply category filter */
    public function actionFilter()
    {
        $data = array();

        $params['from_date'] = $this->model->getSavedVariable('start_date', time());
        $params['to_date'] = $this->model->getSavedVariable('to_date', strtotime('+7 days', time()));

        $from_date = $this->model->getSubmittedVariableByName('start_date', $params['from_date']);
        $to_date = $this->model->getSubmittedVariableByName('to_date', $params['to_date']);

        $this->model->saveVariable('start_date', $from_date);
        $this->model->saveVariable('to_date', $to_date);

        $data['ingredient_list'] = $this->model->getShoppingList($params);
        $data['filters'] = $params;

        return ['Shoppinglist', $data];
    }



}