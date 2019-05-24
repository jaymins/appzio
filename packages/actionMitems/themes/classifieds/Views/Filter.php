<?php

namespace packages\actionMitems\themes\classifieds\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\Components\Components as Components;
use packages\actionMitems\Models\Model as ArticleModel;

class Filter extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {

        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->components->uiKitSlider();
        $this->layout->scroll[] = $this->getPriceField();
        $this->layout->scroll[] = $this->getCategories();
        $this->layout->footer[] = $this->getSubmitButton();

        return $this->layout;
    }

    public function tab2(){
        $this->layout = new \stdClass();
        $categories = $this->getData('categories', 'array');
        $onclick = 'Categories/savecategory/';
        $this->layout->scroll[] = $this->uiKitHierarchicalCategories($categories,$onclick,1);
        return $this->layout;
    }

    protected function getPriceField()
    {
        $params['unit'] = '€';
        $params['min'] = '5';
        $params['max'] = '700';
        $params['default_value'] = '5';
        $params['step'] = '1';
        $params['title'] = '{#price#}';
        $params['variable'] = 'price_from';
        $params['variable2'] = 'price_to';
        $params['default_value2'] = '200';
        return $this->uiKitSlider($params);
    }

    protected function getTagsField()
    {
        $tags[] = $this->getComponentText(strtoupper('{#tags#}'), array(
            'style' => 'mitems_filter_label'
        ));

        $tags[] = $this->getComponentRow(array(
            $this->getComponentFormFieldText('', array(
                'variable' => 'tag',
                'style' => 'mitems_full_width_input'
            )),
            $this->getComponentImage('add_button.png', array(
                'onclick' => $this->getOnclickSubmit('save-filter-tag')
            ))
        ), array(
            'margin' => '10 20 10 20',
            'width' => '100%'
        ));

        $filters = $this->getData('filters', 'object');
        $tagsList = empty($filters->tags) ? array() : $filters->tags;

        $tagsRow = array();

        foreach ($tagsList as $tag) {
            $tagsRow[] = $this->components->getItemTag($tag);
            $tagsRow[] = $this->getComponentImage('delete-tag.png', array(
                'onclick' => $this->getOnclickSubmit('delete_tag_' . $tag),
                'style' => 'remove_tag_button','keep_user_data' => 1
            ));
        }

        $tags[] = $this->getComponentRow($tagsRow, array(), array(
            'margin' => '10 0 0 0'
        ));

        return $this->getComponentColumn($tags, array(), array(
            'margin' => '10 20 0 20'
        ));
    }

    public function getCategories()
    {
        $category = isset($this->presetData['category']) ? $this->presetData['category'] : '';
        $onclick[] = $this->getOnclickTab(2);

        if($this->model->getSavedVariable('category_name')){
            $parameters['is_selected'] = 1;
            $category = $this->model->getSavedVariable('category_name');
        } else {
            $parameters = array();
        }

        if($category){
            $parameters['value'] = $category;
        }

        $parameters['big_form_title'] = true;

        return $this->components->uiKitHintedSelectButtonField(
            '{#category#}',
            'category_name',
            $onclick,
            $parameters);
    }

    protected function getSubmitButton()
    {

        $btn1['onclick'][] = $this->getOnclickSubmit('clear-filters');
        $btn1['onclick'][] = $this->getOnclickClosePopup();

        //$btn2['onclick'][] = $this->getOnclickLocation(array('sync_open' => 1));
        $btn2['onclick'][] = $this->getOnclickSubmit('save-filters');
        $btn2['onclick'][] = $this->getOnclickClosePopup();

        return $this->uiKitDoubleButtons('{#clear_all_filters#}','{#save_filters#}',$btn1,$btn2,
            array(),array(),'#F0F3F8');
    }

    protected function getDistance()
    {
        $price[] = $this->getComponentText(strtoupper('{#Distance#}'), array(
            'style' => 'mitems_filter_label'
        ));

        $distance = $this->model->getSavedVariable('filter_distance');
        $distanceValue = empty($distance) ? 5000 : $distance;

        $price[] = $this->getComponentColumn(array(
            $this->getComponentRow(array(
                $this->getComponentText('0 {#km#}',array(), array(
                    'width' => '33%',
                    'font-size' => '14',
                    'color' => "#FFFFFF"
                )),
                $this->getComponentRow(array(
                    $this->getComponentText($distanceValue, array('variable' => 'filter_distance'), array(
                        'font-size' => '15',
                        'color' => "#FFFFFF"
                    )),
                    $this->getComponentText( ' {#km#}', array(), array(
                        'font-size' => '15',
                        'color' => "#FFFFFF"
                    )),
                ), array(), array(
                    'width' => '33%',
                    'text-align' => 'center',
                )),
                $this->getComponentText('20.000 {#km#}', array(), array(
                    'width' => '33%',
                    'text-align' => 'right',
                    'font-size' => '14',
                    'color' => "#FFFFFF"
                ))
            ), array(), array(
                'margin' => '12 20 0 20',
            )),
            $this->getComponentRangeSlider(array_merge(
                array(
                    'min_value' => 50,
                    'max_value' => 20000,
                    'step' => 50,
                    'variable' => 'filter_distance',
                    'value' => $distanceValue,
                ),
                $this->getSliderStyles()
            ))
        ), array(), array(
            'background-color' => '#2d2d2d',
        ));

        return $this->getComponentColumn($price, array(), array(
            'padding' => '10 20 0 20'
        ));
    }

    public function getSliderStyles() {

        $slider_ball = $this->getImageFileName('slider-ball.png');

        return array(
            'step' => '1',
            'left_track_color' => '#ffc204',
            'right_track_color' => '#bebebe',
            'thumb_color' => '#7ed321',
            'track_height' => '4px',
            'thumb_image' => $slider_ball,
        );
    }
}