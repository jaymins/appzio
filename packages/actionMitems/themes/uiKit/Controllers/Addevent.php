<?php

namespace packages\actionMitems\themes\uiKit\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMitems\themes\uiKit\Models\Model as ArticleModel;

class Addevent extends BootstrapController
{

    /**
     * Your model and Bootstrap model methods are accessible through this variable
     * @var ArticleModel
     */
    public $model;

    /**
     * This is the default action inside the controller. This function gets called, if
     * nothing else is defined for the route
     *
     * @return array
     */
    public function actionDefault()
    {
        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        $data = [];
        $data['button_title'] = strtoupper('{#save_routine#}');
        $data['event_titles'] = $this->getEventTitles();
        $data['submit_value'] = 'Addevent/SaveItem';

        return ['Addevent', $data];
    }

    public function actionSaveItem()
    {
        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        $this->model->validateRoutine();

        $data = [];

        $data['submit_value'] = 'Addevent/SaveItem';

        if ($this->model->validation_errors) {
            $data['button_title'] = strtoupper('{#save_routine#}');
            return ['Addevent', $data];
        }

        if ($this->model->saveRoutine()) {
            $events = $this->model->getRemindersByType();

            $data = [
                'events' => $events
            ];

            return ['Events', $data];
        }

        return ['Addevent', $data];
    }

    public function getEventTitles()
    {

        $data = [
            'Visit Performance Dialog',
            'Conduct Gemba Walk',
            'Review NPA Feedback',
            'Review Raised Problems',
            'Review PD Board KPIs',
            'Conduct Problem Solving Session',
            'Praise Continuous Improvement Behaviour'
        ];

        $output = '';

        foreach ($data as $entry) {
            $output .= $entry . ';' . $entry . ';';
        }

        return $output;
    }

}