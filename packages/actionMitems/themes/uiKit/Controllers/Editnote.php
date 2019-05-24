<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMitems\themes\uiKit\Controllers;

use packages\actionMitems\themes\uiKit\Models\Model as ArticleModel;
use packages\actionMitems\themes\uiKit\Views\View as ArticleView;

class Editnote extends Createvisit
{

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public $data;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function actionDefault()
    {
        $submit_value = $this->getMenuId();

        if (stristr($submit_value, 'edit-note-')) {
            // Clean the current session storage
            $this->model->clearTemporaryData();

            $itemId = str_replace('edit-note-', '', $submit_value);
            $this->model->sessionSet('current_note_id', $itemId);

            $item = $this->model->getItem($itemId);

            $this->model->getFormattedTags($item->pretty_tags, false);
        } else {
            $itemId = $this->model->sessionGet('current_note_id');
            $item = $this->model->getItem($itemId);

            $this->model->getFormattedTags($item->pretty_tags, true);
        }

        // Setup the image variables
        $this->model->setImageSessionVariables($item);

        $presetData = [
            'name' => $item->name,
            'description' => $item->description,
            'date_added' => $item->date_added
        ];

        $this->viewData['itemId'] = $itemId;
        // $this->viewData['item_tags'] = $tags;
        $this->viewData['presetData'] = $presetData;

        return ['Createnote', $this->viewData];
    }

}