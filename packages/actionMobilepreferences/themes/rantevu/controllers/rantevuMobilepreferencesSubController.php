<?php

class rantevuMobilepreferencesSubController extends MobilepreferencesController {

    public $fontstyle = array(
        'font-style' => 'normal',
        'font-size' => '14'
    );

    public function rantevu(){

        $data = new StdClass();

        if ( $this->menuid == 'save-data' ) {
            $age_min_var = $this->getVariableId( 'filter_age_start' );
            $age_max_var = $this->getVariableId( 'filter_age_end' );

            $age_min = $this->submitvariables[$age_min_var];
            $age_max = $this->submitvariables[$age_max_var];

            if ( $age_min > $age_max ) {
                $error_text = '{#the_selected_min_age_should_be_less_than_the_selected_max_age#}';
                $this->data->footer[] = $this->getText($error_text, array('style' => 'register-text-step-2'));
                $this->error = true;                
            }

            if ( !$this->error ) {
                $this->saveVariables();
            }
        }

        $titlestyle['width'] = '60%';
        $this->data->scroll[] = $this->getText('{#contact#}',array('style' => 'settings_title'));

        $this->data->scroll[] = $this->addField_imate('real_name','{#name#}','{#your_real_name#}','name');
        $this->data->scroll[] = $this->addField_imate('phone','{#phone#}','{#your_phone#}','phone');
        $this->data->scroll[] = $this->addField_imate('email','{#email#}','{#your_email#}','email');

        $this->data->scroll[] = $this->formkitCheckbox('notify', '{#notifications#}', array(
            // 'type' => 'toggle',
        ));

        $this->data->scroll[] = $this->getText('{#filtering#}',array('style' => 'settings_title'));
        
        $this->data->scroll[] = $this->formkitCheckbox('men', '{#show_men#}', array(
            // 'type' => 'toggle',
        ));
        $this->data->scroll[] = $this->formkitCheckbox('women', '{#show_women#}', array(
            // 'type' => 'toggle',
        ));

        $this->data->scroll[] = $this->formkitSlider('{#distance#} ({#km#})','distance','10000',50,10000,10);

        $this->data->scroll[] = $this->getAgeSelector('{#age_limits#}');

        $this->data->scroll[] = $this->getSpacer(30);

        if ( $this->menuid == 'reset-matches') {
            $font = $this->fontstyle;
            $font['text-align'] = 'center';
            $this->initMobileMatching();
            $this->mobilematchingobj->resetMatches();
        }

        $onclick1['action'] = 'submit-form-content';
        $onclick1['id'] = 'reset-matches';
        $onclick2['action'] = 'go-home';

        $options['style'] = 'general_button_style_red';
        $options['onclick'] = array($onclick1,$onclick2);

        $this->data->scroll[] = $this->getText('{#reset_my_matches#}',$options);

        if ( $this->menuid == 'save-data' ) {

            if ( empty($this->error) ) {
                $label = '{#saved#}';
            } else {
                $label = '{#save#}';
            }
    
            $this->data->footer[] = $this->getTextbutton($label,array('id' => 'save-data', 'style' => 'general_button_style_red'));

        } else {
            $this->data->footer[] = $this->getTextbutton('{#save#}',array('id' => 'save-data', 'style' => 'general_button_style_red'));
        }

        return $this->data;
    }

}