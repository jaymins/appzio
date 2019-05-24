<?php

namespace packages\actionMvenues\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMvenues\Views\View as ArticleView;
use packages\actionMvenues\Models\Model as ArticleModel;

class Clubs extends BootstrapController
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

        $data = [];
        $data['saved'] = 0;
        if ($this->getMenuId() == 'select_football_club') {
            $clubId = $this->model->getSubmittedVariableByName('selectedFootballClub');


            if ($clubId) {
                $this->model->saveVariable('selected_football_club', $clubId);

                $data['saved'] = 1;
            }

        }

        $data['selectedFootballClub'] = $this->model->getSavedVariable('selected_football_club');
        $data['footballClubs'] = $this->model->getFootballClubs();

        return ['FootballClubs', $data];
    }


}
