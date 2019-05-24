<?php

namespace packages\actionMshopping\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMshopping\Components\Components as Components;
use packages\actionMshopping\Models\Model as ArticleModel;

class Create extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public $theme;
    public $margin;
    public $grid;
    public $deleting;
    public $presetData;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    /**
     * Default action entry point
     *
     * @return \stdClass
     */
    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->presetData = $this->getData('presetData', 'array');
        $this->model->setBackgroundColor('#1b1b1b');

        if ($this->getData('created', 'bool')) {
            $this->layout->onload[] = $this->getOnclickGoHome();
            return $this->layout;
        }

        $this->renderImageGrid();

        $this->renderNameRow();

        $this->renderTagsRow();

        $this->renderPriceAndTimeRow();

        $this->renderItemDescription();

        $this->renderStylesList();

        $this->renderSaveButton();

        return $this->layout;
    }

    /**
     * Render a 5 image grid containing the item pictures.
     *
     */
    public function renderImageGrid()
    {
        $images = isset($this->presetData['images']) ?
            $this->presetData['images'] : array();

        $this->model->loadItemImages($images);

        $grid_items = $this->getComponentImageGrid(array(
            'base_variable' => 'itempic'
        ));

        if ( empty($grid_items) ) {
            return false;
        }

        $this->layout->scroll[] = $grid_items;
    }

    /**
     * Renders the item name row
     * 
     * @return void
     */
    public function renderNameRow()
    {
        $value = isset($this->presetData['name']) ? $this->presetData['name'] : '';

        $this->layout->scroll[] = $this->components->getHintedField('{#name_your_art#}', 'name', 'text', array(
            'value' => $value
        ));

        $this->renderValidationErrors('name');
    }

    /**
     * Renders the tags row. When submitting a tag the whole
     * view is submitted and re-rendered.
     * 
     * @return void
     */
    public function renderTagsRow()
    {
        $action = $this->getData('mode', 'string') == 'edit' ?
            'add_tag_edit_' . $this->presetData['id'] : 'add_tag';

        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentRow(array(
                $this->components->getHintedField('{#tags#}', 'tag', 'text', array(
                    'value' => ''
                ))
            ), array(), array(
                'width' => '80%'
            )),
            $this->getComponentImage('add_button.png', array(
                'onclick' => $this->getOnclickSubmit($action,array('keep_user_data' => 1)),
                'style' => 'add_tag_button'
            ))
        ), array(), array(
            'width' => '100%'
        ));

        $this->renderValidationErrors('tags');

        $tags = array();

        foreach ($this->getData('tags', 'array') as $tag) {
            $tags[] = $this->components->getItemTag($tag);
            $tags[] = $this->getComponentImage('delete-tag.png', array(
                'onclick' => $this->getOnclickSubmit('delete_tag_' . $tag),
                'style' => 'remove_tag_button','keep_user_data' => 1
            ));
        }

        $this->layout->scroll[] = $this->getComponentRow($tags, array(), array(
            'margin' => '5 20 0 20'
        ));
    }

    /**
     * Renders price and time rows for item.
     *
     * @return void
     */
    public function renderPriceAndTimeRow()
    {
        $price = isset($this->presetData['price']) ? $this->presetData['price'] : '';

        $this->layout->scroll[] = $this->components->getHintedIconField(strtoupper('{#price_&_time#}'), 'price', 'number', array(
            'icon' => 'dollar_sign.png',
            'value' => $price,
        ));

        $this->renderValidationErrors('price');

        $this->layout->scroll[] = $this->getComponentSpacer(10);

        $time = isset($this->presetData['time']) ? $this->presetData['time'] : '';

        $this->layout->scroll[] = $this->components->getHintedIconField('{#time#}', 'time', 'number', array(
            'icon' => 'time_sign.png',
            'value' => $time,
        ));

        $this->renderValidationErrors('time');
    }

    /**
     * Renders textarea for item description
     *
     * @return void
     */
    public function renderItemDescription()
    {
        $value = isset($this->presetData['description']) ? $this->presetData['description'] : '';

        $this->layout->scroll[] = $this->components->getHintedField('{#description#}', 'description', 'textarea', array(
            'value' => $value
        ));

        $this->renderValidationErrors('description');
    }

    /**
     * Render category list. Items are rendered as radio buttons in a mosaic list.
     *
     * @return void
     */
    public function renderStylesList()
    {
        $this->layout->scroll[] = $this->getComponentText(strtoupper('{#styles#}'), array(
            'style' => 'steps_hint'
        ));

        $this->renderValidationErrors('categories');

        $this->layout->scroll[] = $this->getComponentSpacer(10);

        $categories = $this->model->getItemCategories();
        $categories = array_map(function($category) { return $category->name; }, $categories);

        $itemCategories = isset($this->presetData['categories']) ? $this->presetData['categories'] : array();

        if (!empty($itemCategories)) {
            $itemCategories = array_map(function($category) { return $category->name; }, $itemCategories);
        }

        $this->layout->scroll[] = $this->components->getCategoryTagButtons(array(
            'items' => $categories,
            'variable' => 'category',
            'values' => $itemCategories
        ));
    }

    /**
     * Renders the save item button
     *
     * @return void
     */
    public function renderSaveButton()
    {
        $action = $this->getData('mode', 'string') == 'edit' ? 'edit_item_' . $this->presetData['id'] : 'save_item';

        $this->layout->scroll[] = $this->getComponentText(strtoupper('{#save_item#}'), array(
            'style' => 'button_primary',
            'onclick' => $this->getOnclickSubmit($action)
        ));
    }

    /**
     * Renders validation errors (if any) for given field
     *
     * @param $field
     */
    public function renderValidationErrors($field) {
        if (isset($this->model->validation_errors[$field])) {
            $this->layout->scroll[] = $this->getComponentText($this->model->validation_errors[$field], array(
                'style' => 'validation_error_message'
            ));
        }
    }
}
