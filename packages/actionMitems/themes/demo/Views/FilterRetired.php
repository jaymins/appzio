<?php

namespace packages\actionMitems\themes\demo\Views;
use packages\actionMitems\Views\Create as BootstrapView;
use packages\actionMitems\themes\demo\Components\Components;

class FilterRetired extends BootstrapView
{

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1()
    {

        $this->margin = '8';

/*        if ( preg_match( '~selected-category-~', $this->menuid ) ) {
            $selected_category_id = str_replace( 'selected-category-', '', $this->menuid );
            $this->layout->scroll[] = $this->getComponentText($selected_category_id);
        }

        if ($this->menuid == 'form-submitter') {
            $this->filterModel->addFilter();
            $this->layout->onload = $this->getRedirect( $this->getActionidByPermaname( 'items' ) );
        }*/

        //$filters = $this->filterModel->getFilter();

        $this->renderTitle();
        $this->getLocationFilter();
        $this->getDistanceFilter();
        return $this->layout;
        $this->getCategoryFilter($filters['category']);

        //$this->getLocationFilter($filters['location']);
        $this->getDistanceFilter($filters['distance']);
        $this->getPriceMinFilter($filters['price_min']);
        $this->getPriceMaxFilter($filters['price_max']);
        $this->getSaveButton();

        return $this->layout;
    }

    public function soontobetab2()
    {

        $categories = $this->categoriesModel->getCategories();

        $this->variablePrefix = 'temp_filter_';
        $this->identifier = 'category';

        $status = $this->getVariable($this->variablePrefix . $this->identifier);

        // Define the selected state for radio buttons.
        // This will give them another class which will mark them as selected
        $selectedState = array('style' => 'radio_selected_state', 'allow_unselect' => 1, 'animation' => 'fade');


        // Display
        $options = array();

        foreach ($categories as $field) {
            $options[] = $this->renderSingleOptionField($field['name'], $status, $selectedState);
            $options[] = $this->getHairline('#DADADA');
        }

        $this->layout->scroll[] = $this->getComponentColumn($options, array(
            'margin' => '0 0 20 0'
        ));

//        foreach ($categories as $key => $category) {
//            $this->layout->scroll[] = $this->getHairline('#CCCCCC');
//            $this->layout->scroll[] = $this->renderCategory($category);
//        }
//
//        $this->layout->scroll[] = $this->getHairline('#CCCCCC');

        $this->renderButtons();

        return $this->layout;
    }

