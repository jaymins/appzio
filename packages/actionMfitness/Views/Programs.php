<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMfitness\Views;

use Bootstrap\Views\BootstrapView;

class Programs extends BootstrapView
{

    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionMfitness\Components\Components
     */
    public $components;
    public $theme;

    public function tab1()
    {
        $this->layout = new \stdClass();

        $programs = $this->getData('programs', 'array');

       /* $this->layout->scrollcontent_config->style_content = [
            'background-image' => $this->getImageFileName('swiss8-scroll-bg.jpg', [
                'priority' => 1
            ]),
            'background-size' => 'cover'
        ];*/

        $this->getHeader();

        // TODO: fix the message's text
        if (empty($programs)) {
            $this->layout->scroll[] = $this->getComponentText('{#no_programs#}', [], [
                'color' => '#ffffff',
                'text-align' => 'center',
                'padding' => '20 20 20 20',
            ]);
            return $this->layout;
        }

        $rows = [];

        foreach ($programs as $program) {
            $rows[] = $this->components->getFitnessProgramRow($program);
        }

        $this->layout->scroll[] = $this->getComponentColumn($rows, [], [
            'width' => '100%',
        ]);

        return $this->layout;
    }

    private function getHeader()
    {
        $category_data = $this->getData('category_data', 'object');

        $this->layout->header[] = $this->components->themeSmallHeader('{#programs#}');
        $this->layout->header[] = $this->components->themeHeader([
            'title' => isset($category_data->name) ? $category_data->name : '{#fitness#}',
            'links' => $this->setLinks(),
        ], [], [
            'background-image' => 'swiss8-header-bg.jpg',
            'height' => '80'
        ]);

/*        $this->layout->header[] = $this->getComponentImage('swiss8-header-divider.jpg', [
            'priority' => 1,
        ], [
            'width' => '100%',
            'height' => '4'
        ]);*/
    }

    private function setLinks()
    {

        $icon = $this->getData('search_active', 'bool') ? 'theme-icon-small-filter-red.png' : 'swiss8-icon-filter.png';

        return [
            [
                'text' => '{#category_filter#}',
                'icon' => $icon,
                'onclick' => $this->getOnclickShowDiv('category_filter',
                    $this->components->themeDivOpenParams(),
                    $this->components->themeDivOpenLayout())
            ],
            [
                'text' => '{#sort#}',
                'icon' => 'swiss8-icon-sorting.png',
                'onclick' => $this->getOnclickShowDiv('sorting',
                    array('background' => '#000000', 'transition' => 'tablet', 'tap_to_close' => true, 'sync_close' => true),
                    array('left' => '0', 'right' => '0', 'top' => 0, 'height' => $this->screen_height))
            ]
        ];
    }


    private function getCategoryDivContent()
    {
        $reset_onclick[] = $this->getOnclickSetVariables(['keyword' => '']);
        $reset_onclick[] = $this->getOnclickSubmit('Programs/resetfilters/', [
            'sync_open' => true
        ]);
        $reset_onclick[] = $this->getOnclickHideDiv('category_filter');

        if ($this->getData('search_active', 'bool')) {
            $filter_content[] =
                $this->getComponentRow([
                    $this->getcomponentText(strtoupper('{#reset_filters#}'), [
                        'onclick' => $reset_onclick
                    ], [
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
        } else {
            $filter_content[] = $this->getComponentSpacer(15);
        }

        $filter_content[] = $this->components->themeFormField('{#search_by_keyword#}', 'keyword', 'text', [
            'shade' => 'light',
            'value' => $this->model->getSavedVariable('tmp_program_filter')
        ]);

/*
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
        );*/

        $onclick[] = $this->getOnclickHideDiv('category_filter');
        $onclick[] = $this->getOnclickSubmit('Programs/savefilters/');

        $filter_content[] = $this->components->themeButton('{#update_filters#}',
            $onclick, 'theme-icon-forward.png');
        return $this->getComponentColumn($filter_content, [], [
            'width' => '100%'
        ]);
    }


    private function getSortingDivContent()
    {
        $sortingItems[] = ['text' => 'Alphabetical order (A-Z)', 'value' => 'ASC'];
        $sortingItems[] = ['text' => 'Alphabetical order (Z-A)', 'value' => 'DESC'];
        $variable = 'sorting';
        $active = $this->getData('sorting', 'mixed');

        return $this->getComponentColumn([
            $this->components->themeRadioButtonsList($sortingItems, $variable, $active),
            $this->components->themeButton('{#save#}',
                [
                    $this->getOnclickSubmit('Programs/Sorting'),
                    $this->getOnclickHideDiv('sorting'),
                ],
                'download.png'),

        ], [], [
            'width'=>'100%'
        ]);
    }

    public function getDivs()
    {
        $divs['sorting'] = $this->components->themeFullScreenDiv([
            'title' => '{#sort_by#}',
            'div_name' => 'sorting',
            'content' => $this->getSortingDivContent()
        ]);

        $divs['category_filter'] = $this->components->themeFullScreenDiv([
            'title' => '{#choose_your#} {#category_filter#}',
            'div_name' => 'category_filter',
            'action' => 'programs/default/',
            'content' => $this->getCategoryDivContent()
        ]);

        return $divs;
    }

}