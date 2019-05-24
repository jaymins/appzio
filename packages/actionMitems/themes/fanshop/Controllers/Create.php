<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMitems\themes\fanshop\Controllers;

use packages\actionMitems\Views\View as ArticleView;
use packages\actionMitems\themes\fanshop\Models\Model as ArticleModel;

class Create extends \packages\actionMitems\Controllers\Create {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function actionDefault()
    {

        $this->model->setBackgroundColor();

        if ($this->getMenuId() == 'new') {
            // new view was opened, clear tags from the session
            $this->model->sessionSet('tags', null);
            $this->model->clearImageVariables();
        }

        if ($this->getMenuId() === self::MENU_SAVE_ITEM) {
            return $this->itemSaving();
        }

        if ($this->getMenuId() === 'update_item') {
            return $this->itemSaving();
        }


        // if there are no tags stored in the session return an empty array
        $tags = empty($this->model->sessionGet('tags')) ?
            array() : $this->model->sessionGet('tags');

        $tagContent = $this->model->getSubmittedVariableByName('tag');

        if (!empty($tagContent)) {
            $original = $tagContent;

            if(stristr($original,'#')){
                $tagContent = explode('#', $original);
            } else {
                $tagContent = explode(' ', $original);
            }

            foreach ($tagContent as $tag) {
                if(strlen($tag) > 1){
                    $tags[] = $tag;
                }
            }

            if(isset($tags)){
                $this->model->sessionSet('tags', array_unique($tags));
            }
        }

        if (strstr($this->getMenuId(), self::MENU_DELETE_TAG)) {
            $this->itemDelete();
        }

        // prefill data to be shown in the form
        $variables = $this->model->getAllSubmittedVariablesByName();
        $presetData = $this->model->fillPresetData($variables);

        return ['Create', [
            'tags' => array_unique($tags),
            'presetData' => $presetData
        ]];
    }

    public function actionDeleteTag(){

        $id = $this->getMenuId();
        if(is_numeric($id)){
            $tags = $this->model->sessionGet('tags');

            if(isset($tags[$id])){
                unset($tags[$id]);
            }

            $this->model->sessionSet('tags', $tags);
        }

        return $this->actionDefault();
    }


    public function itemSaving(){
        $tags = $tags = empty($this->model->sessionGet('tags')) ?
            array() : $this->model->sessionGet('tags');

        if($this->getMenuId() == 'update_item'){
            $variables = $this->model->getAllSubmittedVariablesByName();
            $presetData = $this->model->fillPresetData($variables);

            return ['Create', [
                'tags' => $tags,
                'presetData' => $presetData
            ]];

        }

        $this->model->validateInput();

        if (!empty($this->model->validation_errors)) {
            // prefill data to be shown in the form
            $variables = $this->model->getAllSubmittedVariablesByName();
            $presetData = $this->model->fillPresetData($variables);

            return ['Create', [
                'tags' => $tags,
                'presetData' => $presetData
            ]];
        }

        // form was submitted save item in storage
        $images = $this->model->getItemImages();
        $item = $this->model->saveItem($images, $tags,'shop');
        $isLiked = $this->model->isItemLiked($item->id);

        $this->model->sessionSet('tags', null);
        $this->model->clearImageVariables();
        $this->router->menuid = null;

        return ['View', [
            'item' => $item,
            'isLiked' => $isLiked
        ]];

    }

    public function itemAdding(){
        // the user is adding a tag, the page needs to be refreshed
        $tagContent = $this->model->getSubmittedVariableByName('tag');

        if (!empty($tagContent)) {
            $tagContent = explode(' ', $tagContent);
            foreach ($tagContent as $tag) {
                $tags[] = $tag;
            }
            if(isset($tags)){
                $this->model->sessionSet('tags', $tags);
            }
        }

    }

    public function itemDelete(){
        $tagToDelete = str_replace(self::MENU_DELETE_TAG . '_', '', $this->getMenuId());

        $tags = empty($this->model->sessionGet('tags')) ?
            array() : $this->model->sessionGet('tags');

        $tags = array_filter($tags, function($tag) use($tagToDelete) {
            return strtolower($tag) !== $tagToDelete ? 1 : 0;
        });

        $this->model->sessionSet('tags', $tags);
    }

}
