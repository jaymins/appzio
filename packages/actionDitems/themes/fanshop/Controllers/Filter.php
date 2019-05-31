<?php

namespace packages\actionDitems\themes\fanshop\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionDitems\Models\Model as ArticleModel;
use packages\actionDitems\Models\ItemCategoryModel;

class Filter extends BootstrapController
{
    /* @var ArticleModel */
    public $model;

    const SAVE_FILTERS = 'save-filters';
    const SAVE_FILTER_TAG = 'save-filter-tag';
    const MENU_DELETE_TAG = 'delete_tag';

    public function actionDefault(){

        $filters = $this->model->getUserFilters();


        if (!$this->getMenuId()) {
            $this->model->sessionSet('filter_tags', null);
        }

        if($this->getMenuId() == 'clear-filters'){
            $this->model->clearUserFilters();
        }

        if ($this->getMenuId() == self::SAVE_FILTERS) {
            $filters = $this->model->saveItemFilters();
        }

        if ($this->getMenuId() == self::SAVE_FILTER_TAG) {
            $tag = $this->model->getSubmittedVariableByName('tag');

            $tags = empty($this->model->sessionGet('filter_tags')) ?
                array() : $this->model->sessionGet('filter_tags');

            $tags[] = $tag;

            $this->model->sessionSet('filter_tags', $tags);
        }

        if (strstr($this->getMenuId(), self::MENU_DELETE_TAG)) {
            $tagToDelete = str_replace(self::MENU_DELETE_TAG . '_', '', $this->getMenuId());

            $tags = empty($this->model->sessionGet('filter_tags')) ?
                array() : $this->model->sessionGet('filter_tags');

            $tags = array_filter($tags, function($tag) use($tagToDelete) {
                return strtolower($tag) !== $tagToDelete ? 1 : 0;
            });

            $this->model->sessionSet('filter_tags', $tags);
            $filters->tags = $tags;
            return ['Filter', [
                'filters' => $filters,
                'categories' => $this->model->getHierarchicalCategoryList()
            ]];
        }

        if (!empty($filters)) {
            $filters->tags = empty($this->model->sessionGet('filter_tags')) ?
                json_decode($filters->tags) : $this->model->sessionGet('filter_tags');
        }

        return ['Filter', [
            'filters' => $filters,
            'categories' => $this->model->getHierarchicalCategoryList()
        ]];
    }
}