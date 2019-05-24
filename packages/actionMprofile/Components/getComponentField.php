<?php

namespace packages\actionMprofile\Components;

use Bootstrap\Components\BootstrapComponent;

trait getComponentField
{
    /**
     * Creates a generic text component with label. Used as
     * default whenever a custom component for a field has
     * not been created
     *
     * @param $field
     * The field name, used also for the variable
     * @param $label
     * The field label, sometimes different from the
     * variable so it is passed separately
     * @param $type
     * The input type - different keyboards are used for
     * different types
     * @return \stdClass
     */
    public function getComponentField($field, $label, $type)
    {
        /** @var BootstrapComponent $this */

        return $this->getComponentColumn(array(
            $this->getComponentText($label),
            $this->getComponentFormFieldText($this->model->getSavedVariable($field), array(
                'variable' => $field,
                'hint' => $label,
                'input_type' => $type,
            ), array(
                'margin' => '0 10 0 10',
                'padding' => '10 10 10 10'
            ))
        ));
    }
}