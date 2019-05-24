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

namespace packages\actionMfitness\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMfitness\Models\Model as ArticleModel;
use packages\actionMfitness\Models\ProgramSelectionModel;
use packages\actionMfitness\Views\View as ArticleView;

class Settings extends BootstrapController
{

    /**
     * @var ArticleView
     */
    public $view;

    /**
     * Your model and Bootstrap model methods are accessible through this variable
     * @var ArticleModel
     */
    public $model;

    public $data = [];



    /**
     * This is the default action inside the controller. This function gets called, if
     * nothing else is defined for the route
     * @return array
     */
    public function actionDefault()
    {
        $this->model->rewriteActionConfigField('background_color', '#262626');

        // TODO: figure out when this should be called
        // $this->model->clearTmpStorage();

        $this->data['programs'] = ProgramSelectionModel::getAllUserPrograms($this->playid);
        $this->data['training_field_types'] = $this->trainingFieldTypes();
        $this->data['food_field_types'] = $this->foodFieldTypes();

        return ['Settings', $this->data];
    }

    public function actionStop(){

        $id = $this->getMenuId();

        if($id){
            $this->model->sessionSet('program_setting_delete_flag', $id);
        }

        $this->no_output = true;
        return true;
    }

    public function actionDodelete(){

        $id = $this->model->sessionGet('program_setting_delete_flag');


        if($id){
            $this->model->stopProgram($id);
        }

        return self::actionDefault();
    }



    public function actionStoreSettings()
    {
        $programs_to_update = $this->model->getProgramUpdates();

        if (empty($programs_to_update)) {
            $this->model->validation_errors[] = '{#no_programs_have_been_changed#}';
        }

        if (
            array_column($programs_to_update, 'current_program_food_type') AND
            $this->getMenuId() != 'confirm'
        ) {
            $this->data['confirm_program_edit'] = true;
            return self::actionDefault();
        } else {
            if ($this->model->storeProgramSettings($programs_to_update)) {
                return ['Redirect', [
                    'redirect' => 'schedule'
                ]];
            }
        }

        return self::actionDefault();
    }

    public function actionUpdateProgramWeek()
    {
        $info = $this->getMenuId();
        $info = explode('--', $info);

        if (isset($info[1])) {
            $variable = $info[0];
            $value = $info[1];
            $this->model->sessionSet($variable, $value);
        }

        return self::actionDefault();
    }

    public function actionUpdateProgramReps()
    {
        $info = $this->getMenuId();
        $info = explode('--', $info);

        if (isset($info[1])) {
            $variable = $info[0];
            $value = $info[1];
            $this->model->sessionSet($variable, $value);
        }

        return self::actionDefault();
    }

    public function actionUpdateProgramDays()
    {
        $this->model->storeCheckboxesSelections('training_days');
        return self::actionDefault();
    }

    public function actionUpdateTime()
    {
        $variable = $this->getMenuId();

        $hour = $this->model->getSubmittedVariableByName($variable . '-hour');
        $minute = $this->model->getSubmittedVariableByName($variable . '-minute');

        if ($minute == 0) {
            $minute_val = '00';
        } else {
            $minute_val = $minute;
        }

        $this->model->sessionSet($variable, $hour . ':' . $minute_val);

        return self::actionDefault();
    }

    private function trainingFieldTypes()
    {
        return [
            'training_days_per_week' => [
                'label' => '{#training_days_per_week#}',
                'type' => 'int',
                'div_template' => 'divNumPicker'
            ],
            'training_days' => [
                'label' => '{#select_training_days#}',
                'type' => 'json_single',
                'div_template' => 'divDayPicker'
            ],
            'times' => [
                'label' => '{#training_time#}',
                'type' => 'json_multiple',
                'div_template' => 'divTimePicker'
            ],
        ];
    }

    private function foodFieldTypes()
    {
        return [
            'times' => [
                'label' => '{#meal_time#}',
                'type' => 'json_multiple',
                'div_template' => 'divTimePicker'
            ],
        ];
    }

}