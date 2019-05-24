<?php

namespace packages\actionMitems\themes\clubs\Components;

use Bootstrap\Components\BootstrapComponent;

trait CategoryAccordion
{
    public function getCategoryAccordion($categories = array())
    {
        /** @var BootstrapComponent $this */
        $categories = array_map(function($category) {
            $value = isset($this->model->sessionGet('categories')['category|' . $category->id]) ?
                $this->model->sessionGet('categories')['category|' . $category->id] : '';

            $data = array (
                'id' => $category->id,
                'show' => array (
                    'icon' => $category->picture . '_white.png',
                    'title' => '',
                    'description' => $this->getCategoryDescription($category->description, 'ui_accordion_line_show_description'),
                    'icon-back' => 'anonymous.png'
                ),
                'hide' => array (
                    'icon' => $category->picture . '_red.png',
                    'title' => '',
                    'description' => $this->getCategoryDescription($category->description, 'ui_accordion_line_hide_description'),
                    'icon-back' => 'anonymous.png'
                ),
                'hidden' => array(
                    'input' => 'text',
                    'variable' => 'category|' . $category->id,
                    'description' => 'text input description',
                    'value' => $value
                )
            );

            if ($value) {
                $data['expanded'] = true;
            }

            return $data;
        }, $categories);

        usort($categories, function ($a, $b) {
            return $a['id'] > $b['id'];
        });

        return $this->uiKitAccordion($categories);
    }
}