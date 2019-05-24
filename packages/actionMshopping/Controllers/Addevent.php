<?php

namespace packages\actionMshopping\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMshopping\Views\View as ArticleView;
use packages\actionMshopping\Models\Model as ArticleModel;

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

	const CREATE_ROUTINE = 'save-routine';

    /**
     * This is the default action inside the controller. This function gets called, if
     * nothing else is defined for the route
     *
     * @return array
     */
    public function actionDefault()
    {

	    $this->model->rewriteActionConfigField('background_color', '#ffffff');

	    if ($this->getMenuId() === self::CREATE_ROUTINE) {
		    return $this->saveItem();
	    }

	    $data = [];
        return [$this->view_add_event, $data];
    }

	public function saveItem() {
		$this->model->validateRoutine();

		$data = [];
		
		if ( $this->model->validation_errors ) {
			return [$this->view_add_event, $data];
		}

		if ( $this->model->saveRoutine() ) {
			$events = $this->model->getRemindersByType();

			$data = [
				'events' => $events
			];

			return ['Events', $data];
		}

		return [$this->view_add_event, $data];
	}

}