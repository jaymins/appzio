<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMfood\Views;

use Bootstrap\Views\BootstrapView;
use phpDocumentor\Reflection\Types\Mixed;


class Listing extends BootstrapView
{

    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionMfood\Components\Components
     */
    public $components;
    public $theme;
    private $recipe_list;
    private $list_categories;
    private $selected_categories;
    private $list_ingredients;
    private $selected_ingredients;
    private $calendar_id;


    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->layout->scrollcontent_config = new \stdClass();

        $image = $this->model->getConfigParam('background_image_portrait') ? $this->model->getConfigParam('background_image_portrait') : 'swiss8-food-scroll-bg.jpg';

        $this->layout->scrollcontent_config->style_content = [
            'background-image' => $this->getImageFileName($image, [
                'priority' => 1
            ]),
            'background-size' => 'cover'
        ];

        $this->recipe_list = $this->getData('recipe_data', 'mixed');
        $this->calendar_id = $this->getData('calendar_id', 'mixed');
        $this->list_categories = $this->getData('list_categories', 'mixed');
        $this->selected_categories = $this->getData('selected_categories', 'mixed');

        $this->list_ingredients = $this->getData('list_ingredients', 'mixed');
        $this->selected_ingredients = $this->getData('selected_ingredients', 'mixed');

        if ($this->calendar_id) {
            $this->layout->header[] = $this->components->themeSmallHeader('{#replace_meal#}');
        } else {
            $this->layout->header[] = $this->components->themeSmallHeader('{#recipes#}');
        }

        $this->setHeader();
        $this->setList();

