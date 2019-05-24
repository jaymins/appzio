<?php

namespace packages\actionMitems\themes\uiKit\Controllers;

use packages\actionMarticles\Models\ArticlecategoriesModel;
use packages\actionMitems\themes\uiKit\Models\Model as ArticleModel;

class Listing extends \packages\actionMitems\Controllers\Listing
{
    /* @var ArticleModel */
    public $model;

    /**
     * Default action entry point.
     *
     * @return array
     */
    public function actionDefault()
    {
        $this->model->setBackgroundColor();
        $this->model->sessionSet('itemId', '');

        if ($this->model->actionobj->permaname == 'notelist') {
            $items = $this->model->getUserItemsByType('note');
            $this->model->saveVariable('open_div', null);

            return ['Listingnote', array(
                'items' => $items
            )];
        }

        $items = array_map(function ($item) {
            return array(
                'id' => $item->id,
                'text' => $item->name,
                'info' => count($item->reminders) . ' To-Dos',
                'additional_info' => date('M d, Y', $item->date_added)
            );
        }, $this->model->getUserItemsByType('visit'));

        $category = ArticlecategoriesModel::model()->findByAttributes(array(
            'title' => 'Performance Dialogs'
        ));

        $this->model->saveVariable('go_to_tab', null);

        // TODO: Change the logic so tags are setup upon creating something
        // $this->model->saveVariable('current_tags', [$this->model->getSavedVariable('current_country')]);

        $this->model->saveVariable('temp_date', null);

        return ['Listing', array(
            'items' => $items,
            'category_id' => ((is_object($category) AND isset($category->id)) ? $category->id : '0'),
        )];
    }

}