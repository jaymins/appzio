<?php

namespace packages\actionMfood\Models;
use Bootstrap\Models\BootstrapModel;
use CException;
use Yii;

/* this trait includes all model functionality related
to recipes */


Trait Recipe {

    /**
     * @param $recipe_id
     * @return array|bool|mixed|null
     */
    public function getRecipeInfo($recipe_id){
        // Return recipe info and list of ingredients for it
        if (!$recipe_id) return false;
        $recipesList = RecipeModel::model()->with([
            'step',
            'type'
        ])->findAll();

        return $recipesList;
    }

    /**
     * @param array $filter
     * @param null $order
     * @return array|mixed|null
     */
    public function getRecipesList($filter = [], $order = null){
        // Return filtered list of recipe ordered by $order;
        $recipesList = RecipeModel::model()->with([
            'type',
        ])->findAll($filter);
        return $recipesList;
    }
    
}