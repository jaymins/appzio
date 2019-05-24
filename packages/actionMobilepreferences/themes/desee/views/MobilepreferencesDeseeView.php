<?php

class MobilepreferencesDeseeView extends MobilepreferencesDeseeController
{
    public $selectedState = array(
        'style' => 'radio_selected_state',
        'variable_value' => '1',
        'allow_unselect' => 1,
        'animation' => 'fade'
    );

    public $fontstyle = array(
        'font-style' => 'normal',
        'font-size' => '14'
    );

    public $metas;

    public function desee()
    {

        $this->setMetaData();
        $this->validateAndSave();
        $this->rewriteActionConfigField('background_color', '#f9fafb');

        $this->metas = new MobilematchingmetaModel();
        $this->metas->current_playid = $this->playid;

        $this->handleExtrasPayments();

        $this->setHeader();
        $this->renderPreferences();

//        $this->renderBirthYearField();

        $this->data->scroll[] = $this->getText('{#filters#}', array('style' => 'profile_field_label'));

        $this->getSnippet('userpreferences', array(
            'variable_prefix' => 'preference_',
            'fields' => array(
                'relationship_status' => '{#status#}',
                'seeking' => '{#they_are_seeking#}',
                'religion' => '{#religino#}',
                'diet' => '{#diet#}',
                'tobacco' => '{#tobacco#}',
                'alcohol' => '{#alcohol#}',
                'zodiac_sign' => '{#zodiac_sign#}',
            ),
        ));

        if ($this->menuid == 'save-data') {

            if (empty($this->error)) {
                $label = '{#saved#}';
            } else {
                $label = '{#save#}';
            }

            $this->data->footer[] = $this->getTextbutton($label, array('id' => 'save-data', 'style' => 'desee_general_button_style_footer'));

        } else {
            $this->data->footer[] = $this->getTextbutton('{#save#}', array('id' => 'save-data', 'style' => 'desee_general_button_style_footer'));
        }

        return $this->data;
    }

    public function renderBirthYearField()
    {
        $this->data->scroll[] = $this->getText('{#birth_year#}', array('style' => 'profile_field_label'));
        $this->data->scroll[] = $this->getHairline('#f3f3f3');

        $year = $this->getVariable('birth_year');
        $minLegalYear = date('Y') - 18;

        $years = '';

        for ($i = $minLegalYear; $i > 1920; $i--) {
            $years .= "$i;$i; ";
        }

        $this->data->scroll[] = $this->getFieldlist($years, array(
            'variable' => 'birth_year',
            'value' => $year,
            'background-color' => '#ffffff',
            'height' => '100',
        ));
        $this->data->scroll[] = $this->getHairline('#f3f3f3');
    }

    public function renderPreferences()
    {
        $preferences = array(
            'gender',
            'order',
            'distance',
            'age'
        );

        foreach ($preferences as $preference) {
            $method = 'render' . ucfirst($preference) . 'Preferences';
            $this->{$method}();
        }
    }

    public function renderGenderPreferences()
    {
        $this->data->scroll[] = $this->getText('{#i_am_searching_for#}', array('style' => 'profile_field_label_top'));

        if ($this->getVariable('men') == 1) {
            $this->selectedState['active'] = 1;
        }

        $this->data->scroll[] = $this->getColumn(array(
            $this->getHairline('#f3f3f3'),
            $this->getRow(array(
                $this->getText('Men', array(
                    'padding' => '5 20 5 20',
                    'font-size' => '15',
                )),
                $fieldRow[] = $this->getRow(array(
                    $this->getText('', array(
                        'style' => 'radio_default_state',
                        'variable' => 'men',
                        'selected_state' => $this->selectedState,
                    ))
                ), array(
                    'style' => 'desee-column-right-aligned',
                ))
            ), array(
                'background-color' => '#ffffff',
                'height' => '45',
            )),
            $this->getHairline('#f3f3f3')
        ));

        $this->selectedState['active'] = 0;

        if ($this->getVariable('women') == 1) {
            $this->selectedState['active'] = 1;
        }

        $this->data->scroll[] = $this->getColumn(array(
            $this->getRow(array(
                $this->getText('Women', array(
                    'padding' => '5 20 5 20',
                    'font-size' => '15',
                )),
                $fieldRow[] = $this->getRow(array(
                    $this->getText('', array(
                        'style' => 'radio_default_state',
                        'variable' => 'women',
                        'selected_state' => $this->selectedState,
                    ))
                ), array(
                    'style' => 'desee-column-right-aligned',
                ))
            ), array(
                'background-color' => '#ffffff',
                'height' => '45',
            )),
            $this->getHairline('#f3f3f3')
        ));

        $this->selectedState['active'] = 0;
    }

