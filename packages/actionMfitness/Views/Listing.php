<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMfitness\Views;

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
    private $exercise_data;
    private $list_categories;
    private $selected_categories;
    private $list_ingredients;
    private $selected_ingredients;
    private $calendar_id;


    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->exercise_data = $this->getData('exercise_data', 'mixed');
        $this->calendar_id = $this->getData('calendar_id', 'mixed');
        $this->list_categories = $this->getData('list_categories', 'mixed');
        $this->selected_categories = $this->getData('selected_categories', 'mixed');
        $this->list_ingredients = $this->getData('list_ingredients', 'mixed');
        $this->selected_ingredients = $this->getData('selected_ingredients', 'mixed');

        $this->layout->header[] = $this->components->themeSmallHeader('{#add_to_calendar#}');
        $this->setHeader();
        $this->setList();
        return $this->layout;
    }

    /**
     * @return array
     */
    private function setHeader()
    {
        if ($this->calendar_id) {
            $title = '{#replace_meal#}';
        } else {
            $title = '{#fitness#}';
        }


        return $this->layout->header[] =
            $this->components->themeHeader([
                'title' => $title,
                'links' => $this->setLinks(),
            ], [], [
                'background-image' => 'swiss8-header-bg.jpg'
            ]);
    }

    /**
     * Set header links
     * @return array
     */
    private function setLinks()
    {

        $search = $this->getData('search_active', 'bool');

        $icon = $search ? 'theme-icon-small-filter-red.png' : 'swiss8-icon-filter.png';

        return [
            [
                'text' => '{#search#}',
                'icon' => $icon,
                'onclick' => $this->getOnclickShowDiv('search',$this->components->themeDivOpenParams(),$this->components->themeDivOpenLayout())
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



    public function setList()
    {
        if ($this->exercise_data) {

            $offset = $this->getData('offset', 'num');
            $id = $offset == 15 ? 1 : $offset-15;

            foreach ($this->exercise_data as $content) {
                $action = [];
                $col[] = $this->components->getFitnessExerciseRow($content, $action = [], $type = 'add');
                $col[] = $this->components->themeFullWidthDivider(true);
                $col[] = $this->getComponentRow([
                    $this->components->themeFullWidthDivider(),
                    $this->getComponentText('{#change_date#}')
                ], ['id' => 'change_'.$content->id,
                        'visibility' => 'hidden']
                );

                $rows[] = $this->getComponentColumn($col);
                unset($col);
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
        $this->layout->scroll[] = $this->getComponentText('{#no_items#}',['style' => 'theme_general_centered_grey_text']);
    }

    public function getDivs()
    {
        $divs['training_start_date'] = $this->components->themeFullScreenDiv([
            'title' => '{#choose_your#} {#training_start_date#}',
            'div_name' => 'training_start_date',
            'content' => $this->components->themeCalendar([
                'controller' => 'listing/add/',
                'variable' => 'training_start_date',
            ], [
                'background-color' => '#2e3237',
            ])
        ]);

        $divs['training_time'] = $this->components->themeFullScreenDiv([
            'title' => '{#choose_your#} {#training_time#}',
            'div_name' => 'training_time',
            'content' => $this->components->themeTimePicker([
                'controller' => 'listing/add/',
                'variable' => 'training_time',
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


        $divs['search'] = $this->components->themeFullScreenDiv([
            'title' => '{#search#}',
            'div_name' => 'search',
            'action' => 'listing/search/',
            'content' => $this->searchDiv()
        ]);
        return $divs;
    }


    private function searchDiv(){

        $filter_content[] = $this->getComponentSpacer(10);

        $filter_content[] = $this->components->themeFormField('{#search_by_keyword#}', 'fitness_keyword', 'text', [
            'value' => $this->model->getSubmittedVariableByName('fitness_keyword'),
            'shade' => 'light'
        ]);

        $onclick_cancel[] = $this->getOnclickSetVariables(['fitness_keyword' => '']);
        $onclick_cancel[] = $this->getOnclickSubmit('listing/cancelsearch/');
        $onclick_cancel[] = $this->getOnclickHideDiv('search');

        $onclick_search[] = $this->getOnclickSubmit('listing/search/');
        $onclick_search[] = $this->getOnclickHideDiv('search');

        $filter_content[] = $this->components->themeButton('{#cancel#}',
            $onclick_cancel,
            'icon_arrow_back.png','grey');

        $filter_content[] = $this->components->themeButton('{#search#}',
            $onclick_search, 'theme-icon-search.png');

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
            $this->components->themeButton('{#sort#}',
                [
                    $this->getOnclickSubmit('Listing/sorting'),
                    $this->getOnclickHideDiv('sorting'),
                ],
                'download.png'),
        ], [], [
            'width' => '100%'
        ]);
    }

    /**
     * @param mixed $list_categories
     */
    /*public function setListCategories($list_categories)
    {
        $this->list_categories = $list_categories;
    }*/
}