    public function renderTitle()
    {
        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentText('{#filters#}',['uppercase' => true], [
                'padding' => '15 0 15 15',
                'font-size' => '12',
                'color' => '#545050'
            ])
        ], [
            'background-color' => '#E6E9EE',
        ]);

        $this->layout->scroll[] = $this->uiKitDivider();
    }

    private function renderCategory($category)
    {
        $clicker = new StdClass();
        $clicker->action = 'tab1';
        $clicker->keep_user_data = 1;

        $update_params = array(
            $this->getVariableId('category') => $category['name']
        );

        $clicker->set_variables_data = (object)$update_params;

        return $this->getComponentText($category['name'], [
            'padding' => '10 10 10 10',
            'color' => '#555555',
            'onclick' => $this->getOnclick('tab1')
        ]);
    }


    public function getCategoryFilter($category)
    {
        $clicker = new StdClass();
        $clicker->action = 'open-action';
        $clicker->action_config = $this->getActionidByPermaname('categorieslisting');
        $clicker->id = 'open-categories-popup';
        $clicker->open_popup = '1';
        $clicker->sync_open = '1';
        $clicker->back_button = '1';

        $category_heading = !is_null($category) ? $category : 'Choose Category';

        if ($this->getSubmitVariable('category')) {
            $category_heading = $this->getSubmitVariable('category');
        }

        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentImage('choose_cat.png', [
                'crop' => 'yes',
                'width' => '30',
                'vertical-align' => 'middle',
                'margin' => '0 5 0 10'
            ]),
            $this->getComponentText($category_heading, [
                'variable' => 'category',
                'color' => '#7D7D7D'
            ]),
            $this->getFieldtext($category_heading, array(
                'variable' => $this->getVariableId( 'category' ),
                'width' => 1,
                'height' => 1,
                'opacity' => 0,
            )),
            $this->getComponentText('>', [
                'padding' => '12 10 12 0',
                'color' => 'D4D4D4',
                'floating' => '1',
                'float' => 'right'
            ])
        ], [
            'margin' => '12 ' . $this->margin . ' 0 ' . $this->margin,
            'background-color' => '#F4F5F9',
            'border-radius' => 3,
            'shadow-color' => '#DFE3E6',
            'shadow-offset' => '0 3',
            'shadow-opacity'  => '0.9',
            'onclick' => $this->getOnclick('tab2')
        ]);
    }

    public function getLocationFilter($location='Location')
    {
        $clicker = new \stdClass();
        $clicker->action = 'open-action';
        $clicker->action_config = $this->model->getActionidByPermaname('locationslisting');
        $clicker->id = 'open-locations-popup';
        $clicker->open_popup = '1';
        $clicker->sync_open = '1';
        $clicker->back_button = '1';

        $location_heading = !is_null($location) ? $location : 'Location';

/*        if ($this->getSubmitVariable('location')) {
            $location_heading = $this->getSubmitVariable('location');
        }*/

        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentImage('location_filters.png', [],[
                'crop' => 'yes',
                'width' => '30',
                'vertical-align' => 'middle',
                'margin' => '0 5 0 10'
            ]),
/*            $this->getComponentText($location_heading, [],[
                'variable' => 'location',
                'color' => '#7D7D7D'
            ]),*/
            $this->getComponentFormFieldText($location_heading, array(
                'variable' => $this->model->getVariableId( 'location' ),array(
                'width' => 1,
                'height' => 1,
                'opacity' => 0,)
            )),

            $this->getComponentText('>', [],[
                'padding' => '12 10 12 0',
                'color' => 'D4D4D4',
                'floating' => '1',
                'float' => 'right'
            ])
        ],[], [
            'margin' => '12 ' . $this->margin . ' 0 ' . $this->margin,
            'background-color' => '#F4F5F9',
            'border-radius' => 3,
            'shadow-color' => '#DFE3E6',
            'shadow-offset' => '0 3',
            'shadow-opacity'  => '0.9',
            'onclick' => $this->getOnclickTab(2)
        ]);
    }

    public function getDistanceFilter($distance=10000)
    {
        $distance_heading = !is_null($distance) ? $distance : 10000;

        if ($this->model->getSubmittedVariableByName('distance')) {
            $distance_heading = $this->model->getSubmittedVariableByName('distance');
        }

        $rows[] = $this->getComponentRow([
            $this->getComponentImage('location_filters.png', [],[
                'crop' => 'yes',
                'width' => '30',
                'vertical-align' => 'middle',
                'margin' => '20 5 0 10'
            ]),
            $this->getComponentText('Distance', [],[
                'color' => '#7D7D7D'
            ]),
            $this->getComponentColumn([
                $this->getComponentRow([
                    $this->getComponentText($distance_heading, [
                        'variable' => $this->model->getVariableId( 'distance' )],[
                        'padding' => '12 0 12 0',
                        'color' => 'D4D4D4'
                    ]),
                    $this->getComponentText(' km',[], [
                        'color' => 'D4D4D4',
                        'padding' => '12 10 12 0',
                    ]),
                ])
            ], [],[
                'floating' => '1',
                'float' => 'right'
            ]),
        ]);

        $rows[] = $this->getComponentColumn([
            $this->getComponentRangeSlider([
                'min_value' => 1,
                'max_value' => 20000,
                'step' => 100,
                'variable' => 'distance',
                'value' => $distance_heading,
            ])
        ]);

        $this->layout->scroll[] = $this->getComponentColumn($rows,[], [
            'margin' => '12 ' . $this->margin . ' 0 ' . $this->margin,
            'background-color' => '#F4F5F9',
            'border-radius' => 3,
            'shadow-color' => '#DFE3E6',
            'shadow-offset' => '0 3',
            'shadow-opacity'  => '0.9'
        ]);
    }

    public function getPriceMinFilter($price)
    {
        $price_min = !is_null($price) ? $price / 100 : 10;

        if ($this->getSubmitVariable('price_min')) {
            $price_min = $this->getSubmitVariable('price_min');
        }

        $rows[] = $this->getComponentRow([
            $this->getComponentImage('price_filter.png', [
                'crop' => 'yes',
                'width' => '30',
                'vertical-align' => 'middle',
                'margin' => '20 5 0 10'
            ]),
            $this->getComponentText('Price Min', [
                'color' => '#7D7D7D'
            ]),
            $this->getComponentColumn([
                $this->getComponentRow([
                    $this->getComponentText('$', [
                        'color' => 'D4D4D4'
                    ]),
                    $this->getComponentText($price_min, [
                        'variable' => $this->getVariableId( 'price_min' ),
                        'padding' => '12 10 12 0',
                        'color' => 'D4D4D4'
                    ])
                ])
            ], [
                'floating' => '1',
                'float' => 'right'
            ]),
        ]);

        $rows[] = $this->getComponentColumn([
            $this->getRangeslider('', array_merge([
                'min_value' => 0,
                'max_value' => 3000,
                'step' => 1,
                'variable' => 'price_min',
                'value' => $price_min,
            ],
                $this->getSliderStyles()
            ))
        ]);

        $this->layout->scroll[] = $this->getComponentColumn($rows, [
            'margin' => '12 ' . $this->margin . ' 0 ' . $this->margin,
            'background-color' => '#F4F5F9',
            'border-radius' => 3,
            'shadow-color' => '#DFE3E6',
            'shadow-offset' => '0 3',
            'shadow-opacity'  => '0.9'
        ]);
    }

    public function getPriceMaxFilter($price)
    {
        $price_max = !is_null($price) ? $price / 100 : 50;

        if ($this->getSubmitVariable('price_max')) {
            $price_max = $this->getSubmitVariable('price_max');
        }

        $rows[] = $this->getComponentRow([
            $this->getComponentImage('price_filter.png', [
                'crop' => 'yes',
                'width' => '30',
                'vertical-align' => 'middle',
                'margin' => '20 5 0 10'
            ]),
            $this->getComponentText('Price Max', [
                'color' => '#7D7D7D'
            ]),
            $this->getComponentColumn([
                $this->getComponentRow([
                    $this->getComponentText('$', [
                        'color' => 'D4D4D4'
                    ]),
                    $this->getComponentText($price_max, [
                        'variable' => $this->getVariableId( 'price_max' ),
                        'padding' => '12 10 12 0',
                        'color' => 'D4D4D4'
                    ])
                ])
            ], [
                'floating' => '1',
                'float' => 'right'
            ]),
        ]);

        $rows[] = $this->getComponentColumn([
            $this->getRangeslider('', array_merge([
                'min_value' => 0,
                'max_value' => 3000,
                'step' => 1,
                'variable' => 'price_max',
                'value' => $price_max,
            ],
                $this->getSliderStyles()
            ))
        ]);

        $this->layout->scroll[] = $this->getComponentColumn($rows, [
            'margin' => '12 ' . $this->margin . ' 0 ' . $this->margin,
            'background-color' => '#F4F5F9',
            'border-radius' => 3,
            'shadow-color' => '#DFE3E6',
            'shadow-offset' => '0 3',
            'shadow-opacity'  => '0.9'
        ]);
    }

    public function getSaveButton()
    {
        $margin = ($this->screen_width / 2) - 100;
        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentText('{#Save#}', [
                'background-color' => $this->color_topbar,
                'width' => '100%',
                'color' => '#FFFFFF',
                'padding' => '20 0 20 0',
                'border-radius' => 3,
                'text-align' => 'center',
                'onclick' => $this->getOnclick('id', false, 'form-submitter')

            ])
        ], [
            'margin' => '12 ' . $margin  . ' 10 ' . $margin,
        ]);
    }

    public function getSliderStyles()
    {
        return array(
            'step' => '1',
            'left_track_color' => $this->colors['top_bar_color'],
            'right_track_color' => '#bebebe',
            'thumb_color' => '#7ed321',
            'track_height' => '10px',
            'margin' => '0 10 0 10',
            'thumb_image' => 'slider_ball.png',
            'height' => '35',
        );
    }

    public function renderButtons()
    {
        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentText('{#Save#}', [
                'background-color' => '#45D194',
                'width' => '50%',
                'color' => '#FFFFFF',
                'padding' => '20 0 20 0',
                'text-align' => 'center',
                'onclick' => $this->getOnclick('tab1')

            ]),
            $this->getComponentText('{#Back#}', [
                'background-color' => '#E46465',
                'width' => '50%',
                'color' => '#FFFFFF',
                'padding' => '20 0 20 0',
                'text-align' => 'center',
                'onclick' => $this->getOnclick('tab1')
            ])
        ], [
            'margin' => '12 10 10 10',
        ]);
    }

    protected function renderSingleOptionField($field, $status, $selectedState)
    {
        // When in selected state, the field will have this value
        $selectedState['variable_value'] = $field;
        // Check whether the field should be selected by default
        $selectedState['active'] = $this->getActiveStatus($status, $field);
        // Get the name of the variable that the field should hold
        $variable = $field;

        if (strstr($variable, 'zodiac_sign' )) {
            $fieldRow[] = $this->getComponentImage('zodiac_' . $field . '.png', array(
                'width' => '40',
                'margin' => '0 10 0 10'
            ));
        }

        $fieldRow[] = $this->getComponentText(ucfirst($field), array(
            'padding' => '10 10 10 20'
        ));

        $fieldRow[] = $this->getComponentRow(array(
            $this->getComponentText('', array(
                'style' => 'radio_default_state',
                'variable' => $this->variablePrefix . $variable,
                'selected_state' => $selectedState,
            ))
        ), array(
            'width' => '40%',
            'floating' => 1,
            'float' => 'right'
        ));

        return $this->getComponentRow(($fieldRow), array(
            'padding' => '5 0 5 0',
            'margin' => '0 0 0 0'
        ));
    }

    protected function getActiveStatus($status, $field)
    {
        // If status is not set and we're accessing this NOT from the user profile
        if (!empty($this->variablePrefix) && empty($status)) {
            return '1';
        }

        if (!empty($this->variablePrefix) && !empty($status)) {
            $status = json_decode($status);
            return in_array($field, $status) ? '1' : '0';
        }

        // if ($this->options['type'] != self::FIELD_TYPE_CHECK) {
        //     return $status == $field ? '1' : '0';
        // }

        if (is_null($status)) {
            return '0';
        }

        return in_array($field, json_decode($status)) ? '1' : '0';
    }
}
