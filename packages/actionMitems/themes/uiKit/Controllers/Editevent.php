<?php

namespace packages\actionMitems\themes\uiKit\Controllers;

use packages\actionMitems\themes\uiKit\Models\Model as ArticleModel;

class Editevent extends Addevent
{

    /**
     * Your model and Bootstrap model methods are accessible through this variable
     * @var ArticleModel
     */
    public $model;

    private $event_data;

    /**
     * This is the default action inside the controller. This function gets called, if
     * nothing else is defined for the route
     *
     * @return array
     */
    public function actionDefault()
    {
        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        $cache_key = 'currently_opened_event';

        if ($this->getMenuId() AND is_numeric($this->getMenuId())) {
            $event_id = $this->getMenuId();
            $this->model->sessionSet($cache_key, $event_id);
        } else {
            $event_id = $this->model->sessionGet($cache_key);
        }

        $this->event_data = $this->model->getReminderByID($event_id);

        return ['Addevent', $this->setupEventData()];
    }

    public function actionSaveItem()
    {
        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        $this->model->validateRoutine();

        $data = [];

        if ($this->model->validation_errors) {
            $data = $this->setupEventData();
            return ['Addevent', $data];
        }

        $event_id = $this->model->getSubmittedVariableByName('current_event_id');

        if ($this->model->saveRoutine('update', $event_id)) {
            $events = $this->model->getRemindersByType();

            $data = [
                'events' => $events
            ];

            return ['Events', $data];
        }

        return ['Addevent', $data];
    }

    public function setupEventData()
    {
        return [
            'event_data' => $this->event_data,
            'button_title' => strtoupper('{#update_routine#}'),
            'submit_value' => 'Editevent/SaveItem',
            'event_titles' => $this->getEventTitles(),
        ];
    }

}