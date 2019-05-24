<?php

namespace packages\actionMitems\themes\uiKit\Components;

use Bootstrap\Components\BootstrapComponent;

trait getHourPicker
{

    public function getHourPicker($params = array())
    {

	    $closeDiv = new \stdClass();
	    $closeDiv->action = 'hide-div';
	    $closeDiv->keep_user_data = 1;

        /** @var BootstrapComponent $this */
        $title = isset($params['title']) ? $params['title'] : '';
        $subtitle = isset($params['subtitle']) ? $params['subtitle'] : '';
        $var_hour = isset($params['var_hour']) ? $params['var_hour'] : 'hour';
        $var_minutes = isset($params['var_minutes']) ? $params['var_minutes'] : 'minutes';

	    $hours = ';';
	    $minutes = ';';

	    for ($i = 1; $i <= 24; $i++) {
		    $num = $i < 10 ? '0' . $i : $i;
		    $hours .= ";$i;$num";
	    }

	    for ($i = 0; $i < 60; $i+=15) {
		    $num = $i < 10 ? '0' . $i : $i;
		    $minutes .= ";$i;$num";
	    }

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
            $this->getComponentSpacer(20),
	        $this->getComponentRow(array(
		        $this->getComponentFormFieldList($hours, array(
			        'update_on_entry' => 1,
			        'variable' => $var_hour,
			        'value' => '12',
			        'style' => 'akit_double_selector_field_list'
		        )),
		        $this->getComponentFormFieldList($minutes, array(
			        'update_on_entry' => 1,
			        'variable' => $var_minutes,
			        'value' => '30',
			        'style' => 'akit_double_selector_field_list'
		        )),
	        ), array(), array(
		        'margin' => '0 0 20 0',
	        )),
	        $this->uiKitWideButton('{#select_time#}', array(
		        'onclick' => $closeDiv
	        ))
        ), array(), array(
            'background-color' => '#ffffff'
        ));
    }

}