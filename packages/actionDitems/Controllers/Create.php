<?php

namespace packages\actionDitems\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionDitems\Models\Model as ArticleModel;

class Create extends BootstrapController
{
    /* @var ArticleModel */
    public $model;

    const MENU_SAVE_ITEM = 'save_item';

    const MENU_ADD_TAG = 'add_tag';

    const MENU_DELETE_TAG = 'delete_tag';

    public $viewName = 'Create';

    public $clearSession = 1;

    public $viewData = array();

    /**
     * Default action for the controller
     *
     * @return array
     */
    public function actionDefault()
    {
        return ['Create'];
    }

    public function saveItem()
    {
        $this->model->validateInput();
        $tags = $tags = empty($this->model->sessionGet('tags')) ?
            array() : $this->model->sessionGet('tags');

        if (!empty($this->model->validation_errors)) {
            // prefill data to be shown in the form
            $variables = $this->model->getAllSubmittedVariablesByName();
            $presetData = $this->model->fillPresetData($variables);

            return [$this->viewName, [
                'tags' => $tags,
                'presetData' => $presetData
            ]];
        }

        // form was submitted save item in storage
        $images = $this->model->getItemImages();
        $item = $this->model->saveItem($images, $tags);

        $this->model->sessionSet('tags', null);
        $this->model->clearImageVariables();
        $this->router->menuid = null;

        return [$this->viewName, [
            'created' => true,
            'item_id' => $item->id
        ]];
    }

    // TODO: refactor this function to not preset data; atm it is doing 2 things
    public function addItemTag($tags)
    {
        // the user is adding a tag, the page needs to be refreshed
        $tagContent = $this->model->getSubmittedVariableByName('tag');

        if (!empty($tagContent)) {
            $tagContent = explode(' ', $tagContent);
            foreach ($tagContent as $tag) {
                if (!empty($tag)) {
                    $tags[] = trim(trim($tag), ',');
                }
            }
            $this->model->sessionSet('tags', $tags);
        }

        // prefill data to be shown in the form
        $variables = $this->model->getAllSubmittedVariablesByName();
        return $this->model->fillPresetData($variables);
    }

    public function deleteItemTag($tags)
    {
        $tagToDelete = str_replace(self::MENU_DELETE_TAG . '_', '', $this->getMenuId());

        $tags = array_filter($tags, function ($tag) use ($tagToDelete) {
            return strtolower($tag) !== $tagToDelete ? 1 : 0;
        });

        $this->model->sessionSet('tags', $tags);

        // prefill data to be shown in the form
        $variables = $this->model->getAllSubmittedVariablesByName();
        return $this->model->fillPresetData($variables);
    }

}