<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionDitems\themes\uiKit\Controllers;

use packages\actionDitems\themes\uiKit\Models\Model as ArticleModel;
use packages\actionDitems\themes\uiKit\Views\View as ArticleView;

class Editvisit extends Createvisit
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

        if (stristr($submit_value, 'edit-visit-')) {

            // Clean the current session storage
            $this->model->clearTemporaryData();

            $itemId = str_replace('edit-visit-', '', $submit_value);

            $item = $this->model->getItem($itemId);

            $this->model->getFormattedTags($item->pretty_tags);
            $this->model->setImageSessionVariables($item);

            $this->model->setupCategories($item->id);

            $presetData = [
                'name' => $item->name,
                'description' => $item->description,
                'date_added' => $item->date_added,
                // 'item_tags' => $tags
            ];

            $this->model->saveVariable('temp_preset', $presetData);
            $this->model->saveVariable('temp_date', $item->date_added);

            $this->model->sessionSet('current_visit_id', $itemId);

        } else {
            $itemId = $this->model->sessionGet('current_visit_id');

            $presetData = json_decode(
                $this->model->getSavedVariable('temp_preset', []),
                true
            );
        }

        $this->viewData['subject'] = '{#edit_visit#}';
        $this->viewData['itemId'] = $itemId;
        $this->viewData['presetData'] = $presetData;

        return ['Create', $this->viewData];
    }

}