<?php

namespace packages\actionMitems\themes\cityapp\Components;

use Bootstrap\Views\BootstrapView;

trait getSearchItem
{

    public function getSearchItem(string $icon, string $title, string $side_content = '', array $parameters=array(), array $styles=array())
    {
        /** @var BootstrapView $this */

        $events = [];
        $title_row = [];
        $text_params = [];

        $title_row[] = $this->getComponentImage($icon, array(
            'priority' => 9
        ), array(
            'width' => '60',
            'height' => '60',
            'crop' => 'round',
            'margin' => '0 10 0 0',
        ));

        $title_row[] = $this->getComponentColumn(array(
            $this->getComponentText($title, $text_params, array(
                'color' => '#393939',
                'font-size' => '20',
                'font-weight' => 'bold',
            ))
        ), array(), array(
            'width' => ( $side_content ? '50%' : 'auto' ),
            'padding' => ( $side_content ? '0 5 0 0' : '0 15 0 0 ' ),
        ));

        if ( $side_content ) {
            $title_row[] = $this->getComponentColumn(array(
                $this->getComponentText($side_content, $text_params, array(
                    'color' => '#777d81',
                    'font-size' => '12',
                    'text-align' => 'right',
                )),
            ), array(), array(
                'width' => '35%'
            ));
        }

        if ( isset($parameters['onclick']) AND $parameters['onclick'] ) {
            $events['onclick'] = $parameters['onclick'];
        }

        $data[] = $this->getComponentRow($title_row, $events, array(
            'width' => 'auto',
            'padding' => '0 15 0 15',
            'vertical-align' => 'middle',
        ));

        if ( isset($parameters['divider']) AND $parameters['divider'] ) {
            $data[] = $this->getComponentDivider(array(
                'style' => 'article-uikit-divider'
            ));
        }

        $behaviour = [];
        $bhv_params = ['id', 'visibility'];

        foreach ( $bhv_params as $bhv_param ) {
            if ( isset($parameters[$bhv_param]) ) {
                $behaviour[$bhv_param] = $parameters[$bhv_param];
            }
        }

        return $this->getComponentColumn($data, $behaviour, array_merge(array(
            'width' => 'auto',
            'padding' => ( isset($parameters['divider']) ? '0 0 0 0' : '10 0 10 0' )
        ),
            $styles
        ));
    }

}