    public function renderDistancePreferences()
    {
        $this->data->scroll[] = $this->getText('{#distance#}', array('style' => 'profile_field_label'));

        $distance = $this->getVariable('filter_distance');
        $distanceValue = empty($distance) ? 0 : $distance;

        $this->data->scroll[] = $this->getColumn(array(
            $this->getHairline('#f3f3f3'),
            $this->getRow(array(
                $this->getText('10 km', array(
                    'width' => '33%',
                    'font-size' => '14'
                )),
                $this->getRow(array(
                    $this->getText($distanceValue, array(
                        'variable' => 'filter_distance',
                        'font-size' => '15',
                    )),
                    $this->getText( ' km', array(
                        'font-size' => '15',
                    )),
                ), array(
                    'width' => '33%',
                    'text-align' => 'center',
                )),
                $this->getText('50 km', array(
                    'width' => '33%',
                    'text-align' => 'right',
                    'font-size' => '14'
                ))
            ), array(
                'margin' => '12 20 0 20',
            )),
            $this->getRangeslider('', array_merge(
                array(
                    'min_value' => 0,
                    'max_value' => 50,
                    'step' => 1,
                    'variable' => 'filter_distance',
                    'value' => $distanceValue,
                ),
                $this->getSliderStyles()
            )),
            $this->getHairline('#f3f3f3')
        ), array(
            'background-color' => '#FFFFFF',
        ));
    }

    public function renderAgePreferences()
    {
        $this->data->scroll[] = $this->getText('{#min_age#}', array('style' => 'profile_field_label'));

        $minAgeValue = empty($this->getVariable('filter_age_start')) ? 18 : $this->getVariable('filter_age_start');

        $this->data->scroll[] = $this->getColumn(array(
            $this->getHairline('#f3f3f3'),
            $this->getRow(array(
                $this->getText('18 Yrs.', array(
                    'width' => '33%',
                    'font-size' => '14'
                )),
                $this->getRow(array(
                    $this->getText($minAgeValue, array(
                        'variable' => 'filter_age_start',
                        'font-size' => '15',
                    )),
                    $this->getText(' Yrs.', array(
                        'font-size' => '15',
                    )),
                ), array(
                    'width' => '33%',
                    'text-align' => 'center',
                )),
                $this->getText('90 Yrs.', array(
                    'width' => '33%',
                    'text-align' => 'right',
                    'font-size' => '14'
                ))
            ), array(
                'margin' => '12 20 0 20'
            )),
            $this->getRangeslider('', array_merge(
                array(
                    'min_value' => 18,
                    'max_value' => 90,
                    'step' => 1,
                    'variable' => 'filter_age_start',
                    'value' => $minAgeValue,
                ),
                $this->getSliderStyles(),
                array(
                    'right_track_color' => '#ffc204',
                    'left_track_color' => '#bebebe',
                )
            )),
            $this->getHairline('#f3f3f3')
        ), array(
            'background-color' => '#FFFFFF'
        ));

        $this->data->scroll[] = $this->getText('{#max_age#}', array('style' => 'profile_field_label'));

        $maxAgeValue = empty($this->getVariable('filter_age_end')) ? 30 : $this->getVariable('filter_age_end');

        $this->data->scroll[] = $this->getColumn(array(
            $this->getHairline('#f3f3f3'),
            $this->getRow(array(
                $this->getText('18 Yrs.', array(
                    'width' => '33%',
                    'font-size' => '14'
                )),
                $this->getRow(array(
                    $this->getText($maxAgeValue, array(
                        'variable' => 'filter_age_end',
                        'font-size' => '15'
                    )),
                    $this->getText(' Yrs.', array(
                        'font-size' => '15'
                    ))
                ), array(
                    'width' => '33%',
                    'text-align' => 'center',
                )),
                $this->getText('90 Yrs.', array(
                    'width' => '33%',
                    'text-align' => 'right',
                    'font-size' => '14'
                ))
            ), array(
                'margin' => '12 20 0 20'
            )),
            $this->getRangeslider('', array_merge(
                array(
                    'min_value' => 18,
                    'max_value' => 90,
                    'step' => 1,
                    'variable' => 'filter_age_end',
                    'value' => $maxAgeValue,
                ),
                $this->getSliderStyles()
            )),
            $this->getHairline('#f3f3f3')
        ), array(
            'background-color' => '#FFFFFF'
        ));
    }

