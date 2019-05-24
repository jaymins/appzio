<?php

namespace packages\actionMshopping\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMshopping\Views\View as ArticleView;
use packages\actionMshopping\Models\Model as ArticleModel;

class Events extends BootstrapController
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

    /**
     * This is the default action inside the controller. This function gets called, if
     * nothing else is defined for the route
     *
     * @return array
     */
    public function actionDefault()
    {

	    $this->model->rewriteActionConfigField('background_color', '#ffffff');
	
	    $events = $this->model->getRemindersByType();
	    
	    $data = [
	    	'events' => $events,
	    ];

        return ['Events', $data];
    }

}