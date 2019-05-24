<?php

namespace Bootstrap\Components\ClientComponents;

use Bootstrap\Views\BootstrapView;

trait FormFieldList
{

    /**
     * @param $list
     * @param array $parameters selected_state, variable, onclick, style
     * <code>
     * $array = array(
     * 'hint' => 'hint text',
     * 'height' => '40',
     * 'submit_menu_id' => 'someid',
     * 'maxlength', => '80',
     * 'input_type' => 'text',
     * 'activation' => 'initially' //initially or keep-open,
     * 'empty' => '1'       // whether the field should be empty and not use submitted value
     * );
     * </code>
     * @param array $styles -- please see the link for more information about parameters [link] Bootstrap\Components\ComponentStyles
     * @return \stdClass
     */

    public function getComponentFormFieldSelectorList($list, array $parameters = array(), array $styles = array())
    {
        /** @var BootstrapView $this */

        $obj = new \stdClass;
        $obj->type = 'field-list';

        if (empty($list)) {
            return $this->getComponentText('Field definition missing');
        }

        if (is_array($list)) {
            $newlist = '';

            foreach ($list as $key => $value) {
                $newlist = $key . ';' . $value . ';';
            }

            $newlist = substr($newlist, 0, -1);
            $list = $newlist;
        }

        $obj->content = $list;
        $obj = $this->attachStyles($obj, $styles);
        $obj = $this->attachParameters($obj, $parameters);
        $obj = $this->configureDefaults($obj);

        return $obj;
    }
}