    public function renderOrderPreferences()
    {
        $this->data->scroll[] = $this->getSpacer(10);

        $args = array(
            'style' => 'radio_default_state',
        );

        $col_args = array(
            'background-color' => '#ffffff',
            'height' => '45',
        );

        $is_active = $this->metas->checkMeta('active-users-first');

        if ($is_active) {

            if ($this->getVariable('show_active_first')) {
                $this->selectedState['active'] = 1;
            }

            $args['selected_state'] = $this->selectedState;
            $args['variable'] = 'show_active_first';
        } else {
            $this->registerProductDiv( 'buy-active-first', 'm_active_users_first.png', '{#active_stack#}', 'Show most active users on top of your stack for the next 30 days', 'active_stack.01', 'active_stack.001' );
            $col_args['onclick'] = $this->showProductDiv( 'buy-active-first' );
        }

        $this->data->scroll[] = $this->getColumn(array(
            $this->getHairline('#f3f3f3'),
            $this->getRow(array(
                $this->getText('{#show_most_active_profiles_first#}', array(
                    'padding' => '5 20 5 20',
                    'font-size' => '15',
                    'color' => ($is_active ? '#000000' : '#afafae'),
                )),
                $fieldRow[] = $this->getRow(array(
                    $this->getText('', $args)
                ), array(
                    'style' => 'desee-column-right-aligned'
                )),
            ), $col_args),
            $this->getHairline('#f3f3f3')
        ));

        $this->selectedState['active'] = 0;
    }

    public function getSliderStyles() {

        $slider_ball = $this->getImageFileName('slider-ball.png');

        return array(
            'step' => '1',
            'left_track_color' => '#ffc204',
            'right_track_color' => '#bebebe',
            'thumb_color' => '#7ed321',
            'track_height' => '4px',
            'margin' => '12 20 12 20',
            'thumb_image' => $slider_ball,
            'height' => '28',
        );
    }

    public function handleExtrasPayments() {

        if ( !isset($_REQUEST['purchase_product_id']) ) {
            return false;
        }

        $product_id = $_REQUEST['purchase_product_id'];
        $card_config = $this->metas->getCardByProductID( $product_id );

        if ( empty($card_config) ) {
            return false;
        }

        $this->metas->play_id = $this->playid;
        $this->metas->meta_key = $card_config['trigger'];
        $this->metas->meta_value = ( $card_config['measurement'] == 'time' ? time() : $card_config['amount'] );
        $this->metas->meta_limit = $card_config['measurement'];
        $this->metas->saveMeta();

        return true;
    }

    public function setHeader() {
        $toggleSidemenu = new stdClass();
        $toggleSidemenu->action = 'open-sidemenu';

        $this->data->header[] = $this->getRow(array(
            $this->getImage('ic_menu_new.png', array(
                'width' => '20',
                'onclick' => $toggleSidemenu
            )),
            $this->getText('{#preferences#}', array(
                'color' => '#ff6600',
                'width' => '90%',
                'text-align' => 'center',
            ))
        ), array(
            'background-color' => '#FFFFFF',
            'padding' => '10 20 10 20',
            'width' => '100%',
        ));
        $this->data->header[] = $this->getImage('header-shadow.png', array(
            'imgwidth' => '1440',
            'width' => '100%',
        ));

        return true;
    }

}