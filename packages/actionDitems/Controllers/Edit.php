<?php

namespace packages\actionDitems\Controllers;

use Bootstrap\Controllers\BootstrapController;
use packages\actionDitems\Models\ItemCategoryRelationModel;
use packages\actionDitems\Models\ItemTagModel;
use packages\actionDitems\Models\ItemTagRelationModel;
use packages\actionDitems\Models\Model as ArticleModel;
use packages\actionDitems\Models\ItemCategoryModel;

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
                $presetData['id'] = $item->id;

                return ['Create', [
                    'mode' => 'edit',
                    'tags' => $tags,
                    'presetData' => $presetData
                ]];
            }

            $variables = $this->model->getAllSubmittedVariablesByName();
            $images = $this->model->getItemImages();

            $item->name = $variables['name'];
            $item->description = $variables['description'];
            if (isset($variables['price'])) {
                $item->price = $variables['price'];
            }
            $item->time = $variables['time'];
            $item->images = json_encode($images);
            $item->save();

            ItemTagRelationModel::model()->deleteAll('item_id = :itemId', array(
                ':itemId' => $item->id
            ));

            $tagIds = $this->model->saveAndGetTagIds($tags);
            $this->model->saveItemAndTagRelation($item->id, $tagIds);

            ItemCategoryRelationModel::model()->deleteAll('item_id = :itemId', array(
                ':itemId' => $item->id
            ));

            $categoryIds = $this->model->getSubmittedCategoryIds();
            $this->model->saveItemAndCategoryRelation($item->id, $categoryIds);

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
            $data['mode'] = 'edit';
            $data['tags'] = $tags;
            $data['presetData']['id'] = $item->id;
            $data['presetData']['name'] = $item->name;
            if (isset($item->price)) {
                $data['presetData']['price'] = $item->price;
            }
            $data['presetData']['time'] = $item->time;
            $data['presetData']['description'] = $item->description;
            $data['presetData']['images'] = json_decode($item->images);
            $data['presetData']['categories'] = $item->categories;
        }

        return ['Create', $data];
    }
}