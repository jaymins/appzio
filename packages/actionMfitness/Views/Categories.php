<?php

/**
 * This is a default View file. You see many references here and in components for style classes.
 * Documentation for styles you can see under themes/example/styles
 */

namespace packages\actionMfitness\Views;

use Bootstrap\Views\BootstrapView;

class Categories extends BootstrapView
{

    /**
     * Access your components through this variable. Built-in components can be accessed also directly from the view,
     * but your custom components always through this object.
     * @var \packages\actionMfitness\Components\Components
     */
    public $components;
    public $theme;

    /*public function __construct($obj)
    {
        parent::__construct($obj);
    }*/

    public function tab1()
    {
        $this->layout = new \stdClass();

        $categories_array = $this->getData('categories', 'array');

        $parameters['action'] = ($this->getData('action', 'string') == 'categoriesexercise')? 'exerciselist':'programs';
        // TODO: fix the message's text
        if (empty($categories_array)) {
            $this->layout->scroll[] = $this->getComponentText('{#no_categories#}');
            return $this->layout;
        }

        $this->getHeader();

        $rows = array_chunk($categories_array, 2);

        foreach ($rows as $categories) {

            $row_data = [];

            foreach ($categories as $category) {
                $row_data[] = $this->components->getFitnessCategory($category,$parameters);
            }

            $this->layout->scroll[] = $this->getComponentRow($row_data, [], [
                'width' => 'auto',
                'margin' => '2 2 2 2'
            ]);
        }

        return $this->layout;
    }

    private function getHeader()
    {
        $this->layout->header[] = $this->components->themeHeader([
            //'description' => 'the 8',
            'title' => '{#fitness#}',
        ], [], [
            'background-image' => 'swiss8-header-bg.jpg',
            'height' => '150'
        ]);

        $this->layout->scroll[] = $this->getComponentSpacer(1, [], [
            'background-color' => '#222222',
        ]);

        $this->layout->scroll[] = $this->getComponentSpacer(1, [], [
            'background-color' => '#030303',
        ]);
    }

}