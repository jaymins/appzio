<?php

namespace backend\modules\fitness\controllers;

use backend\modules\fitness\models\AeExtFitProgram;
use backend\modules\fitness\models\AeExtFitProgramRecipe;
use backend\modules\fitness\search\AeExtFitProgramExercise;
use Yii;
use yii\helpers\Url;

/**
 * This is the class for controller "AeExtFitProgramController".
 */
class AeExtFitProgramController extends \backend\modules\fitness\controllers\base\AeExtFitProgramController
{

    /**
     * @return mixed|string|\yii\web\Response
     */
    public function actionCreate()
    {
        if (!isset($_GET['cat']) and empty($_GET['cat'])) {
            return $this->redirect(['index']);
        }

        $type = $_GET['cat'];

        $model = new AeExtFitProgram;

        $model->program_type = ($type == 'challenge' || $type == 'food') ? $type : 'fitness';
        $model->program_sub_type = ($type == 'challenge' || $type == 'food') ? 'weekly_based' : $type;

        if ($model->program_type == 'food') {
            $model->category_id = 7; // Defaults to Nutrition category
        }

        $session = \Yii::$app->session;

        if (isset($session['app_id'])) {
            $model->app_id = $session['app_id'];
        }

        if (Yii::$app->request->post() && $model->load($_POST)) {
            if ($model->program_type == 'food') {
                $this->insertFoodEntries($model);
            }

            if ($model->program_sub_type == 'weekly_based' && $model->program_type != 'food') {
                $this->insertFitnessEntries($model);
            }
            if ($model->program_sub_type == 'non_weekly_based' && $model->program_type != 'food') {
                $this->insertNoneWeeklyEntries($model);
            }
        }

        if ($model->program_type == 'food') {

            return $this->render('create_food_program', [
                'model' => $model,
                'weeks' => $this->getWeeks(),
                'recipes' => $model->getAllRecipes(),
                'exercise' => [],
                'exercise_json' => [],
                'recipes_json' => []
            ]);

        } elseif ($model->program_sub_type == 'weekly_based' && $model->program_type != 'food') {

            return $this->render('create', [
                'model' => $model,
                'weeks' => $this->getWeeks(),
                'recipes' => [],
                'exercise' => $model->getAllExercises(),
                'exercise_json' => [],
                'recipes_json' => []
            ]);

        } elseif ($model->program_sub_type == 'non_weekly_based') {
            return $this->render('create', [
                'model' => $model,
                'weeks' => false,
                'recipes' => [],
                'exercise' => $model->getAllExercises(),
                'exercise_json' => [],
                'recipes_json' => []
            ]);
        }

    }

    /**
     * @param string $id
     * @return mixed|string|\yii\web\Response
     * @throws \yii\web\HttpException
     */
    public function actionUpdate($id)
    {

        $model = $this->findModel($id);
        $type = $model->program_type;

        if (Yii::$app->request->post() && $model->load($_POST)) {

            if ($model->program_type == 'food') {
                $this->insertFoodEntries($model, true);
            }

            if ($model->program_sub_type == 'weekly_based' && $model->program_type != 'food') {
                $this->insertFitnessEntries($model, true);
            }

            if ($model->program_sub_type == 'non_weekly_based' && $model->program_type != 'food') {
                $this->insertNoneWeeklyEntries($model, true);
            }

        } else {

            if ($model->program_type == 'food') {

                $food_relations = [];
                foreach ($this->getWeeks() as $week => $name) {
                    $food_relations[$week] = $this->getFormattedRelations(AeExtFitProgramRecipe::getRelationsByID($id, $week), 'food',$model->exercises_per_day);
                }

                return $this->render('update', [
                    'model' => $model,
                    'weeks' => $this->getWeeks(),
                    'recipes' => $model->getAllRecipes(),
                    'exercise' => [],
                    'exercise_json' => [],
                    'recipes_json' => $food_relations

                ]);

            } elseif ($model->program_sub_type == 'weekly_based' && $model->program_type != 'food') {

                $exercise_relations = [];
                foreach ($this->getWeeks() as $week => $name) {
                    $exercise_relations[$week] = $this->getFormattedRelations(AeExtFitProgramExercise::getRelationsByID($id, $week), 'fitness',$model->exercises_per_day);
                }

                return $this->render('update', [
                    'model' => $model,
                    'weeks' => $this->getWeeks(),
                    'recipes' => [],
                    'exercise' => $model->getAllExercises(),
                    'exercise_json' => $exercise_relations,
                    'recipes_json' => []
                ]);
            } elseif ($model->program_sub_type == 'non_weekly_based' && $model->program_type != 'food') {
                $exercise_relations = $this->getFormattedRelations(AeExtFitProgramExercise::getRelationsByID($id, false), $type,$model->exercises_per_day);

                return $this->render('update', [
                    'model' => $model,
                    'weeks' => false,
                    'recipes' => [],
                    'exercise' => $model->getAllExercises(),
                    'exercise_json' => $exercise_relations,
                    'recipes_json' => []
                ]);
            }
        }

    }

