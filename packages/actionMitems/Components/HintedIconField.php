<?php

namespace packages\actionMitems\Components;

use Bootstrap\Components\BootstrapComponent;

trait HintedIconField
{

    public function getHintedIconField($hint = '', $variable, $type = 'text', $params = array())
    {
        /** @var BootstrapComponent $this */

        if (!empty($hint)) {
            $field[] = $this->getComponentText(strtoupper($hint), array(
                'style' => 'steps_hint'
            ));
        }

        $value = isset($params['value']) ?
            $params['value'] : $this->model->getSavedVariable($variable);

        $image = isset($params['icon']) ?
            $params['icon'] : $this->model->getSavedVariable($variable);

        $field[] = $this->getComponentRow(array(
            $this->getComponentFormFieldText($value, array(
                'variable' => $variable,
                'input_type' => $type,
                'style' => 'hinted_field_input'
            )),
            $this->getComponentImage($image, array(
                'style' => 'hinted_field_image'
            ))
        ), array(
            'style' => 'hinted_field_wrapper'
        ));

        return $this->getComponentColumn($field);
    }

}