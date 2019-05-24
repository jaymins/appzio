<?php

namespace packages\actionMitems\themes\uiKit\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMitems\themes\uiKit\Models\Model as ArticleModel;

class Statisticstop extends BootstrapController
{
    /* @var ArticleModel */
    public $model;

    public function actionDefault()
    {
        $data = [];

        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        $get_top_users = $this->model->getTopVisits('play_id');

        if ($get_top_users) {
            $get_top_users = $this->enrichVisits($get_top_users);
        }

        $get_top_country = $this->model->getTopVisits('country');

        $data['top_countries'] = $get_top_country;
        $data['top_users'] = $get_top_users;

        return ['Statisticstop', $data];
    }

    public function actionSaveFilterMonth()
    {
        if ($month = $this->model->getSubmittedVariableByName('filter_month')) {
            $this->model->saveVariable('filter_month', $month);
        }

        return self::actionDefault();
    }

    public function actionSaveFilterYear()
    {
        if ($year = $this->model->getSubmittedVariableByName('filter_year')) {
            $this->model->saveVariable('filter_year', $year);
        }

        return self::actionDefault();
    }

    private function enrichVisits(array $visits)
    {
        foreach ($visits as $i => $visit) {
            $visits[$i]['user_details'] = \AeplayVariable::getArrayOfPlayvariables($visit['play_id']);
        }

        return $visits;
    }

}