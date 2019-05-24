<?php

namespace packages\actionMshopping\Components;

use Bootstrap\Components\BootstrapComponent;

trait RadioButtons
{
    public function getRadioButtons($params = array())
    {
        /** @var BootstrapComponent $this */
        $items = $params['items'];
        $variable = $params['variable'];
        $value = $params['value'];

        // Approximate amount of characters that can be fitted on the screen
        $maxCharsPerRow = 45;
        $currentRowChars = 0;

        $maxItemsCount = 2;
        $count = 0;
        $row = array();
        $result = array();

        foreach ($items as $item) {
            $currentRowChars += strlen($item);
            $count++;

            if ($currentRowChars >= $maxCharsPerRow || $count > $maxItemsCount) {
                // Due to weird screen resolutions we can have items only up to certain characters or count
                $result[] = $this->getComponentRow($row);
                $row = array();
                $currentRowChars = strlen($item);
                $count = 1;
            }

            $row[] = $this->getSingleRadioButton($item, $value, $variable);
        }

        // Add remaining items that make less than a full row
        $result[] = $this->getComponentRow($row);

        return $this->getComponentColumn($result, array(), array(
            'margin' => '0 20 0 20'
        ));
    }

    protected function getSingleRadioButton($content, $currentValue, $variable)
    {
        $selectstate = array(
            'style' => 'formkit-radiobutton-selected',
            'variable_value' => $content,
            'allow_unselect' => 1,
            'animation' => 'fade',
            'active' => $currentValue === $content ? '1' : '0'
        );

        return $this->getComponentText($content, array(
            'variable' => $variable,
            'allow_unselect' => 1,
            'style' => 'formkit-radiobutton-unselected',
            'variable_value' => $content,
            'selected_state' => $selectstate,
            'font-size' => '12'
        ));
    }
}