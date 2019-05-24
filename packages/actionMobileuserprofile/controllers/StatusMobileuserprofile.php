<?php

class StatusMobileuserprofile extends MobileuserprofileController
{
    protected $identifier;
    protected $contents;
    protected $title;
    protected $variablePrefix;

    const FIELD_TYPE_CHECK = 'check';

    public function tab1()
    {
        $this->data = new stdClass();

        if (strstr($this->menuid, 'save-status-')) {
            $this->identifier = str_replace('save-status-', '', $this->menuid);
            $this->contents = $this->getStatusData();
            $this->saveStatus();
            $this->no_output = 1;
            return $this->data;
        } else {
            $this->identifier = $this->getIdentifier();
            $this->variablePrefix = $this->getVariablePrefix();
            $this->contents = $this->getStatusData();
        }

        if (!isset($this->contents['fields'])) {
            return $this->data;
        }

        $this->title = $this->getTitle();
        $this->renderHeader();
        $this->renderOptions();
        return $this->data;
    }

    protected function getIdentifier()
    {
        $params = explode('|', $this->menuid);
        return $params[0];
    }

    protected function getVariablePrefix()
    {
        $params = explode('|', $this->menuid);
        return isset($params[1]) ? $params[1] : '';
    }

    /**
     * Save the user status
     */
    public function saveStatus()
    {
        if (count($this->submitvariables) == 1) {
            $status = $this->getSubmittedVariableByName($this->identifier);
            $this->saveVariable($this->identifier, $status);
        } else {
            // Multiple options allowed, save as a json string
            $options = array();
            foreach ($this->submitvariables as $variable) {
                if (empty($variable)) {
                    continue;
                }
                $options[] = $variable;
            }

            $this->saveVariable($this->identifier, json_encode($options));
        }
    }

    public function renderHeader()
    {
        $this->data->header[] = $this->getText($this->title, array(
            'text-align' => 'center',
            'background-color' => '#FF6600',
            'color' => '#FFFFFF',
            'padding' => '20 0 20 0',
            'margin' => '0 0 0 0',
        ));
    }

    /**
     * Render options fields
     */
    public function renderOptions()
    {
        // If images are not prepared up front they will not be rendered
        $this->copyAssetWithoutProcessing('circle_non_bg.png');
        $this->copyAssetWithoutProcessing('circle_selected_bg.png');

        $status = $this->getVariable($this->variablePrefix . $this->identifier);

        // Define the selected state for radio buttons.
        // This will give them another class which will mark them as selected
        $selectedState = array('style' => 'radio_selected_state', 'allow_unselect' => 1, 'animation' => 'fade');
        $options = array();

        foreach ($this->contents['fields'] as $field) {
            $options[] = $this->renderSingleOptionField($field, $status, $selectedState);
            $options[] = $this->getHairline('#f3f3f3');
        }

        $this->data->scroll[] = $this->getColumn($options, array(
            'margin' => '0 0 20 0'
        ));

        $this->data->footer[] = $this->getRow(array(
            $this->renderCancelButton(),
            $this->renderSaveButton()
        ), array(
            'width' => '100%'
        ));
    }

    /**
     * Renders a single option field
     *
     * @param $field
     * @param $status
     * @param $selectedState
     * @return mixed
     */
    protected function renderSingleOptionField($field, $status, $selectedState)
    {
        // When in selected state, the field will have this value
        $selectedState['variable_value'] = $field;
        // Check whether the field should be selected by default
        $selectedState['active'] = $this->getActiveStatus($status, $field);
        // Get the name of the variable that the field should hold
        $variable = $this->getStatusVariable($field);

        if (strstr($variable, 'zodiac_sign' )) {
            $fieldRow[] = $this->getImage('zodiac_' . $field . '.png', array(
                'width' => '40',
                'margin' => '0 10 0 10'
            ));
        }

        $fieldRow[] = $this->getText(ucfirst($field), array(
            'padding' => '10 10 10 20'
        ));

        $fieldRow[] = $this->getRow(array(
            $this->getText('', array(
                'style' => 'radio_default_state',
                'variable' => $this->variablePrefix . $variable,
                'selected_state' => $selectedState,
            ))
        ), array(
            'width' => '40%',
            'floating' => 1,
            'float' => 'right'
        ));

        return $this->getRow(($fieldRow), array(
            'padding' => '5 0 5 0',
            'margin' => '0 0 0 0'
        ));
    }

    /**
     * Render saving button; Will close popup when clicked;
     *
     * @return mixed
     */
    protected function renderSaveButton()
    {
        $onclick = new stdClass();
        $onclick->id = 'save-status-' . $this->variablePrefix . $this->identifier;
        $onclick->action = 'submit-form-content';

        $clicker = array();
        $clicker[] = $onclick;
        $clicker[] = $this->getOnclick('close-popup');

        return $this->getText('Submit', array(
            'onclick' => $clicker,
            'id' => 'id',
            'style' => 'desee_general_button_style_footer_half'
        ));
    }

    protected function renderCancelButton()
    {
        return $this->getText('Cancel', array(
            'onclick' => $this->getOnclick('close-popup'),
            'id' => 'id',
            'style' => 'desee_general_button_style_footer_half_default'
        ));
    }

    /**
     * Get the name of the status variable.
     * If the variable is radio it returns the identifier of the option.
     * If it should use checkboxes return a unique name.
     *
     * @param $field
     * @return string
     */
    protected function getStatusVariable($field)
    {
        if (!empty($this->variablePrefix)) {
            // Prefixed variables use checkboxes, return unique variable name
            return $this->variablePrefix . $this->identifier . '_' . $field;
        }

        if ($this->contents['type'] != self::FIELD_TYPE_CHECK) {
            // It's a radio button
            return $this->identifier;
        }

        return $this->identifier . '_' . $field;
    }

    /**
     * Check if the given field is selected for this status
     *
     * @param $status
     * @param string $field
     * @return string
     */
    protected function getActiveStatus($status, string $field)
    {
        // If status is not set and we're accessing this NOT from the user profile
        if (!empty($this->variablePrefix) && empty($status)) {
            return '1';
        }

        if (!empty($this->variablePrefix) && !empty($status)) {
            $status = json_decode($status);
            return in_array($field, $status) ? '1' : '0';
        }

        if ($this->contents['type'] != self::FIELD_TYPE_CHECK) {
            return $status == $field ? '1' : '0';
        }

        if (is_null($status)) {
            return '0';
        }

        return in_array($field, json_decode($status)) ? '1' : '0';
    }

    /**
     * Get the status popup title.
     *
     * @return array|string
     */
    protected function getTitle()
    {
        $title = explode('_', $this->identifier);
        $title = array_map(function ($item) {
            return ucfirst($item);
        }, $title);
        $title = implode(' ', $title);

        return $title;
    }

    /**
     * Get all fields for each status
     *
     * @return array
     */
    protected function getStatusData()
    {
        // Method to be overwritten in theme to define the status options
    }
}