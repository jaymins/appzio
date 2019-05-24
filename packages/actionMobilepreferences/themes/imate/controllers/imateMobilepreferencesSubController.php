<?php

class imateMobilepreferencesSubController extends MobilepreferencesController {

    public $fontstyle = array(
        'font-style' => 'normal',
        'font-size' => '14'
    );

    public function imate(){

        $data = new StdClass();

        $this->validateAndSave();

        $titlestyle['width'] = '60%';
        $this->data->scroll[] = $this->getText('{#contact#}',array('style' => 'settings_title'));

        $this->data->scroll[] = $this->addField_imate('real_name','{#name#}','{#your_real_name#}','name');
        $this->data->scroll[] = $this->addField_imate('phone','{#phone#}','{#your_phone#}','phone');
        $this->data->scroll[] = $this->addField_imate('email','{#email#}','{#your_email#}','email');
        $this->data->scroll[] = $this->formkitCheckbox('notify', '{#notifications#}');

        $this->data->scroll[] = $this->getText('{#filtering#}',array('style' => 'settings_title'));

        $params['onclick'] = new StdClass();
        $params['onclick']->action = 'open-interstitial';
        $params['id'] = 'interstitial';
        $params['style'] = 'general_button_style_red';

        $this->data->scroll[] = $this->formkitSlider('{#distance#} ({#km#})','distance','10000',50,10000,10);
        // $this->data->scroll[] = $this->getAgeSelector('{#age_limits#}');
        
        $this->data->scroll[] = $this->formkitCheckbox('look_for_men', '{#show_men#}', array(
            'type' => 'toggle',
        ));
        $this->data->scroll[] = $this->formkitCheckbox('look_for_women', '{#show_women#}', array(
            'type' => 'toggle',
        ));

        $this->data->scroll[] = $this->getSpacer(30);

        if(isset($this->menuid) AND $this->menuid == 'reset-matches') {
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

        // if($this->getSavedVariable('purchase_matchswappunlimited') == 1 OR $this->getSavedVariable('purchase_matchswappdisableadvertising') == 1) {
            $this->data->scroll[] = $this->getText('{#reset_my_matches#}', $options);
        // }

        if($this->getSavedVariable('purchase_matchswappunlimited') != 1 AND $this->getSavedVariable('purchase_matchswappdisableadvertising') != 1) {
            $this->data->scroll[] = $this->getTextbutton('{#advertising_for_more#}', $params);
        }

        $params['onclick'] = new StdClass();
        $params['onclick']->action = 'inapp-purchase';
        $params['onclick']->id = 'MatchSwappUnlimited';
        $params['onclick']->product_id_ios = 'MatchSwappUnlimited';
        $params['onclick']->product_id_android = 'matchswappdisableadvertising';
        $params['onclick']->producttype_android = 'inapp';
        $params['onclick']->producttype_ios = 'inapp';

        if($this->getSavedVariable('purchase_matchswappdisableadvertising') != 1 AND $this->getSavedVariable('system_source') == 'client_android'){
            $this->data->scroll[] = $this->getTextbutton('{#remove_all_advertising#}',$params);
        }

        if($this->getSavedVariable('purchase_matchswappunlimited') != 1 AND $this->getSavedVariable('system_source') == 'client_iphone'){
            $this->data->scroll[] = $this->getTextbutton('{#remove_all_advertising#}',$params);
        }

        /* this is just to test android subscriptions */
        $params['onclick'] = new StdClass();
        $params['onclick']->action = 'inapp-purchase';
        $params['onclick']->id = 'MatchSwappUnlimited';
        $params['onclick']->product_id_android = 'matchswappunlimited';
        $params['onclick']->producttype_android = 'subs';

        if($this->getSavedVariable('purchase_matchswappunlimited') != 1 AND $this->getSavedVariable('system_source') == 'client_android'){
            $this->data->scroll[] = $this->getTextbutton('{#buy_unlimited_subscription#}',$params);
        }

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