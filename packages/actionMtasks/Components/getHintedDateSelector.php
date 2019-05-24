<?php

namespace packages\actionMtasks\Components;
use Bootstrap\Components\BootstrapComponent;

trait getHintedDateSelector {

    /**
     * @param $content string, no support for line feeds
     * @param array $styles 'margin', 'padding', 'orientation', 'background', 'alignment', 'radius', 'opacity',
     * 'orientation', 'height', 'width', 'align', 'crop', 'text-style', 'font-size', 'text-color', 'border-color',
     * 'border-width', 'font-android', 'font-ios', 'background-color', 'background-image', 'background-size',
     * 'color', 'shadow-color', 'shadow-offset', 'shadow-radius', 'vertical-align', 'border-radius', 'text-align',
     * 'lazy', 'floating' (1), 'float' (right | left), 'max-height', 'white-space' (no-wrap), parent_style
     * @param array $parameters selected_state, variable, onclick, style
     * @return \stdClass
     */

    public function getHintedDateSelector($hint,$variablename,$time, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapComponent $this */

        $parameters['variable'] = $variablename;

        /* error handling */
        if($this->model->getValidationError($variablename)) {
            $error[] = $this->getComponentText($hint .' ', array('style' => 'steps_hint','uppercase' => true));
            $error[] = $this->getComponentText($this->model->getValidationError($variablename),array('style' => 'steps_error'));
            $out[] = $this->getComponentRow($error,array(),array('width' => '100%'));
        } else {
            $out[] = $this->getComponentText($hint, array('style' => 'steps_hint','uppercase' => true));
        }

        /* hinter for the field */

        $yearvalue =  '2017';
        $dayvalue =  date('d');
        $monthvalue = date('m') ;

        $date[] = $this->getComponentText($monthvalue,array('style' => 'mtask_popup_chooser','variable' => $variablename.'_month'));
        $date[] = $this->getComponentText(' / ',array('style' => 'mtask_popup_chooser'));
        $date[] = $this->getComponentText($dayvalue,array('style' => 'mtask_popup_chooser','variable' => $variablename.'_day'));
        $date[] = $this->getComponentText(' / ',array('style' => 'mtask_popup_chooser'));
        $date[] = $this->getComponentText($yearvalue,array('style' => 'mtask_popup_chooser','variable' => $variablename.'_year'));

            $row[] = $this->getComponentRow($date,array('style' => 'mtask_popup_chooser','variable' => $variablename));
            $row[] = $this->getComponentImage('form-arrow-down.png',array('style' => 'mtasks_select_icon'));

            $onclick[] = $this->getOnclickHideElement('header');
            $onclick[] = $this->getOnclickHideElement($variablename.'hinter');
            $onclick[] = $this->getOnclickShowElement($variablename.'selector');

        $out[] = $this->getComponentRow($row,array('onclick'=>$onclick,'id' => $variablename.'hinter'),array('margin' => '8 20 8 40'));

        /* data that's shown after click, hidden by default */
            $closeclick[] = $this->getOnclickHideElement($variablename.'selector');
            $closeclick[] = $this->getOnclickShowElement($variablename.'hinter');

            $closeimg[] = $this->getComponentImage('form-arrow-up.png',array('onclick' => $closeclick,'style' => 'mtasks_select_icon'),array());
            $openstate[] = $this->getComponentRow($closeimg,array('onclick'=>$onclick),array('margin' => '8 20 8 40'));

            $openstate[] = $this->getDateSelector($variablename);
            $openstate[] = $this->getComponentText('{#choose#}',array('onclick' => $closeclick,'style' => 'mtasks_small_btn'));

        $out[] = $this->getComponentColumn($openstate,array('id' => $variablename.'selector','height' => '300','visibility' => 'hidden'),array());

        /* error handling */
        if($this->model->getValidationError($variablename)){
            $out[] = $this->getComponentText('',array('style' => 'steps_field_divider_error'));
        } else {
            $out[] = $this->getComponentText('',array('style' => 'steps_field_divider'));
        }


        return $this->getComponentColumn($out);
	}

	private function getDateSelector($var){
        $years = '2017;2017;2018;2018;2019;2019';
        $months = '01;01;02;02;03;03;04;04;05;05;06;06;07;07;08;08;09;09;10;10;11;11;12;12';
        $days = '01;01;02;02;03;03;04;04;05;05;06;06;07;07;08;08;09;09;10;10;11;11;12;12;13;13;14;14;15;15;16;16;17;17;18;18;19;19;20;20;21;21;22;22;23;23;24;24;25;25;26;26;27;27;28;28;29;29;30;30;31;31';

        $yearvalue =  '2017';
        $dayvalue =  date('d');
        $monthvalue = date('m') ;

        $col[] = $this->getComponentFormFieldSelectorList($days,array('value' => $dayvalue,'variable' => $var.'_day','update_on_entry' => 1),array('width' => 50,'margin' => '0 10 0 40'));
        $col[] = $this->getComponentFormFieldSelectorList($months,array('value' => $monthvalue,'variable' => $var.'_month','update_on_entry' => 1),array('width' => 150,'margin' => '0 10 0 0'));
        $col[] = $this->getComponentFormFieldSelectorList($years,array('value' => $yearvalue,'variable' => $var.'_year','update_on_entry' => 1),array('width' => 80,'margin' => '0 40 0 0'));

        return $this->getComponentRow($col);

    }

}