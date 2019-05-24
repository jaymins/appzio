<?php

namespace packages\actionMitems\Components;

use Bootstrap\Components\BootstrapComponent;

trait CategoryTagButtons
{
    public function getCategoryTagButtons($params = array())
    {
        /** @var BootstrapComponent $this */
        $items = $params['items'];
        $variable = $params['variable'];
        $values = $params['values'];

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

            $row[] = $this->getSingleCategoryButton($item, $values, $variable);
        }

        // Add remaining items that make less than a full row
        $result[] = $this->getComponentRow($row);

        return $this->getComponentColumn($result, array(), array(
            'margin' => '0 20 0 20'
        ));
    }

    protected function getSingleCategoryButton($content, $currentValues, $variable)
    {
        /** @var BootstrapComponent $this */
        $selectstate = array(
            'style' => 'formkit-radiobutton-selected',
            'variable_value' => $content,
            'allow_unselect' => 1,
            'animation' => 'fade',
            'active' => in_array($content, $currentValues) ? 1 : 0
        );

        return $this->getComponentText($content, array(
            'variable' => $variable . '|' . $content,
            'allow_unselect' => 1,
            'style' => 'formkit-radiobutton-unselected',
            'variable_value' => $content,
            'selected_state' => $selectstate,
            'font-size' => '12'
        ));
    }
}