    private function getFormattedRelations(array $relations, string $type,$exercises_per_day=false)
    {
        $output = [];
        $day_array = [];

        if(isset($exercises_per_day)){
            $days[] = 'Monday';
            $days[] = 'Tuesday';
            $days[] = 'Wednesday';
            $days[] = 'Thursday';
            $days[] = 'Friday';
            $days[] = 'Saturday';
            $days[] = 'Sunday';

            foreach ($days as $day){
                for($i = 1; $i < $exercises_per_day+1; $i++){
                    $day_array[] = $day;
                }
            }
        }

        if ($type == 'fitness' || $type == 'challenge' || $type = 'mindfulness') {

            if (empty($relations)) {
                return [];
            }
            foreach ($relations as $i => $relation) {
                $output[$i]['field-day'] = is_array($day_array) && !empty($day_array) ? array_shift($day_array) : '';
                $output[$i]['field-input-id'] = $relation->id;
                $output[$i]['field-select-relation-id'] = (isset($relation->exercise_id)) ? $relation->exercise_id : $relation->recipe_id;
                $output[$i]['field-input-duration'] = (isset($relation->time)) ? $relation->time : null;
                $output[$i]['week'] = $relation->week;
            }

        } elseif ($type == 'food') {

            if (empty($relations)) {
                return [];
            }


            foreach ($relations as $i => $relation) {
                $output[$i]['field-day'] = is_array($day_array) && !empty($day_array) ? array_shift($day_array) : '';
                $output[$i]['field-input-id'] = $relation->id;
                $output[$i]['field-select-relation-id'] = $relation->recipe_id;
                $output[$i]['week'] = $relation->week;
            }
        } else {
            return [];
        }

        return json_encode($output);
    }

    /**
     * Return an array of weeks
     *
     * @return array
     */
    private function getWeeks()
    {
        $weeks = [];
        for ($i = 1; $i <= 8; $i++) {
            $weeks[$i] = 'Week ' . $i;
        }
        return $weeks;
    }

    private function insertFoodEntries(AeExtFitProgram $model, $update = false)
    {
        $relations_data = [];

        foreach ($this->getWeeks() as $week_key => $week) {
            if (!Yii::$app->request->post('recipe-group-' . $week_key)) {
                continue;
            }

            $relations_data[] = $this->buildRelationsData('recipe-group-', $week_key);
        }

        if ($model->save()) {
            if (!empty($relations_data)) {
                AeExtFitProgramRecipe::addOrUpdateRelations($relations_data, $model->id, $update);
            }
        }

        if ($update) {
            return $this->redirect(Url::previous());
        } else {
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    private function insertNoneWeeklyEntries(AeExtFitProgram $model, $update = false)
    {
        if (Yii::$app->request->post('field_program_exercise')) {
            $relations_data = $this->buildRelationsData('field_program_exercise', null);
            if ($model->save()) {
                if (!empty($relations_data)) {
                    AeExtFitProgramExercise::addOrUpdateRelations($relations_data, $model->id, $update, 'daily');
                }
            }
        }

        if ($update) {
            return $this->redirect(Url::previous());
        } else {
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    private function insertFitnessEntries(AeExtFitProgram $model, $update = false)
    {
        $relations_data = [];

        foreach ($this->getWeeks() as $week_key => $week) {
            if (!Yii::$app->request->post('field_program_exercise_' . $week_key)) {
                continue;
            }

            $relations_data[$week_key] = $this->buildRelationsData('field_program_exercise_', $week_key);
        }

        if ($model->save()) {
            if (!empty($relations_data)) {
                AeExtFitProgramExercise::addOrUpdateRelations($relations_data, $model->id, $update);

                if ($update) {
                    return $this->redirect(Url::previous());
                } else {
                    return $this->redirect(['view', 'id' => $model->id]);
                }

            }
        }

        return true;
    }

    private function buildRelationsData(string $post_param, $week_key)
    {
        $i = 0;
        $data = [];

        if (!$week_key) {
            $post_data = Yii::$app->request->post($post_param);
        } else {
            $post_data = Yii::$app->request->post($post_param . $week_key);
        }

        foreach ($post_data as $key => $row) {
            $i++;

            if (!isset($row['field-select-relation-id']) OR empty($row['field-select-relation-id'])) {
                continue;
            }

            $map = [
                'field-select-relation-id' => $row['field-select-relation-id'],
                'field-input-week' => $week_key,
                'field-input-priority' => $i,
                'field-input-id' => $row['field-input-id']
            ];

            if (isset($row['field-input-duration'])) {
                $map['field-input-duration'] = $row['field-input-duration'];
            }

            $data[] = $map;
        }

        return $data;
    }

}