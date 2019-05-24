<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMfood\Views;

use Bootstrap\Views\BootstrapView;


class Shoppinglist extends BootstrapView
{

    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionMfood\Components\Components
     */
    public $components;
    public $theme;

    private $type_list;

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->type_list = $this->getData('category', 'array');
        $ingredient_list = $this->getData('ingredient_list', 'array');

        $this->layout->header[] = $this->components->themeSmallHeader('{#shopping_list#}');

        $this->setHeader();
        $this->layout->scroll[] = $this->setList($ingredient_list);

        $this->setFooterFilter();

        return $this->layout;
    }

    public function getAddItemDiv(){
/*        $category_id = $this->model->sessionGet('category_id');
        $value = ($category_id && isset($this->type_list[$category_id])) ? $this->type_list[$category_id] : '';*/

        $firstrow[] = $this->getComponentText('{#add_my_own_items#}',['uppercase' => true],[
            'color' => '#ffffff','font-size' => '14','margin' => '10 20 10 20',
            'font-weight' => 'bold'
        ]);
        $firstrow[] = $this->getComponentImage('swiss8-icon-x.png',[],[
            'floating' => '1', 'float' => 'right','width' => '20','margin' => '10 20 10 20'
        ]);

        $content[] = $this->getComponentRow($firstrow,[
            'onclick' => [
                $this->getOnclickHideDiv('add_my_own_items'),
            ]
        ],[
            'vertical-align' => 'middle'
        ]);

        $content[] = $this->components->themeFormField('{#ingredient_category#}',
            'ingredient_category', 'opendiv',
            [
                //'value' => $value,
                'icon_right' => 'theme-icon-white-category-tag.png',
                'shade' => 'light',
                'div_name' => 'ingredient_category',
            ]
        );

        $content[] = $this->components->themeFormField('{#name_of_item#}',
            'name',
            'text',
            ['icon_right' => 'theme-icon-white-text.png','shade' => 'light']
        );

        $content[] = $this->getComponentRow([
            $this->getComponentColumn([
                $this->components->themeButton(strtoupper('{#add#}'), [
                    $this->getOnclickSubmit('shoppinglist/add/'),
                    $this->getOnclickHideElement('add_my_own_items'),
                    $this->getOnclickShowElement('add_my_own_items_button'),
                ], false,'black'),
            ], [], [
                'width' => '30%',
                'padding' => '0 -15 0 -15'
            ]),
            $this->getComponentColumn([
                $this->components->themeButton(strtoupper('{#add_+_add_another#}'), [
                    $this->getOnclickSubmit('shoppinglist/add/'),
                ], false,'black'),
            ], [
            ], [
                'width' => '70%',
                'padding' => '0 -15 0 -15'
            ])

        ], [], [
            'width' => '100%',
            'margin' => '0 15 0 15'
        ]);

        return $this->getComponentColumn($content,
            ['id' => 'add_my_own_items','visibility' => 'hidden'],
            [
                'background-color' => '#33383E'
            ]);

    }

    public function getDivs()
    {

        $divs['add_my_own_items'] = $this->getAddItemDiv();

        $divs['start_date'] = $this->components->themeFullScreenDiv([
            'title' => '{#choose_date#}',
            'div_name' => 'start_date',
            'content' => $this->components->themeCalendar([
                'controller' => 'shoppinglist/filter/',
                'variable' => 'start_date',
            ], [
                'background-color' => '#2e3237',
            ])
        ]);//to_date
        $divs['to_date'] = $this->components->themeFullScreenDiv([
            'title' => '{#choose_date#}',
            'div_name' => 'to_date',
            'content' => $this->components->themeCalendar([
                'controller' => 'shoppinglist/filter/',
                'variable' => 'to_date',
            ], [
                'background-color' => '#2e3237',
            ])
        ]);

        $divs['ingredient_category'] = $this->getItemCategoriesDiv();

        return $divs;
    }

    public function getItemCategoriesDiv(){
        foreach($this->type_list as $key=>$item){
            $vals['text'] = $item;
            $vals['value'] = $key;
            $list[] = $vals;
        }

        if(!isset($list)){
            $list  = array();
        }

        $content = $this->components->themeCheckboxList($list,
            'ingredient_category',
            true,[
                'update_on_entry' => 'ingredient_category',
                'submit' => 'shoppinglist/setingredientcategory/',
                'div' => 'ingredient_category']);


        return $this->components->themeFullScreenDiv([
            'title' => '{#ingredient_category#}',
            'div_name' => 'ingredient_category',
            'content' => $content]);
    }

    public function addItemDivOpenParams()
    {
        return [
            'background' => '#33383E',
            'transition' => 'tablet',
            'tap_to_close' => true,
            'sync_close' => true];
    }

    public function addItemDivOpenLayout()
    {
        return [
            'left' => '0',
            'right' => '0',
            'top' => 182,
            'height' => '280'];
    }


    public function setHeader()
    {
        $this->layout->header[] =
            $this->components->themeHeader([
                'title' => '{#shopping_list#}',
                'content' =>
                    $this->getComponentColumn([
                        $this->getComponentText(' ', [], [
                            'height' => '1',
                            'background-color' => '#1D1D1D'
                        ]),
                        $this->getComponentText(' ', [], [
                            'height' => '1',
                            'background-color' => '#000000'
                        ]),
                        $this->getDivButtonComponent([
                            'title' => strtoupper('{#add_my_own_items#}'),
                            'icon' => 'theme-icon-edit.png',
                        ],
                            [
                            'onclick' => [
                                $this->getOnclickShowDiv('add_my_own_items',$this->addItemDivOpenParams(),$this->addItemDivOpenLayout()),
                            ],'id' => 'add_my_own_items_button'
                        ], [
                            'width' => '100%',
                        ]),
                        $this->getComponentText(' ', [], [
                            'height' => '1',
                            'background-color' => '#000000'
                        ])
                    ], [], [])
            ], [], [
                'background-image' => 'shopping_list.png'
            ]);

    }

    public function getDivButtonComponent($content, $action, $style)
    {

        $style['vertical-align'] = 'middle';

        $component [] = $this->getComponentText($content['title'], [], [
            'background' => 'blur',
            'color' => $this->color_top_bar_text_color,
            'width' => '90%',
            'padding' => '15 15 15 15',
            'font-size' => '14',
            'font-weight' => 'bold'
        ]);
        $component [] = $this->getComponentImage($content['icon'], [
            'priority' => '1'
        ], ['width' => '20']);
        return $this->getComponentRow($component, $action, $style);
    }

    private function getListHeader()
    {
        $filter_date = date('d.m.Y', $this->model->getSavedVariable('start_date', time())) . ' - ' .
            date('d.m.Y', $this->model->getSavedVariable('to_date', strtotime('+7 days', time())));
        return $this->getComponentRow([
            $this->getComponentColumn([
                $this->getComponentText($filter_date, [],
                    [
                        'vertical-alignment' => 'middle',
                        'padding' => '15 15 15 15',
                        'color' => $this->color_top_bar_text_color
                    ])
            ], [], [
                'width' => '80%',
                'vertical-align' => 'middle',
                'text-align' => 'left'
            ]),
            $this->getComponentColumn([]),
        ], [], [

            'width' => '100%',
            'background-color' => '#FFFFFF',
        ]);
    }

    public function setList($ingredient_list)
    {
        $category[] = $this->getListHeader();
        $category[] = $this->getComponentText(' ', [], [
            'height' => '1',
            'background-color' => '#383d44'
        ]);

        if (!empty($ingredient_list)) {
            foreach ($ingredient_list as $cat => $item) {
                $list_content = [];
                foreach ($item['item'] as $key => $ingredient) {
                    if (!$ingredient['name']) {
                        continue;
                    }
                    $active = ($ingredient['quantity'] > 0) ? 0 : 1;

                    if (!$ingredient['custom_id']) {
                        $quantity = ($ingredient['order_quantity'] > 0 && $ingredient['quantity'] > 0) ? $ingredient['quantity'] : $ingredient['order_quantity'];
                    } else {
                        $quantity = '';
                    }
                    $unit = ($ingredient['unit']) ? $ingredient['unit'] : '';
                    $list_content[] = [
                        'text' =>  $quantity . ' ' . $unit .' ' .$ingredient['name'],
                        'is_active' => $active,
                        'record_id' => $cat . '-' . $key,
                        'complete_id' => $ingredient['complete_id']
                    ];
                }
                $category[] = $this->getComponentRow([
                    $this->getComponentColumn([
                        $this->getComponentImage($item['category']['icon'], [
                            'priority' => '1'
                        ], [
                            'width' => '50%',
                            'padding' => '5 5 5 5'
                        ]),
                        $this->getComponentText($item['category']['name'], [], [
                            'font-size' => '12',
                            'text-align' => 'center'
                        ])
                    ], [], [
                        'width' => '20%',
                        'background-color' => '#FFFFFF',
                        'text-align' => 'center',
                        'vertical-align' => 'top',
                        'padding' => '5 5 5 5',
                    ]),
                    $this->getComponentColumn($this->components->getShoppingListCategory($list_content), [
                    ], [
                        'width' => '80%',
                        'vertical-align' => 'middle',
                        'text-align' => 'center'
                    ]),
                ], [], []);
                $category[] = $this->getComponentText(' ', [], [
                    'height' => '1',
                    'background-color' => '#383d44'
                ]);
            }
            $this->layout->scroll[] = $this->getComponentColumn($category, [], [
                'width' => '100%'
            ]);
        } else {
            $this->layout->scroll[] = $this->getComponentColumn([
                $this->getListHeader(),
                $this->noItems()
            ], [], [
                'width' => '100%'
            ]);
        }
    }

    private function setFooterFilter()
    {

        $value = time();

        $menu[] = $this->components->themeFormField('{#select_date#}', 'start_date', 'opendiv',
            ['div_name' => 'start_date', 'icon_right' => 'swiss8-icon-calendar-input.png','shade' => 'light','value' => $value]);
        $menu[] = $this->components->themeFormField('{#select_date#}', 'to_date', 'opendiv',
            ['div_name' => 'to_date', 'icon_right' => 'swiss8-icon-calendar-input.png','shade' => 'light']);
        $menu[] = $this->components->themeButton('{#refresh_list#}',
            [
                $this->getOnclickSubmit('shoppinglist/filter/'),
                $this->getOnclickHideElement('controls', ['transition' => 'none']),
                $this->getOnclickHideElement('close-controls', ['transition' => 'none']),
                $this->getOnclickShowElement('open-controls', ['transition' => 'none']),
            ],
            'theme-icon-refresh-white.png', 'black');
        $this->layout->footer[] = $this->components->themeExpandableFooter('{#select_your_cook_days#}',
            'theme-icon-calendar.png', $menu);
    }

    public function noItems()
    {
        return $this->getComponentRow([
            $this->getComponentText('{#no_items#}', [],
                [
                    'vertical-alignment' => 'middle',
                    'padding' => '15 15 15 15',
                    'color' => $this->color_text_color
                ])
        ], [], [
            'text-align' => 'center',
            'width' => '100%',
            'background-color' => $this->color_background_color
        ]);
    }
}