<?php

namespace packages\actionMitems\themes\uiKit\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMitems\themes\uiKit\Models\Model as ArticleModel;

class Statistics extends BootstrapController
{
    /* @var ArticleModel */
    public $model;

    public $data = [];

    public function actionDefault()
    {
        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        $page = isset($_REQUEST['next_page_id']) ? $_REQUEST['next_page_id'] : 1;

        $visits = $this->model->getVisits(20, $page);

        if ($visits) {
            $visits = $this->enrichVisits($visits);
        }

        $this->data['visits'] = $visits;
        $this->data['selected_countries'] = $this->getSelectedCountries();

        return ['Statistics', $this->data];
    }

    public function actionFilterData()
    {

        if ($time_to = $this->model->getSubmittedVariableByName('filter_time_to')) {
            $this->model->saveVariable('filter_time_to', $time_to);
        }

        if ($time_from = $this->model->getSubmittedVariableByName('filter_time_from')) {
            $this->model->saveVariable('filter_time_from', $time_from);
        }

        if ($submitted_countries = $this->model->getSubmittedVariableByName('filter_select_country')) {

            $pieces = explode(',', $submitted_countries);

            foreach ($pieces as $country) {
                if ($country AND strlen($country) > 3) {
                    $this->model->addToVariable('filter_select_country', $country);
                }
            }
        }

        return self::actionDefault();
    }

    public function actionExport()
    {
        $this->model->exportVisits();

        $this->data['export_generated'] = true;

        return self::actionDefault();
    }

    public function actionRemoveCountry()
    {
        $country_to_remove = $this->getMenuId();
        $this->model->removeFromVariable('filter_select_country', $country_to_remove);

        return self::actionDefault();
    }

    private function enrichVisits(array $visits)
    {
        foreach ($visits as $i => $visit) {
            $visits[$i]->extra_data = \AeplayVariable::getArrayOfPlayvariables($visit->play_id);
        }

        return $visits;
    }

    private function getSelectedCountries()
    {
        $countries = $this->model->getSavedVariable('filter_select_country');

        if (empty($countries)) {
            return [];
        }

        return @json_decode($countries, true);
    }

}