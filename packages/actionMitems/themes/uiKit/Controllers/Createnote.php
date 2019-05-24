<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMitems\themes\uiKit\Controllers;

use packages\actionMitems\Models\ItemRemindersModel;
use packages\actionMitems\themes\uiKit\Models\Model as ArticleModel;
use packages\actionMitems\themes\uiKit\Views\View as ArticleView;

class Createnote extends Createvisit
{

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function actionDefault()
    {
        // Set a default tag
        $this->model->setDefaultTag();

        return ['Createnote'];
    }

    public function actionSaveNote()
    {
        $itemId = null;
        if (strstr($this->getMenuId(), 'edit_')) {
            $itemId = str_replace('edit_', '', $this->getMenuId());
        }

        $this->model->setBackgroundColor();

        $this->model->validateInput();

        if (!empty($this->model->validation_errors)) {
            // prefill data to be shown in the form
            $variables = $this->model->getAllSubmittedVariablesByName();
            $presetData = $this->model->fillPresetData($variables);

            return ['Createnote', [
                'presetData' => $presetData
            ]];
        }

        $this->model->editId = $itemId;

        $item = $this->model->saveItem(
            $this->model->getSubmittedImages(),
            $this->model->getSubmittedTags(),
            'note'
        );

        if ($item == null) {
            $this->no_output = 1;
            return false;
        }

        $this->model->sessionSet('itemId', '');
        $this->model->clearTemporaryData();

        // TODO: Check if this is really needed
        // $this->model->clearImageVariables();

        $this->model->sessionSet('item_id_' . $this->model->action_id, $item->id);

        return ['Redirect', [
            'action' => 'viewnote',
            'item_id' => $item->id,
            'tab_to_open' => '1',
        ]];
    }

}