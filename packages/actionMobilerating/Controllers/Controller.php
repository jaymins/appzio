<?php

namespace packages\actionMobilerating\Controllers;
use Bootstrap\Controllers\BootstrapController;
use packages\actionMobilerating\Views\View as ArticleView;
use packages\actionMobilerating\Models\Model as ArticleModel;

class Controller extends BootstrapController {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;


    /* this is the default action inside the controller. This gets called, if
    nothing else is defined for the route */
    public function actionDefault(){

        if ($this->getMenuId() == 'feedback') {
            $this->sendFeedback();
            return ['View', ['message' => 'Thank you for your feedback!']];
        }

        return ['View', []];
    }

    private function sendFeedback()
    {
        $feedback = $this->model->submitvariables['feedback'];


        $notifications = new \Aenotification();
        $notifications->id_channel = 1;
        $notifications->app_id = $this->model->appid;
        $notifications->play_id = $this->playid;
        $notifications->subject = 'Feedback';
        $notifications->message = $feedback;
        $notifications->email_to = 'alicheema@deseedating.com';
        $notifications->type = 'email';
        $notifications->parameters = false;
        $notifications->insert();
    }
}
