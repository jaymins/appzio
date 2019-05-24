<?php

namespace Bootstrap\Components\AppzioUiKit\Forms;

use Bootstrap\Components\BootstrapComponent as BootstrapComponent;

trait uiKitGeneralField
{

    public function uiKitGeneralField($field, $title,$icon = false,$params = array(),$column_styles=array())
    {
        /** @var BootstrapComponent $this */

        $params_initial['variable'] = $field;
        $params_initial['hint'] = $title;
        $params_initial['style'] = 'uikit-general-field-text';

        if ($field == 'email') {
            $params_initial['input_type'] = 'email';
        } elseif ($field == 'phone') {
            $params_initial['input_type'] = 'number';
        }

        $params = array_merge($params_initial, $params);

        if ($icon) {
            $col[] = $this->getComponentImage($icon, array('style' => 'uikit-general-field-icon'));
        } else {
            $col[] = $this->getComponentText('', array('style' => 'uikit-general-field-icon'));
        }

        if ($this->model->getSubmittedVariableByName($field)) {
            $value = $this->model->getSubmittedVariableByName($field);
        } elseif ($this->model->getSavedVariable($field)) {
            $value = $this->model->getSavedVariable($field);
        } else {
            $value = '';
        }

        if (stristr($field, 'password')) {
            $col[] = $this->getComponentFormFieldPassword($this->model->getSubmittedVariableByName($field), $params);
        } else {
            $col[] = $this->getComponentFormFieldText($value, $params);
        }

        if (isset($this->model->validation_errors[$field])) {
            $error = $this->model->validation_errors[$field];
        } elseif(isset($params['error']) AND $params['error']){
            $error = $params['error'];
        } else {
            $error = false;
        }


        if (isset($params['divider']) AND $params['divider']) {

            if($error){
                $output[] = $this->getComponentRow($col, array(), array('vertical-align' => 'middle'));
                $output[] = $this->uiKitDividerError();
                $output[] = $this->uiKitFormErrorText($error);
                return $this->getComponentColumn($output);

            } else {
                $output[] = $this->getComponentRow($col, array(), array('vertical-align' => 'middle'));
                $output[] = $this->uiKitDivider();
                return $this->getComponentColumn($output);
            }

        } elseif($error) {
            $row[] = $this->getComponentRow($col, array(), array('vertical-align' => 'middle'));
            $row[] = $this->getComponentText($error, array('style' => 'uikit-general-field-error'));
            return $this->getComponentColumn($row);
        }

        return $this->getComponentRow($col, array(), array_merge(['vertical-align' => 'middle'],$column_styles));
    }

}