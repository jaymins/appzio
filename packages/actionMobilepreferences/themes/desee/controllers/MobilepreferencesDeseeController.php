<?php

class MobilepreferencesDeseeController extends MobilepreferencesController
{
    public function tab1(){

        if ( $this->getConfigParam('article_action_theme') ) {
            $theme = $this->getConfigParam('article_action_theme');

            Yii::import('application.modules.aelogic.packages.actionMobilepreferences.themes.'. $theme .'.controllers.*');

            $method = strtolower($theme);

            $this->data = new StdClass();

            if (strstr($this->menuid, 'save-status')) {
                $fields = $this->getAdditionalInformationFieldNames();
                $field = 'preference_' . str_replace('save-status-', '', $this->menuid);
                $values = array();
                foreach ($this->submitvariables as $key => $value) {
                    if (strstr($key, $field)) {
                        $variable = str_replace($field, '', $value);
                        if (!empty($variable)) {
                            $values[] = $variable;
                        }
                    }
                }

                $this->saveVariable($field, json_encode($values));
            }

            if ( method_exists($this, $method) ) {
                $this->$method();
            }

            return $this->data;
        }

    }

    public function setMetaData()
    {
        $this->rewriteActionField('subject', 'Preferences');
    }

    public function validateAndSave()
    {
        $this->doResetting();

        if ($this->menuid != 'save-data') {
            return false;
        }

        // $this->validateCommonVars();

        if (empty($this->error)) {
            $this->saveVariables();
            $this->loadVariableContent(true);

            // $this->mobilematchingobj->resetMatches(false);
        } else {
            // Display the error messages in the footer section
            $this->displayErrors();
        }

    }

    /**
     * Get additional information field identifiers and names
     *
     * @return array
     */
    protected function getAdditionalInformationFieldNames()
    {
        return array(
            'relationship_status' => 'Status',
            'seeking' => 'They are seeking',
            'religion' => 'Religion',
            'diet' => 'Diet',
            'tobacco' => 'Tobacco',
            'alcohol' => 'Alcohol',
            'zodiac_sign' => 'Zodiac Sign'
        );
    }

}