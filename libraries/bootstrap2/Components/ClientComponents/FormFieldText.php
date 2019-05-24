<?php

namespace Bootstrap\Components\ClientComponents;
use Bootstrap\Views\BootstrapView;

/**
 * Trait FormFieldText
 * This trait provides the basic text input component.
 *
 * @package Bootstrap\Components\Elements
 */
trait FormFieldText {

    /**
     * @param $field_content string, no support for line feeds
     * @param array $parameters
     * <code>
     * $array = array(
     *      'selected_state',            // name of style class to use when field is in focus
     *      'variable',                  // attached variable, used for submit & when setting values
     *      'onclick',                   // array or single object of onclicks
     *      'style'                      // style class name
     *      'hint' => 'hint text',
     *      'height' => '40',
     *      'submit_menu_id' => 'someid', // shows submit button on the keyboad
     *      'maxlength', => '80',        // maximum number of characters
     *      'input_type' => 'text',      // can be also number, email, phone etc. -- password uses different field
     *      'activation' => 'initially'  // initially or keep-open,
     *      'empty' => '1'               // whether the field should be empty and not use submitted value
     * );
     * </code>
     * @param array $styles -- please see the link for more information about parameters [link] Bootstrap\Components\ComponentStyles
     * @return \stdClass
     */

    public function getComponentFormFieldText(string $field_content = '',array $parameters=array(),array $styles=array()){
        /** @var BootstrapView $this */

        $obj = new \stdClass;
        $obj->type = 'field-text';

        if($this->model){
            if(empty($field_content) AND isset($parameters['variable']) AND !isset($parameters['empty']) AND !isset($parameters['value'])){
                if($this->model->getSubmittedVariableByName($parameters['variable'])){
                    $field_content = $this->model->getSubmittedVariableByName($parameters['variable']);
                } elseif($this->model->getSavedVariable($parameters['variable'])){
                    $field_content = $this->model->getSavedVariable($parameters['variable']);
                }
            }
        }


        $obj->content = $field_content;

        $obj = $this->attachStyles($obj,$styles);
        $obj = $this->attachParameters($obj,$parameters);
        $obj = $this->configureDefaults($obj);

        if(isset($parameters['uppercase']) AND isset($parameters['hint'])){
            if($this->model){
                $content = $this->model->localize($parameters['hint']);
            } else {
                $content = $parameters['hint'];
            }
            $obj->hint = strtoupper($content);
        }

        return $obj;
    }
}