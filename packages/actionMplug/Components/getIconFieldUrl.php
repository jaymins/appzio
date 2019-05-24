<?php

namespace packages\actionMplug\Components;
use Bootstrap\Components\BootstrapComponent;

trait getIconFieldUrl {

    /**
     * This will automatically set input type for eamil, phone and password accordingly, based on the $field
     *
     * @param $field -- Field name or ID. This is accessible from the model when view is submitted.
     * @param string $title -- Title for the field. Recommended to be provided as a localization string like this: {#name#}
     * @param string $icon -- Icon file name. Icon should be put under images directory.
     * @return \stdClass
     */
    public function getIconFieldUrl($field, string $title, $icon=''){
        /** @var BootstrapComponent $this */

        $params['variable'] = $field;
        $params['hint'] = $title;
        $params['style'] = 'mplug_fieldtext_url';
        $params['input_type'] = 'lowercase';

        if($field == 'email'){
            $params['input_type'] = 'email';
        }elseif($field == 'phone'){
            $params['input_type'] = 'numeric';
        }

        if($icon){
            $col[] = $this->getComponentImage($icon,array('style' => 'mplug_icon_field'));
        } else {
            $col[] = $this->getComponentText('',array('style' => 'mplug_icon_field'));
        }

        $col[] = $this->getComponentText('https://',array('style' => 'mplug_icon_field_url'));

        if(stristr($field, 'password')){
            $col[] = $this->getComponentFormFieldPassword($this->model->getSubmittedVariableByName($field),$params);
        } else {
            $col[] = $this->getComponentFormFieldText($this->model->getSubmittedVariableByName($field),$params);
        }

        if(isset($this->model->validation_errors[$field])){
            $row[] = $this->getComponentText($this->model->validation_errors[$field],array('style' => 'mplug_error'));
            $row[] = $this->getComponentRow($col,array(),array('vertical-align' => 'middle'));
            return $this->getComponentColumn($row);
        }

        return $this->getComponentRow($col,array(),array('vertical-align' => 'middle'));
    }

}