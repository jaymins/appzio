<?php

namespace packages\actionDitems\themes\fans\Components;

use Bootstrap\Components\BootstrapComponent;

trait getNamePicker
{

    public function getNamePicker($params = array())
    {

	    $closeDiv = new \stdClass();
	    $closeDiv->action = 'hide-div';
	    $closeDiv->keep_user_data = 1;

        /** @var BootstrapComponent $this */
        $title = isset($params['title']) ? $params['title'] : '';
        $subtitle = isset($params['subtitle']) ? $params['subtitle'] : '';
        $variable = isset($params['variable']) ? $params['variable'] : 'pick-day';
        $button_label = isset($params['button_label']) ? $params['button_label'] : '{#select#}';

        return $this->getComponentColumn(array(
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
	        $this->getComponentFormFieldText('', array(
		        'variable' => $variable,
		        'update_on_entry' => 1,
	        ), array(
		        'margin' => '20 15 20 15',
	        	'padding' => '0 10 0 10',
	        	'border-width' => '1',
	        	'border-color' => '#e9e9e9',
	        	'border-radius' => '5',
	        )),
	        $this->uiKitWideButton($button_label, array(
		        'onclick' => $closeDiv
	        ))
        ), array(), array(
            'background-color' => '#ffffff'
        ));
    }

}