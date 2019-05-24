<?php

namespace packages\actionMvenues\Components;
use Bootstrap\Components\BootstrapComponent;
use packages\actionMvenues\Models\Model;

trait getPlacesField {

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

    public function getPlacesField($field,$title,$icon=false,$params=array()){
        /** @var BootstrapComponent $this */
        /** @var Model $this->model */

        $params_initial['variable'] = $field;
        $params_initial['hint'] = $title;
        $params_initial['style'] = 'mreg_fieldtext';

        if($field == 'email'){
            $params_initial['input_type'] = 'email';
        }elseif($field == 'phone'){
            $params_initial['input_type'] = 'number';
        }

        $params = array_merge($params_initial, $params);

        if($icon){
            $col[] = $this->getComponentImage($icon,array('style' => 'mreg_icon_field'));
        } else {
            $col[] = $this->getComponentText('',array('style' => 'mreg_icon_field'));
        }

        if($this->model->getSubmittedVariableByName($field)){
            $value = $this->model->getSubmittedVariableByName($field);
        } elseif($this->model->getSavedVariable($field)){
            $value = $this->model->getSavedVariable($field);
        } elseif(isset($params['value'])) {
            $value = $params['value'];
        } else {
            $value = '{#click_to_search_for_your_venue#}';
        }

        $id = $this->model->getVariableId($field);

        $onclick = $this->getOnclickGooglePlaces($id,['sync_close' => 1]);

        $col[] = $this->getComponentText($value,$params,['font-size' => 13]);
        
        if(isset($this->model->validation_errors[$field])){
            $row[] = $this->getComponentText($this->model->validation_errors[$field],array('style' => 'mreg_error'));
            $row[] = $this->getComponentRow($col,array(),array('vertical-align' => 'middle'));
            return $this->getComponentColumn($row);
        }

        return $this->getComponentColumn($col,array('onclick'=>$onclick),array(
            'margin' => '20 80 0 80','vertical-align' => 'middle',
            'padding' => '0 0 10 0',
            'text-align' => 'center','border-color' => '#E1E4E3','border-radius' => '10'));
    }

}