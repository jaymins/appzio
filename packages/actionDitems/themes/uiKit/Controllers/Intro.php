<?php

namespace packages\actionDitems\themes\uiKit\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionDitems\Models\Model as ArticleModel;

class Intro extends BootstrapController
{
    /* @var ArticleModel */
    public $model;

    public function actionDefault()
    {
        $seen = false;

        if ($this->model->getMenuId() === 'visit_opened') {
            $this->model->saveVariable('visit_email_sent', 1);
            $seen = true;
        } else if ($this->model->getMenuId() === 'note_opened') {
            $this->model->saveVariable('note_email_sent', 1);
            $seen = true;
        }

        if ($this->model->actionobj->permaname == 'visitemailintro') {
            $view = 'VisitEmailIntro';
        } else if ($this->model->actionobj->permaname == 'noteemailintro') {
            $view = 'NoteEmailIntro';
        }else if ($this->model->getSavedVariable('logged_in')) {
            $view = 'Intro';
        } else {
            $view = 'AppIntro';
        }

        return [$view, [
            'seen' => $seen
        ]];
    }
}
