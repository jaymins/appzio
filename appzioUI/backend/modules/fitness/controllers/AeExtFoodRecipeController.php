<?php

namespace backend\modules\fitness\controllers;

use backend\components\Helper;
use backend\modules\fitness\models\AeExtFoodRecipe;
use backend\modules\fitness\models\AeExtFoodRecipeIngredient;
use backend\modules\fitness\models\AeExtFoodRecipeStep;
use Yii;
use yii\helpers\Url;

/**
 * This is the class for controller "AeExtFoodRecipeController".
 */
class AeExtFoodRecipeController extends \backend\modules\fitness\controllers\base\AeExtFoodRecipeController
{
    public function actionCreate()
    {
        $model = new AeExtFoodRecipe;

        try {
            if ($model->load($_POST)) {

                $model = Helper::addImageUploads($model, 'AeExtFoodRecipe');

                if ($model->save()) {
                    $ex_id = $model->id;

                    if ($ingredients = Yii::$app->request->post('ingredients-group')) {
                        AeExtFoodRecipeIngredient::addOrUpdateRelations($ingredients, $ex_id, true);
                    }

                    if ($steps = Yii::$app->request->post('steps-group')) {
                        AeExtFoodRecipeStep::addOrUpdateRelations($steps, $ex_id, true);
                    }

                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    $model->load($_GET);
                }

            } else {
                $model->load($_GET);
            }

        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            $model->addError('_exception', $msg);
        }

        return $this->render('create', [
            'model' => $model,
            'ingredients' => AeExtFoodRecipe::getAllIngredients(),
            'steps' => AeExtFoodRecipe::getAllSteps(),
            'ingredients_json' => [],
            'steps_json' => []
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post('AeExtFoodRecipe');
        if ($post['photo']) {
            $photo = Helper::addImageUploads($model, 'AeExtFoodRecipe');
        } elseif ($model->photo) {
            $photo = $model->photo;
        } else {
            $photo = '';
        }

        if ($model->load($_POST)) {
            $model->photo = $photo;
            $model = Helper::addImageUploads($model, 'AeExtFoodRecipe');
            if ($model->save()) {
                if ($ingredients = Yii::$app->request->post('ingredients-group')) {
                    $ex_id = $model->id;
                    AeExtFoodRecipeIngredient::addOrUpdateRelations($ingredients, $ex_id, true);
                }
                if ($ingredients = Yii::$app->request->post('steps-group')) {
                    $ex_id = $model->id;
                    AeExtFoodRecipeStep::addOrUpdateRelations($ingredients, $ex_id, true);
                }
                return $this->redirect(Url::previous());
            }
        } else {
            $ingredient_relations = AeExtFoodRecipeIngredient::getRelationsByID($id);
            $steps_relations = AeExtFoodRecipeStep::getRelationsByID($id);
            return $this->render('update', [
                'model' => $model,
                'ingredients' => AeExtFoodRecipe::getAllIngredients(),
                'steps' => AeExtFoodRecipe::getAllSteps(),
                'ingredients_json' => $this->getFormattedRelations($ingredient_relations, 'ingredient'),
                'steps_json' => $this->getFormattedRelations($steps_relations, 'step')
            ]);
        }
        
    }

    private function getFormattedRelations(array $relations, string $rel)
    {
        $output = [];

        if ($rel && $rel == 'ingredient') {
            if (empty($relations)) {
                return [];
            }

            foreach ($relations as $i => $relation) {
                $output[$i]['field-input-id'] = $relation->id;
                $output[$i]['field-select-ingredient'] = $relation->ingredient_id;
                $output[$i]['field-input-quantity'] = $relation->quantity;
            }
        } elseif ($rel && $rel == 'step') {
            if (empty($relations)) {
                return [];
            }

            foreach ($relations as $i => $relation) {
                $output[$i]['field-input-id'] = $relation->id;
                $output[$i]['field-input-step'] = $relation->description;
                $output[$i]['field-input-time'] = $relation->time;
            }
        } else {
            return [];
        }

        return json_encode($output);
    }   

}