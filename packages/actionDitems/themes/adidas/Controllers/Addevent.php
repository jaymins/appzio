<?php

namespace packages\actionDitems\themes\adidas\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionDitems\Views\View as ArticleView;
use packages\actionDitems\themes\adidas\Models\Model as ArticleModel;

class Addevent extends BootstrapController
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

    public $view_add_event = 'Addevent';
    public $form_var = 'varfootball_match';

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

        if ( !$this->model->getSubmittedVariableByName( $this->form_var ) ) {
            // Trigger some validation here
        }

        $selected_match = $this->model->getSubmittedVariableByName( $this->form_var );

        $match_data = $this->model->getMatchByID( $selected_match );
        $data['venues'] = $this->model->getPlaces();
        
        $this->model->sessionSet('selected_match', $selected_match);

        if ( empty($match_data) ) {
            // Trigger some validation here
        }

        $data['match_data'] = $match_data;

        return [$this->view_add_event, $data];
    }

    public function actionCreate() {

        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        $this->model->validateEvent();

        $data = [];

        if ( $this->model->validation_errors ) {
            $data['errors'] = $this->model->validation_errors;
            return [$this->view_add_event, $data];
        }

        if ( $this->model->saveMatchEvent() ) {
            $data['football_matches'] = $this->model->getFootballMatches();
            return ['Footballmatches', $data];
        }

        return [$this->view_add_event, $data];
    }

}