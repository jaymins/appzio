<?php

class deseeStatusMobileuserprofile extends StatusMobileuserprofile
{
    /**
     * Get all fields for each status
     *
     * @return array
     */
    protected function getStatusData()
    {
        switch ($this->identifier) {
            case 'relationship_status':
                return array(
                    'type' => 'radio',
                    'fields' => array(
                        'Divorced',
                        'Married',
                        'Separated',
                        'Single',
                        'Widowed',
                    )
                );
                break;
            case 'seeking':
                return array(
                    'type' => 'check',
                    'fields' => array(
                        'Dating',
                        'Friendship',
                        'Long-Term Relationship',
                        'Marriage'
                    )
                );
                break;
            case 'religion':
                return array(
                    'type' => 'radio',
                    'fields' => array(
                        'Buddhist',
                        'Christian',
                        'Hindu',
                        'Muslim',
                        'Sikh',
                        'Other'
                    )
                );
                break;
            case 'diet':
                return array(
                    'type' => 'radio',
                    'fields' => array(
                        'Non-Veg',
                        'Veg'
                    )
                );
                break;
            case 'tobacco':
                return array(
                    'type' => 'radio',
                    'fields' => array(
                        'Chew',
                        'Smoke',
                        'Vape',
                        'None'
                    )
                );
                break;
            case 'alcohol':
                return array(
                    'type' => 'radio',
                    'fields' => array(
                        'Yes',
                        'No'
                    )
                );
                break;
            case 'zodiac_sign':
                return array(
                    'type' => 'radio',
                    'image' => true,
                    'fields' => array(
                        'aquarius',
                        'aries',
                        'cancer',
                        'capricorn',
                        'gemini',
                        'leo',
                        'libra',
                        'pisces',
                        'sagittarius',
                        'scorpio',
                        'taurus',
                        'virgo'
                    )
                );
                break;
        }
    }
}