<?php

namespace packages\actionMitems\themes\uiKit\Components;

use Bootstrap\Components\BootstrapComponent;

trait VisitCategories
{
    public function getVisitCategories($categories = array())
    {
        
        /** @var BootstrapComponent $this */
        $categories = array_map(function ($category) {
            $notes = $category->category_relations[0]->description ?? '{#no_notes_added#}';

            $data = array(
                'id' => $category->id,
                // 'expanded' => true,
                'show' => array(
                    'icon' => $category->picture . '_white.png',
                    'title' => '',
                    'description' => $this->getCategoryDescription($category->description, 'ui_accordion_line_show_description'),
                    'icon-back' => 'anonymous.png'
                ),
                /*
                'hide' => array(
                    'icon' => $category->picture . '_red.png',
                    'title' => '',
                    'description' => $this->getCategoryDescription($category->description, 'ui_accordion_line_hide_description'),
                    'icon-back' => 'anonymous.png'
                ),
                */
                'hidden' => array(
                    'variable' => 'category|' . $category->id,
                    'description' => $notes,
                )
            );

            if ( $notes ) {
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