<?php

namespace packages\actionMitems\themes\fanshop\Views;
use packages\actionMitems\Views\Create as BootstrapView;
use packages\actionMitems\themes\fanshop\Components\Components;

class Create extends BootstrapView
{

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;


    public function tab1()
    {
        $this->layout = new \stdClass();

        if($this->model->getSavedVariable('logged_in') != 1){
            $this->layout->scroll[] = $this->components->getIntroScreen();
            $this->layout->overlay[] = $this->components->getIntroScreenOverlay();
            return $this->layout;
        }

        $this->presetData = $this->getData('presetData', 'array');

        $name = isset($this->presetData['name']) ? $this->presetData['name'] : '';
        $price = isset($this->presetData['price']) ? $this->presetData['price'] : '';
        $description = isset($this->presetData['description']) ? $this->presetData['description'] : '';
        $category = isset($this->presetData['category']) ? $this->presetData['category'] : '';

        $this->model->setBackgroundColor('#1b1b1b');
        $this->layout->scroll[] = $this->getComponentSpacer('15');
        $this->renderImageGrid();
        $this->layout->scroll[] = $this->components->getHintedField('{#name#}', 'name', 'text',
            array('value' => $name));
        //$this->renderCategories();

        if($this->model->getSavedVariable('category') OR $category){
            $this->renderTagsRow();
        }

        $this->layout->scroll[] = $this->components->getHintedField('{#description#}', 'description', 'textarea',array(
            'value' => $description
        ));

        $this->layout->scroll[] = $this->components->getHintedField('{#price#}', 'price', 'text', array(
            'input_type' => 'number','value' => $price));
        $this->renderSaveButton();
        return $this->layout;
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
        
        $onclick[] = $this->getOnclickSubmit($action,array('keep_user_data' => 1,'viewport' => 'current'));
        $onclick[] = $this->getOnclickSetVariables(array('tag' => ''),array('viewport' => 'current'));

        $prettytags = $this->model->getPrettyTagsFromSession();

        if($this->model->getSavedVariable('categories_name')){
            $parameters['is_selected'] = 1;
        } else {
            $parameters = array();
        }

        $parameters['icon'] = 'add_tag_icon.png';
        $parameters['editable_field'] = true;
        $parameters['value'] = '';

        $this->layout->scroll[] = $this->components->uiKitHintedSelectButtonField(
            '{#tags#}',
            'tag',
            $onclick,
            $parameters);


/*        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentRow(array(
                $this->components->getHintedField('{#tags#}', 'tag', 'text', array(
                    'value' => '',
                    'activation' => 'keep_open',
                    'viewport' => 'current',
                    'submit_menu_id' => 'add_tag'
                ))
            ), array(), array(
                'width' => '100%'
            )),
            $this->getComponentImage('add_tag_icon.png', array(
                'onclick' => $onclick,
                'style' => 'fanshop_fanshop_ukit_form_field_select_icon'
            ))
        ), array(), array(
            'width' => '100%'
        ));*/

        $this->renderValidationErrors('tags');

        if($prettytags){
            $this->layout->scroll[] = $this->uiKitTagList($prettytags,array(
                'onclick_delete' => 'Create/deletetag/'
            ));
        }

    }


    /**
     * Render category list. Items are rendered as radio buttons in a mosaic list.
     *
     * @return void
     */
    public function renderCategories()
    {
        $this->renderValidationErrors('category');
        $this->layout->scroll[] = $this->getComponentSpacer(10);
        $category = isset($this->presetData['category']) ? $this->presetData['category'] : '';

        $onclick[] = $this->getOnclickSubmit('update_item',array('keep_user_data' => 1,'viewport' => 'current'));
        $onclick[] = $this->getOnclickOpenAction('categories',false,array(
            'open_popup' => 1,'sync_close' => 1
        ));

        if($this->model->getSavedVariable('categories_name')){
            $parameters['is_selected'] = 1;
        } else {
            $parameters = array();
        }

        if($category){
            $parameters['value'] = $category;
        }

        $this->layout->scroll[] = $this->components->uiKitHintedSelectButtonField(
            '{#category#}',
            'category',
            $onclick,
            $parameters);
    }


    public function renderSaveButton()
    {
        $action = $this->getData('mode', 'string') == 'edit' ? 'edit_item_' . $this->presetData['id'] : 'save_item';

        $this->layout->scroll[] = $this->uiKitButtonFilled('{#save_item#}', array(
            'onclick' => $this->getOnclickSubmit($action, array('keep_user_data' => 1))
        ), array('margin' => '20 80 20 80'));
    }

    /**
     * Render a 5 image grid containing the item pictures.
     *
     */
    public function renderImageGrid()
    {

        $grid_items = $this->uiKitImageGridUpload(array(
            'base_variable' => 'itempic'
        ));

        if (empty($grid_items)) {
            return false;
        }

        $this->layout->scroll[] = $grid_items;
    }



}
