<?php

namespace packages\actionMMarketplace\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMMarketplace\themes\tattoo\Models\Model;

class Create extends BootstrapController
{
    /**
     * @var Model
     */
    public $model;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function actionDefault()
    {
        if (strstr($this->getMenuId(), 'remove_style')) {
            $this->model->removeFromVariable(
                'itemcategory',
                $this->model->getSubmittedVariableByName($this->getMenuId())
            );
        }
        $variables = $this->model->getAllSubmittedVariablesByName();
        $presetData = $this->model->fillPresetData($variables);

        return ['Create', compact('presetData')];

    }

    public function actionSave()
    {

        $this->model->validateInput();

        if (!empty($this->model->validation_errors)) {
            $variables = $this->model->getAllSubmittedVariablesByName();
            $presetData = $this->model->fillPresetData($variables);

            return ['Create', compact('presetData')];
        }

        $this->model->createBidItem();

        $this->model->saveVariable('itempic', '');
        $this->model->saveVariable('itempic2', '');
        $this->model->saveVariable('itempic3', '');
        $this->model->saveVariable('itemcategory', '');

        $redirect = 'marketplace';
        return ['Redirect', compact('redirect')];
    }

}