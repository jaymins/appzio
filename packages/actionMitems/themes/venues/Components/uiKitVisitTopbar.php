<?php

namespace packages\actionMitems\themes\venues\Components;

use Bootstrap\Components\BootstrapComponent;

trait uiKitVisitTopbar
{

    public $corner_size = 7;

    public function uiKitVisitTopbar($image, string $title, $onclick = false, $custom_styles = array(), $secondImage = false, $secondOnclick = array()) {
        /** @var BootstrapComponent $this */

        if ( empty($onclick) ) {
            $onclick = $this->getOnclickGoHome();
        }

        $styles = array(
            'width' => 'auto',
            'height' => '50',
            'vertical-align' => 'middle',
        );

        if ( !empty($custom_styles) ) {
            $styles = array_merge($styles, $custom_styles);
        }

        return $this->getComponentRow(array_merge(
            $this->getLeftBar($image, $onclick, $title),
            $this->getRightBar($secondImage, $secondOnclick)
        ), array(), $styles);
    }

    private function getLeftBar($image, $onclick, $title) {
        return array(
            $this->getComponentColumn(array(
                $this->getComponentImage($image, array(), array(
                    'height' => '25',
                )),
            ), array(
                'onclick' => $onclick,
            ), array(
                'width' => $this->screen_width / $this->corner_size,
                'text-align' => 'left',
                'padding' => '0 0 0 15',
            )),
            $this->getComponentColumn(array(
                $this->getComponentText($title, array(), array(
                    'font-size' => '17',
                    'color' => '#ffffff',
                    'text-align' => 'center',
                )),
            ), array(), array(
                'text-align' => 'center',
                'width' => $this->screen_width - (2 * ($this->screen_width / $this->corner_size))
            ))
        );
    }

    private function getRightBar($secondImage, $secondOnclick) {

        if ( empty($secondImage) ) {
            return array();
        }

        return array(
            $this->getComponentColumn(array(
                $this->getComponentImage($secondImage, array(), array(
                    'height' => '25',
                )),
            ), array(
                'onclick' => $secondOnclick,
            ), array(
                'width' => $this->screen_width / $this->corner_size,
                'text-align' => 'left',
                'padding' => '0 0 0 15',
            ))
        );
    }

}