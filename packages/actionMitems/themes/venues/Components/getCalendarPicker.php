<?php

namespace packages\actionMitems\themes\venues\Components;

use Bootstrap\Components\BootstrapComponent;

trait getCalendarPicker
{
    public $isLiked;

    public function getCalendarPicker($params = array())
    {
        $closeDiv = new \stdClass();
        $closeDiv->action = 'hide-div';
        $closeDiv->keep_user_data = 1;

        /** @var BootstrapComponent $this */
        $title = isset($params['title']) ? $params['title'] : '';
        $subtitle = isset($params['subtitle']) ? $params['subtitle'] : '';
        $variable = isset($params['variable']) ? $params['variable'] : 'pick-date';

        return $this->getComponentColumn(array_merge(
            array(
                $this->getComponentRow(array(
                    $this->getComponentText($title, array(), array(
                        'color' => '#ffffff',
                        'font-size' => '14',
                        'width' => '100%',
                    )),
                    $this->getComponentImage('cross-sign.png', array(
                        'onclick' => $closeDiv
                    ), array(
                        'width' => '15',
                        'floating' => '1',
                        'float' => 'right',
                        'margin' => '2 0 0 0'
                    ))
                ), array(), array(
                    'padding' => '10 20 10 20',
                    'background-color' => '#4a4a4a',
                    'shadow-color' => '#33000000',
                    'shadow-radius' => '1',
                    'shadow-offset' => '0 3',
                    'margin' => '0 0 20 0'
                )),
                $this->getReminderDivSubtitle($subtitle),
                $this->getComponentSpacer(20),
                $this->getComponentCalendar(array(
                    'date' => time(),
                    'update_on_entry' => 1,
                    'variable' => $variable,
                    'date_format' => 'MM / dd / yyyy',
                    'min_date' => '0',
                    'selection_style' => array(
                        'color' => '#ffffff',
                        'background-color' => '#FFCC00'
                    ),
                ), array(
                    'width' => '100%',
                    'margin' => '0 15 0 15',
                )),
            ),
            $this->getCalendarPickerButtons( $params )
        ), array(), array(
            'background-color' => '#ffffff'
        ));
    }

    public function getCalendarPickerButtons( $params ) {

        $closeDiv = new \stdClass();
        $closeDiv->action = 'hide-div';
        $closeDiv->keep_user_data = 1;

        $buttons = [];

        if ( isset($params['show_random']) AND $params['show_random'] == true ) {
            $buttons[] = $this->uiKitWideButton('{#random#}', array(
                'onclick' => [
                    $closeDiv,
                    $this->getOnclickSubmit( 'pick-random' )
                ]
            ), array(
                'parent_style' => 'uikit_wide_button_text_yellow',
                'backround-color' => '#FFCC00',
            ));
        }

        $buttons[] = $this->uiKitWideButton('{#select_date#}', array(
            'onclick' => $closeDiv
        ));

        return $buttons;
    }

}