<?php

namespace Bootstrap\Components\AppzioUiKit\Forms;

use Bootstrap\Components\BootstrapComponent as BootstrapComponent;

trait uiKitFormOnOff
{

    public function uiKitFormOnOff(array $parameters = array())
    {
        /** @var BootstrapComponent $this */

        $title = $this->addParam('title', $parameters, '');
        $value = $this->addParam('value', $parameters, '');
        $variable = $this->addParam('variable', $parameters, '');
        $onclick = $this->addParam('onclick', $parameters, '');
        $divider = $this->addParam('divider', $parameters, false);

        $params = [];

        if ($onclick) {
            $params['onclick'] = $onclick;
        }

        $out[] = $this->getComponentRow(array(
            $this->getComponentText($title, $params, ['margin' => '4 15 4 15', 'width' => '70%', 'font-size' => '14']),
            $this->getComponentFormFieldOnoff(array(
                'type' => 'toggle',
                'variable' => $variable,
                'value' => $value
            ), ['margin' => '4 15 4 15'])
        ), ['width' => '90%']);

        if ($divider) {
            if ($this->model->getValidationError($variable)) {
                $out[] = $this->getComponentText('', [], ['background-color' => '#F12617', 'height' => '1', 'width' => '100%']);
                $out[] = $this->getComponentText($this->model->getValidationError($variable), array('style' => 'uikit_steps_error'));
            } else {
                $out[] = $this->getComponentText('', [], ['background-color' => '#D9DBDA', 'height' => '1', 'width' => '100%', 'margin' => '5 0 5 0']);
            }
        }

        return $this->getComponentColumn($out);


    }

}