<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMfood\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMfood\Components\Components;


class View extends BootstrapView
{


    /* @var Components */
    public $components;
    public $theme;
    public $recipe_data;
    public $recipe_info;
    public $recipe_ingredients_list;
    public $recipe_step_list;
    public $summary_info;
    public $font_size = 16;

    /* main recipe view */
    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->layout->scrollcontent_config = new \stdClass();
        $this->layout->scrollcontent_config->style_content = ['background-color' => '#ffffff'];

        $this->recipe_info = $this->getData('recipe_info', 'mixed');
        $this->recipe_ingredients_list = $this->getData('ingredient_list', 'mixed');
        $this->summary_info = $this->getData('summary_bar', 'mixed');

        $this->setTopBar();
        $this->setHeader(1);


        if (isset($this->recipe_info->photo) AND $this->recipe_info->photo) {
            $this->setTopImage($this->recipe_info->photo);
            $this->setIngredients();
        }

        return $this->layout;
    }

    /* method */
    public function tab2()
    {

        $this->layout = new \stdClass();
        $this->layout->scrollcontent_config = new \stdClass();
        $this->layout->scrollcontent_config->style_content = ['background-color' => '#ffffff'];

        $this->recipe_step_list = $this->getData('step_list', 'mixed');

        $this->setTopBar();
        $this->setHeader(2);

        $this->layout->scroll[] = $this->getComponentColumn($this->setSummaryBar($this->summary_info), [], [
            'width' => '100%',
            'background-color' => $this->color_top_bar_color,
            'vertical-align' => 'bottom',
        ]);

        $this->setRecipeMethod();

        return $this->layout;
    }

    public function getDivs()
    {
        $divs['units'] = $this->components->themeFullScreenDiv([
            'title' => '{#choose_your#} {#units#}',
            'div_name' => 'units',
            'content' => $this->getUnitsDivContent()
        ]);
        return $divs;
    }

    private function getUnitsDivContent()
    {
        $unitsItems[] = ['text' => 'kg / gram', 'value' => 'metric'];
        $unitsItems[] = ['text' => 'lbs / oz', 'value' => 'imperial'];
        $variable = 'units';
        $active = $this->model->getSavedVariable('units');
        $id = isset($this->recipe_info->id) ? $this->recipe_info->id : false;

        return $this->getComponentColumn([
            $this->components->themeRadioButtonsList($unitsItems, $variable, $active),
            $this->components->themeButton('{#save#}',
                [
                    $this->getOnclickHideDiv('units'),
                    $this->getOnclickSubmit('controller/changeunits/' . $id)
                ],
                'download.png'),
        ], [], []);
    }

    /**
     * Set standard uiKit component uiKitTopbarWithButtons
     */
    public function setTopBar()
    {
        $this->layout->header[] = $this->uiKitTopbarWithButtons([
            'leftSection' => [
                'image' => 'icon_arrow_back.png',
                'onclick' => $this->getOnclickGoHome()
            ],
            'centerSection' => [
                'title' => strtoupper($this->recipe_data['type']['name']),
            ],
            'rightSection' => [
                [
                    'image' => 'theme-icon-scale.png',
                    'onclick' => $this->getOnclickShowDiv('units',
                        array('background' => '#000000', 'transition' => 'from-bottom', 'tap_to_close' => true, 'sync_close' => true),
                        array('left' => '0', 'right' => '0', 'top' => 0, 'height' => $this->screen_height)
                    )
                ],
            ],
        ]);
    }

    /**
     * @param int $tab
     * @return mixed
     */
    public function setHeader($tab = 1)
    {
        $calendar_item_id = $this->getData('calendar_item_id', 'mixed');

        if (isset($this->recipe_info->name)) {
            $this->layout->scroll[] =
                $this->components->themeHeader([
                    'title' => $this->recipe_info->name,
                    'floating_element' => $this->getFloatingElement($calendar_item_id),
                    'tabs' => $this->getTabs()
                ], [
                    'activeTab' => $tab
                ], [
                    'background-image' => 'bgr_recipe.png',
                ]);
        }
    }

    /**
     * @return array
     */
    private function getFloatingElement($calendar_item_id)
    {
        if (!$calendar_item_id) {
            return false;
        }

        return $floating_element = [
            'icon' => 'theme-icon-switch.png',
            'action' => ['onclick' => $this->getOnclickOpenAction('meals', false, [
                'sync_open' => 1,
                'back_button' => 1
            ])],
            'style' => [
                'width' => '25%',
                'height' => '80',
            ]
        ];
    }

    /**
     * @return array
     */
    private function getTabs()
    {
        return [
            ['text' => '{#ingredients#}',
                'onclick' => $this->getOnclickTab(1)],
            ['text' => '{#method#}',
                'onclick' => $this->getOnclickTab(2)]
        ];
    }

    /**
     * @param $image
     */
    public function setTopImage($image)
    {
        $this->layout->scroll[] = $this->getComponentColumn($this->setSummaryBar($this->summary_info), [], [
            'width' => '100%',
            'height' => '350',
            'background-image' => $this->getImageFileName($image, [
                'priority' => '9',
                'imgwidth' => '1366',
                'imgheight' => '768',
            ]),
            'background-size' => 'cover',
            'vertical-align' => 'bottom',
        ]);
    }

    /* time, servings, difficulty */
    /**
     * @param null $recipeType
     * @return array|\stdClass
     */
    public function setSummaryBar($recipeType = null)
    {
        if ($recipeType == null) {
            return [];
        }

        foreach ($recipeType as $element) {

            if (!$element['name'] OR !$element['icon']) {
                continue;
            }

            $summaryElements[] = $this->getComponentRow([
                $this->getComponentImage($element['icon'], [
                    'priority' => '1'
                ], [
                    'height' => '30',
                    'padding' => '0 10 0 4'
                ]),
                $this->getComponentText($element['name'], [
                    'uppercase' => true
                ], [
                    'font-size' => '14',
                    'color' => '#ffffff',
                    'font-ios' => 'OpenSans-Bold',
                    'font-android' => 'OpenSans-Bold',

                ])
            ], [], [
                'padding' => '10 10 10 10',
                'width' => '33%',
            ]);
        }

        return [
            $this->getComponentRow(
                $summaryElements, [], [
                'vertical-align' => 'middle',
                'height' => '60',
                'width' => '100%',
                'background-image' => $this->getImageFileName('shadow-image-wide-inverted.png', [
                    'priority' => '1',
                    'imgwidth' => '1366',
                    'imgheight' => '768',
                ]),
                'background-size' => 'cover',
            ])
        ];
    }

    public function setIngredients()
    {
        $content[] = $this->getComponentRow([
            $this->getComponentText('{#ingredients#}', [], [
                'font-size' => '26',
                'color' => '#000000',
            ])
        ], [], [
            'background-color' => '#FFFFFF',
            'padding' => '20 20 20 20'
        ]);

        foreach ($this->recipe_ingredients_list as $ingredient) {

            $content[] = $this->getComponentRow([
                $this->getComponentText('• ' . $ingredient->quantity . ' ' . $ingredient->recipe_ingredient->unit . ' ' . $ingredient->recipe_ingredient->name, [], [
                    'font-size' => $this->font_size,
                ])
            ], [], [
                'color' => '#000000',
                'background-color' => '#FFFFFF',
                'vertical-align' => 'top',
                'padding' => '10 0 15 40',
                'width' => '100%',
            ]);
        }

        $content[] = $this->getcomponentText(strtoupper('{#view_shopping_list#}'), [
            'onclick' => $this->getOnclickOpenAction('shoppinglist', ['sync_open' => true]),
        ], [
            'width' => '50%',
            'padding' => '5 5 5 5',
            'margin' => '15 15 15 15',
            'border-radius' => '10',
            'background-color' => $this->color_top_bar_color,
            'font-size' => '16',
            'text-align' => 'center',
            'color' => $this->color_top_bar_text_color
        ]);
        $this->layout->scroll[] = $this->getComponentColumn($content, [], [
            'color' => '#000000',
            'background-color' => '#FFFFFF',
        ]);
    }

    /* steps to prepare */
    public function setRecipeMethod()
    {
        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentText('{#Method#}', [], [
                'font-size' => '22',
                'color' => '#000000',
            ])
        ], [], [
            'background-color' => '#FFFFFF',
            'padding' => '20 20 20 20'
        ]);

        if (empty($this->recipe_step_list)) {
            return false;

        }

        $i = 0;
        foreach ($this->recipe_step_list as $step) {
            $i++;

            $row[] = $this->getComponentText($i, [], [
                'font-size' => 18,
                'color' => $this->color_top_bar_text_color,
                'font-weight' => 'bold',
                'margin' => '0 20 0 0',
                'width' => '35'
            ]);

            $col[] = $this->getComponentText(trim($step['description']), [], [
                'font-size' => $this->font_size,
                'color' => $this->color_top_bar_text_color,
                'padding' => '0 30 0 0',
            ]);

            if($step['time']){
                $timer[] = $this->getComponentImage('theme-icon-time-grey.png', [
                    'priority' => '1'
                ], [
                    'height' => '25',
                    'padding' => '0 3 0 3'
                ]);

                $timer[] = $this->getComponentText($step['time'] . ' {#min#}', [], [
                    'font-size' => 15,
                    'font-weight' => 'bold',
                    'padding' => '0 30 0 0',
                    'color' => $this->color_top_bar_text_color,
                ]);

                $col[] = $this->getComponentRow($timer,[],[
                    'margin' => '10 0 0 0'
                ]);


            }

            $row[] = $this->getComponentColumn($col);

            $this->layout->scroll[] = $this->getComponentRow($row,[],[
                'color' => '#000000',
                'background-color' => '#FFFFFF',
                'vertical-align' => 'top',
                'padding' => '10 20 15 20',
                'width' => '100%',
            ]);

            unset($row);
            unset($col);
            unset($timer);

/*            $this->layout->scroll[] = $this->getComponentRow([

                $this->getComponentColumn([
                    ,
                    $this->getComponentRow([
                    ], [], [
                        'width' => '100%',
                        'margin' => '5 0 0 15'
                    ])
                ], [], []),

            ], [], [
                'color' => '#000000',
                'background-color' => '#FFFFFF',
                'vertical-align' => 'top',
                'padding' => '10 20 15 20',
                'width' => '100%',
            ]);*/
        }

        return true;
    }

}