        return $this->layout;
    }

    private function setHeader()
    {
        if ($this->calendar_id) {
            $title = '{#replace_meal#}';
        } else {
            $title = '{#nutrition#}';
        }

        $this->layout->header[] = $this->components->themeHeader([
            'title' => $title,
            'links' => $this->setLinks(),
        ], [], [
            'background-image' => 'swiss8-food-header-bg.jpg'
        ]);

        $this->layout->header[] = $this->getComponentImage('swiss8-food-header-divider.jpg', [
            'priority' => 1,
        ], [
            'width' => '100%',
            'height' => '3'
        ]);
    }

    /**
     * Set header links
     * @return array
     */
    private function setLinks()
    {

        $filter = $this->getData('filter_status', 'mixed');

        if (is_array($filter)) {
            foreach ($filter as $value) {
                if ($value) {
                    $icon = 'theme-icon-small-filter-red.png';
                }
            }
        }

        if (!isset($icon)) {
            if ($this->model->getSavedVariable('recipe_filter_keyword')) {
                $icon = 'theme-icon-small-filter-red.png';
            } else {
                $icon = 'theme-icon-small-filter.png';
            }
        }


        return [
            ['text' => '{#filter#}',
                'icon' => $icon,
                'onclick' => $this->getOnclickShowDiv('category_filter',
                    array('background' => '#000000', 'transition' => 'from-bottom', 'tap_to_close' => true, 'sync_close' => true),
                    array('left' => '0', 'right' => '0', 'top' => 0, 'height' => $this->screen_height)),
            ],
            ['text' => '{#sort#}',
                'icon' => 'swiss8-icon-sorting.png',
                'onclick' => $this->getOnclickShowDiv('sorting',
                    array('background' => '#000000', 'transition' => 'from-bottom', 'tap_to_close' => true, 'sync_close' => true),
                    array('left' => '0', 'right' => '0', 'top' => 0, 'height' => $this->screen_height))
            ]
        ];
    }

    public function setList()
    {
        $type = ($this->getData('calendar_id', 'mixed')) ? $this->getData('calendar_id', 'mixed') : 'add';
        $offset = $this->getData('offset', 'num');
        $id = $offset == 15 ? 1 : $offset-15;

        if ($this->recipe_list) {
            foreach ($this->recipe_list as $content) {
                $action = [
                    'onclick' => $this->getOnclickOpenAction('nutritiondetails', false, [
                        'id' => $content->recipe['id'],
                        'sync_open' => 1,
                        'back_button' => 1
                    ])
                ];

                $list[] = $this->components->getRecipeListRow($content->recipe, $action, $type);
                $list[] = $this->components->themeFullWidthDivider(true);
                $list[] = $this->getComponentRow([
                    $this->components->themeFullWidthDivider(),
                    $this->getComponentText('{#change_date#}')
                ], [
                    'id' => 'change_' . $content->recipe['id'],
                    'visibility' => 'hidden'
                ]);

                $rows[] = $this->getComponentColumn($list);
                unset($list);
            }

            if(isset($rows)){
                $out[] = $this->getComponentColumn(
                    $rows,
                    array(
                        'id' => $id
                    ), []);

                $this->layout->scroll[] = $this->getInfiniteScroll($out,array(
                    'next_page_id' => "$offset",
                    'show_loader' => 1,
                ));
            }

        } else {
            $this->noItems();
        }
    }

    public function noItems()
    {
        $this->layout->scroll[] = $this->getComponentText('{#no_items#}');
    }

    public function getDivs()
    {

        $divs['food_start_date'] = $this->components->themeFullScreenDiv([
            'title' => '{#choose_your#} {#meal_time#}',
            'div_name' => 'food_start_date',
            'content' => $this->components->themeCalendar([
                'controller' => 'listing/add/',
                'variable' => 'food_start_date',
            ], [
                'background-color' => '#2e3237',
            ])
        ]);

        $divs['meal_time'] = $this->components->themeFullScreenDiv([
            'title' => '{#choose_your#} {#meal_time#}',
            'div_name' => 'meal_time',
            'content' => $this->components->themeTimePicker([
                'controller' => 'listing/add/',
                'variable' => 'meal_time',
                'hours' => $this->model->getHourSelectorData24h(),
                'minutes' => $this->model->getMinuteSelectorData(),
            ], [
                'background-color' => '#2e3237',
            ])
        ]);

        $divs['sorting'] = $this->components->themeFullScreenDiv([
            'title' => '{#choose_your#} {#sorting#}',
            'div_name' => 'sorting',
            'action' => 'listing/meals/',
            'content' => $this->getSortingDivContent()
        ]);

        $divs['category_filter'] = $this->components->themeFullScreenDiv([
            'title' => '{#choose_your#} {#category_filter#}',
            'div_name' => 'category_filter',
            'action' => 'listing/meals/',
            'content' => $this->getCategoryDivContent()
        ]);

        $divs['meal_type'] = $this->components->themeFullScreenDiv([
            'title' => '{#choose_your#} {#meal_type#}',
            'div_name' => 'meal_type',
            'action' => 'listing/meals/',
            'content' => $this->getMealTypeContent()
        ]);

/*        $divs['key_ingredients'] = $this->components->themeFullScreenDiv([
            'title' => '{#choose_your#} {#key_ingredients#}',
            'div_name' => 'key_ingredients',
            'content' => $this->getKeyIngredientsContent()
        ]);*/

        return $divs;
    }

    private function getSortingDivContent()
    {
        /*  $sortingItems[] = ['text' => 'Date (Newest-Oldest)', 'value' => 'date DESC'];
          $sortingItems[] = ['text' => 'Date (Oldest-Newest)', 'value' => 'date ASC'];*/
        $sortingItems[] = ['text' => 'Alphabetical order (A-Z)', 'value' => 'ASC'];
        $sortingItems[] = ['text' => 'Alphabetical order (Z-A)', 'value' => 'DESC'];
        $variable = 'sorting';
        $active = $this->getData('sorting', 'mixed');

        return $this->getComponentColumn([
            $this->components->themeRadioButtonsList($sortingItems, $variable, $active),
            $this->components->themeButton('{#sort#}',
                [
                    $this->getOnclickSubmit('Listing/sorting'),
                    $this->getOnclickHideDiv('sorting'),
                ],
                'theme-icon-forward.png'),

        ], [], [
            'width' => '100%'
        ]);
    }

    private function getCategoryDivContent()
    {
        $count_ingredients = (is_array($this->selected_ingredients)) ? count($this->selected_ingredients) : 0;
        $count_category = (is_array($this->selected_categories)) ? count($this->selected_categories) : 0;
        $count_keyword = $this->model->getSavedVariable('recipe_filter_keyword') ? 1 : 0;
        $count_filters = $count_ingredients + $count_category + $count_keyword;

        $reset_onclick[] = $this->getOnclickSetVariables(['keyword' => '','key_ingredients' => '']);
        $reset_onclick[] = $this->getOnclickSubmit('Listing/reset', [
            'sync_open' => true
        ]);
        $reset_onclick[] = $this->getOnclickHideDiv('category_filter');

        if ($count_filters > 0) {
            $filter_content[] =
                $this->getComponentRow([
                    $this->getComponentText($count_filters . '{#_filters_set#}', [
                        'uppercase' => true
                    ], [
                        'font-size' => '16',
                        'color' => $this->color_top_bar_text_color,
                    ]),
                    $this->getcomponentText(strtoupper('{#reset_filters#}'), [
                        'onclick' => $reset_onclick
                    ], [
                        'color' => $this->color_text_color,
                        'padding' => '5 15 5 15',
                        'border-radius' => '10',
                        'background-color' => $this->color_top_bar_color,
                        'font-size' => '16',
                        'text-align' => 'center',
                        'color' => $this->color_top_bar_text_color,
                        'floating' => 1,
                        'float' => 'right'
                    ])
                ], [], [
                    'margin' => '15 15 15 15',
                    'background-color' => 'grey'
                ]);
        }



        $filter_content[] = $this->components->themeFormField('{#search_by_keyword#}', 'keyword', 'text', [
            'shade' => 'light','value' => $this->model->getSavedVariable('recipe_filter_keyword')
        ]);
        $filter_content[] = $this->components->themeFormField('{#meal_type#}', false,
            'opendiv',
            ['div_name' => 'meal_type', 'value' => $count_category . '{#_selected#}','shade' => 'light']
        );

        $value = isset($this->presetData['item_tags']) ? $this->presetData['item_tags'] :
            $this->model->getSubmittedVariableByName('item_tags');


/*        $filter_content[] = $this->getComponentRow(array(
            $this->getComponentFormFieldText($value, array(
                'hint' => '{#type_tag#}',
                'variable' => 'item_tags',
                'id' => 'component_id',
                'suggestions_update_method' => 'gettags',
                'suggestions' => [],
                'suggestions_placeholder' => $this->getComponentText('$value', array(), array(
                    'font-size' => 15,
                    'color' => '#333333',
                    'background-color' => '#ffffff',
                    'padding' => '12 10 12 10',
                )),
            ), array(
                'font-style' => 'italic',
                'color' => '#8C8C8C',
                'padding' => '0 20 0 20',
                'font-size' => '16'
            ))
        ), array(), array(
            'margin' => '5 0 5 0',
        ));*/


        $filter_content[] = $this->components->themeFormField('{#key_ingredients#}', 'key_ingredients', 'text',
            [
                'shade' => 'light',
                'custom_params' => [
                    'id' => 'component_id',
                    'suggestions_update_method' => 'gettags',
                    'suggestions' => [],
                    'suggestions_placeholder' => $this->getComponentText('$value', array(), array(
                        'font-size' => 14,
                        'color' => '#ffffff',
                        'background-color' => '#545050',
                        'padding' => '12 10 12 10',
                    ))
                ]
            ]
        );

        $onclick[] = $this->getOnclickSubmit('listing/savefilters/');
        $onclick[] = $this->getOnclickHideDiv('category_filter');

        $filter_content[] = $this->components->themeButton('{#update_filters#}',
            $onclick, 'theme-icon-forward.png');
        return $this->getComponentColumn($filter_content, [], [
            'width' => '100%'
        ]);
    }

    private function getMealTypeContent()
    {

        $list_content = [];
        $selected_categories = $this->getData('selected_categories', 'mixed');

        if ($this->list_categories) {
            foreach ($this->list_categories as $category) {
                if ($category->type) {
                    if ($selected_categories) {
                        $is_active = (in_array($category->type->id, $selected_categories)) ? '1' : '0';
                    } else {
                        $is_active = 0;
                    }

                    $list_content[] = [
                        'text' => $category->type->name,
                        'variable' => $category->type->id,
                        'value' => $category->type->id,
                        'is_active' => $is_active
                    ];
                }
            }
        }

        return $this->getComponentColumn([
            $this->components->themeCheckboxList($list_content, 'meal'),
            $this->components->themeButton('{#save#}',
                [
                    $this->getOnclickSubmit('Listing/meal'),
                    $this->getOnclickHideDiv('meal_type')
                ],
                'download.png'),
        ], [], [
            'width' => '100%'
        ]);
    }

    private function getKeyIngredientsContent()
    {
        $list_content = [];
        $selected_ingredients = $this->getData('selected_ingredients', 'mixed');





        if ($this->list_ingredients) {
            foreach ($this->list_ingredients as $key_ingredient) {
                $is_active = ($selected_ingredients && in_array($key_ingredient->recipe_ingredient['id'], $selected_ingredients)) ? '1' : '0';
                $list_content[] = [
                    'text' => $key_ingredient->recipe_ingredient['name'],
                    'variable' => $key_ingredient->recipe_ingredient['id'],
                    'value' => $key_ingredient->recipe_ingredient['id'],
                    'is_active' => $is_active
                ];
            }
            return $this->getComponentColumn([
                $this->components->themeCheckboxList($list_content, 'ingredient'),
                $this->components->themeButton('{#save#}',
                    [
                        $this->getOnclickSubmit('Listing/ingredients'),
                        $this->getOnclickHideDiv('key_ingredients')
                    ],
                    'download.png'),
            ], [], [
                'width' => '100%'
            ]);
        }
    }

}