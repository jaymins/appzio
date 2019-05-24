<?php

namespace packages\actionMitems\themes\fanshop\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionMitems\Models\ItemModel;
use packages\actionMitems\Models\Model as ArticleModel;
use packages\actionMitems\Models\ItemCategoryModel;

class Edit extends BootstrapController
{
    /* @var ArticleModel */
    public $model;

    const MENU_EDIT_ITEM = 'edit_item';
    const MENU_ADD_TAG_EDIT = 'add_tag_edit';
    const MENU_DELETE_TAG = 'delete_tag';

    public function actionDefault()
    {
        $itemId = $this->getMenuId();
        $data = array();
        $tags = null;

        if (strstr($this->getMenuId(), self::MENU_ADD_TAG_EDIT)) {
            $itemId = str_replace(self::MENU_ADD_TAG_EDIT . '_', '', $this->getMenuId());
            $tagContent = $this->model->getSubmittedVariableByName('tag');

            $tags = empty($this->model->sessionGet('tags')) ?
                array() : $this->model->sessionGet('tags');

            if (!empty($tagContent)) {
                $tagContent = explode(' ', $tagContent);
                foreach ($tagContent as $tag) {
                    $tags[] = $tag;
                }
                $this->model->sessionSet('tags', $tags);
            }
        }

        if (strstr($this->getMenuId(), self::MENU_DELETE_TAG)) {
            $itemId = $this->model->sessionGet('item_id');
            $tagToDelete = str_replace(self::MENU_DELETE_TAG . '_', '', $this->getMenuId());

            $tags = empty($this->model->sessionGet('tags')) ?
                array() : $this->model->sessionGet('tags');

            $tags = array_filter($tags, function($tag) use($tagToDelete) {
                return strtolower($tag) !== $tagToDelete ? 1 : 0;
            });

            $this->model->sessionSet('tags', $tags);
        }

        if (strstr($this->getMenuId(), self::MENU_EDIT_ITEM)) {
            $itemId = str_replace(self::MENU_EDIT_ITEM . '_', '', $this->getMenuId());
            $item = $this->model->getItem((int)$itemId);

            $itemTags = array_map(function($tag) { return $tag->name; }, $item->tags);
            $tags = empty($this->model->sessionGet('tags')) ? $itemTags : $this->model->sessionGet('tags');
            $this->model->validateInput();

            if (!empty($this->model->validation_errors)) {
                // prefill data to be shown in the form
                $variables = $this->model->getAllSubmittedVariablesByName();
                $presetData = $this->model->fillPresetData($variables);

                foreach($item as $key=>$value){
                    $presetData[$key] = $value;
                }

                return ['Create', [
                    'tags' => $tags,
                    'presetData' => $presetData
                ]];
            }

            $variables = $this->model->getAllSubmittedVariablesByName();
            $images = $this->model->getItemImages();

            $category = ItemCategoryModel::model()->find('name = :name', array(
                ':name' => $this->model->getSubmittedVariableByName('category')
            ));


            $item->name = $variables['name'];
            $item->description = $variables['description'];
            $item->price = $variables['price'];
            $item->time = isset($variables['time']) ? $variables['time'] : time();
            $item->images = json_encode($images);
            $item->category_id = $category->id;
            $item->save();

            $tagIds = $this->model->saveAndGetTagIds($tags);
            $this->model->saveItemAndTagRelation($item->id, $tagIds);

            $item->tags = array_map(function($tag) {
                $item = new \stdClass();
                $item->name = $tag;

                return $item;
            }, $tags);

            $item->owner = \AeplayVariable::getArrayOfPlayvariables($item->play_id);

            return ['View', [
                'item' => $item
            ]];
        }

        $itemId = (int) $itemId;

        if($itemId > 0){
            $item = $this->model->getItem($itemId);
            $this->model->sessionSet('item_id', $itemId);

            if (is_null($tags)) {
                $tags = array_map(function($tag) {
                    return $tag->name;
                }, $item->tags);

                $this->model->sessionSet('tags', $tags);
            }

            if (strstr($this->getMenuId(), self::MENU_ADD_TAG_EDIT) ||
                strstr($this->getMenuId(), self::MENU_DELETE_TAG)) {
                $variables = $this->model->getAllSubmittedVariablesByName();
                $data['presetData'] = $this->model->fillPresetData($variables);
                $data['presetData']['tags'] = $tags;
                $data['tags'] = $tags;
                $data['mode'] = 'edit';
                $data['presetData']['id'] = $item->id;
            } else {
                $this->model->loadItemImages(json_decode($item->images,true));
                $data['mode'] = 'edit';
                $data['tags'] = $tags;
                $data['presetData']['id'] = $item->id;
                $data['presetData']['name'] = $item->name;
                $data['presetData']['price'] = $item->price;
                $data['presetData']['time'] = $item->time;
                $data['presetData']['description'] = $item->description;
                $data['presetData']['images'] = json_decode($item->images);
                $data['presetData']['category'] = $this->model->getCategoryPath($item->category_id);
            }
        }

        return ['Create', $data];
    }

    public function actionDelete(){

        if($this->getMenuId() AND is_numeric($this->getMenuId())){
            ItemModel::model()->deleteAllByAttributes(array('id' => $this->getMenuId(), 'play_id'=>$this->playid));
        }

        $this->no_output = true;
    }
}