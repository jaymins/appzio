<?php

namespace packages\actionMitems\Views;

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
        $this->model->rewriteActionConfigField('background_color', '#1b1b1b');

        $this->layout = new \stdClass();
        $this->layout->scroll[] = $this->getPriceField();
        $this->layout->scroll[] = $this->getDistance();
        $this->layout->scroll[] = $this->getTagsField();
        $this->layout->scroll[] = $this->getCategories();
        $this->layout->footer[] = $this->getSubmitButton();

        return $this->layout;
    }

    protected function getPriceField()
    {
        $price[] = $this->getComponentText(strtoupper('{#price#}'), array(
            'style' => 'mitems_filter_label'
        ));

        $priceFrom = !empty($this->getData('filters', 'object')->price_from) ?
            $this->getData('filters', 'object')->price_from : '';

        $priceTo = !empty($this->getData('filters', 'object')->price_to) ?
            $this->getData('filters', 'object')->price_to : '';

        $price[] = $this->getComponentRow(array(
            $this->getComponentFormFieldText($priceFrom, array(
                'variable' => 'price_from',
                'hint' => '{#from#}',
                'style' => 'mitems_half_width_input',
                'input_type' => 'number'
            )),
            $this->getComponentFormFieldText($priceTo, array(
                'variable' => 'price_to',
                'hint' => '{#to#}',
                'style' => 'mitems_half_width_input',
                'input_type' => 'number'
            ))
        ), array(
            'margin' => '0 20 0 20'
        ));

        return $this->getComponentColumn($price, array(), array(
            'padding' => '10 20 0 20'
        ));
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

    protected function getCategories()
    {
        $styles[] = $this->getComponentText(strtoupper('{#styles#}'), array(
            'style' => 'steps_hint'
        ));

        $categories = $this->model->getItemCategories();

        $items = array_map(function($item) { return $item->name; }, $categories);

        $filters = $this->getData('filters', 'object');
        $filterCategories = empty($filters->categories) ? array() : json_decode($filters->categories);

        $styles[] = $this->components->getCategoryTagButtons(array(
            'items' => $items,
            'variable' => 'category',
            'values' => $filterCategories
        ));

        return $this->getComponentColumn($styles, array(), array(
            'padding' => '10 0 0 0'
        ));
    }

    protected function getSubmitButton()
    {
        $onclick = new \stdClass();
        $onclick->action = 'submit-form-content';
        $onclick->id = 'save-filters';

        return $this->getComponentText('Update Results', array(
            'style' => 'items_button_primary',
            'onclick' => array(
                $this->getOnclickSubmit('save-filters'),
                $this->getOnclickClosePopup(),
                $this->getOnclickLocation(array('sync_open' => 1)),
            )
        ));
    }

    protected function getDistance()
    {
        $price[] = $this->getComponentText(strtoupper('{#Distance#}'), array(
            'style' => 'mitems_filter_label'
        ));

        $distance = $this->model->getSavedVariable('filter_distance');
        $distanceValue = empty($distance) ? 150 : $distance;

        $price[] = $this->components->getHintedRangeSlider('filter_distance', array(
            'min_value' => 0,
            'max_value' => 1000,
            'value' => $distanceValue
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