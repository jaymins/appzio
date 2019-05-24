<?php

class fashionMobilelocationSubController extends MobilelocationController {

    public function tab1(){
        $data = new StdClass();

        $textstyle['font-size'] = '14';
        $textstyle['text-align'] = 'center';
        $textstyle['margin'] = '10 0 10 0';


        $textstyle['font-size'] = '24';
        $textstyle['text-align'] = 'center';
        $textstyle['margin'] = '0 0 3 0';

        $imgstyle['crop'] = 'round';
        $imgstyle['margin'] = '20 90 10 90';
        $imgstyle['text-align'] = 'center';
        $imgstyle['variable'] = $this->getVariableId('profilepic');

        $textstyle['font-size'] = '24';
        $textstyle['text-align'] = 'center';
        $textstyle['margin'] = '0 0 3 0';

        $output[] = $this->getText('{#your_current location#}',array( 'style' => 'location-title-sila'));
        $output[] = $this->getSpacer(20);

        $tr_lang = ( $this->appinfo->name == 'Rantevu' ? 'el' : $this->lang );

        if(isset($this->varcontent['city'])){
            $output[] = $this->getText( ThirdpartyServices::translateString( $this->varcontent['city'], 'en', $tr_lang ),$textstyle );
        } else {
            $output[] = $this->getText('Unable to locate properly :(',$textstyle);
        }

        $textstyle['font-size'] = '14';

        if(isset($this->varcontent['country'])){
            $output[] = $this->getText( ThirdpartyServices::translateString( $this->varcontent['country'], 'en', $tr_lang ), $textstyle );
        }

        $textstyle['margin'] = '0 0 15 0';

        if(isset($this->varcontent['lat']) AND isset($this->varcontent['lon'])){
            $output[] = $this->getText(round($this->varcontent['lat'],2) .', ' .round($this->varcontent['lon'],2),$textstyle);
        }

        $textstyle['color'] = '#42bb43';

        if(isset($this->menuid) AND $this->menuid == 'gps-update') {
            Yii::import('application.modules.aelogic.packages.actionMobilelocation.models.*');
            MobilelocationModel::geoTranslate($this->varcontent,$this->gid,$this->playid);
            $this->loadVariableContent(true);

            $output[] = $this->getText('{#location_updated#}',$textstyle);
            $this->saveVariable('country_selected',$this->getSavedVariable('country'));
            $this->saveVariable('city_selected',$this->getSavedVariable('city'));

            $load = new StdClass();
            $load->action = 'submit-form-content';
            $data->onload[] = $load;
        }

        $onclick1 = new StdClass();
        $onclick1->action = 'ask-location';
        $onclick1->sync = true;

        $onclick2 = new StdClass();
        $onclick2->action = 'submit-form-content';
        $onclick2->id = 'gps-update';

        $buttonparams['style'] = 'general_button_style_red';
        $buttonparams['onclick'] = array($onclick1,$onclick2);

        if(isset($this->varcontent['lat']) AND $this->varcontent['lat']){
            MobilelocationModel::geoTranslate($this->varcontent,$this->gid,$this->playid);
        }

        $this->loadVariableContent(true);
        $this->getText($this->getSavedVariable('city'));

        $output[] = $this->getText('{#update_my_location_using_gps#}',$buttonparams);

        if ( $this->getConfigParam('enable_manual_location') ) {
            $output[] = $this->getSpacer(20);

            if($this->menuid == 'change-country') {
                $output[] = $this->getText('{#please_choose_your_city#}', array( 'style' => 'location-title-sila'));
            } else {
                $output[] = $this->getText('{#change_location_manually#}', array( 'style' => 'location-title-sila'));
            }

            $output[] = $this->getSpacer(20);

            if ($this->menuid == 'change-country') {
                $this->saveVariables();
                $this->loadVariableContent(true);
                //$output[] = $this->countrylist();
                $output[] = $this->citylist();
                $output[] = $this->getSpacer(15);
                $output[] = $this->manualSave('change-city','{#save_city#}');
            } elseif($this->menuid == 'change-city') {
                $output[] = $this->citylist();
                $this->saveVariables();
                $this->saveVariable('city',$this->getSavedVariable('city_selected'));
                $this->saveVariable('country',$this->getSavedVariable('country_selected'));
                Yii::import('application.modules.aelogic.packages.actionMobilelocation.models.*');
                MobilelocationModel::addressTranslation($this->gid,$this->playid,$this->getSavedVariable('country_selected'),$this->getSavedVariable('city_selected'));
                $output[] = $this->getSpacer(15);
                $output[] = $this->manualSave('change-country','{#change_manually#}');
                $load = new StdClass();
                $load->action = 'submit-form-content';
                $data->onload[] = $load;
            } else {
                $output[] = $this->countrylist();
                $output[] = $this->getSpacer(15);
                $output[] = $this->manualSave('change-country','{#change_manually#}');
            }
        }

        $data->scroll = $output;
        $data->footer[] = $this->getBanner($this->getConfigParam('google_adcode_banners'));

        return $data;
    }

    public function countrylist(){
        if($this->menuid == 'change-country' OR $this->menuid == 'change-city'){
            $country = $this->getSavedVariable('country_selected');
        } else {
            $country = $this->getSavedVariable('country');
        }

        $cols[] = $this->getFieldlist('Afghanistan;Afghanistan;Albania;Albania;Algeria;Algeria;American Samoa;American Samoa;Andorra;Andorra;Angola;Angola;Anguilla;Anguilla;Antarctica;Antarctica;Antigua and Barbuda;Antigua and Barbuda;Argentina;Argentina;Armenia;Armenia;Aruba;Aruba;Australia;Australia;Austria;Austria;Azerbaijan;Azerbaijan;Bahamas;Bahamas;Bahrain;Bahrain;Bangladesh;Bangladesh;Barbados;Barbados;Belarus;Belarus;Belgium;Belgium;Belize;Belize;Benin;Benin;Bermuda;Bermuda;Bhutan;Bhutan;Bolivia;Bolivia;Bosnia and Herzegovina;Bosnia and Herzegovina;Botswana;Botswana;Bouvet Island;Bouvet Island;Brazil;Brazil;British Indian Ocean Territory;British Indian Ocean Territory;Brunei Darussalam;Brunei Darussalam;Bulgaria;Bulgaria;Burkina Faso;Burkina Faso;Burundi;Burundi;Cambodia;Cambodia;Cameroon;Cameroon;Canada;Canada;Cape Verde;Cape Verde;Caribbean Netherlands ;Caribbean Netherlands ;Cayman Islands;Cayman Islands;Central African Republic;Central African Republic;Chad;Chad;Chile;Chile;China;China;Christmas Island;Christmas Island;Cocos (Keeling) Islands;Cocos (Keeling) Islands;Colombia;Colombia;Comoros;Comoros;Congo;Congo;Congo, Democratic Republic of;Congo, Democratic Republic of;Cook Islands;Cook Islands;Costa Rica;Costa Rica;Côte d\'Ivoire;Côte d\'Ivoire;Croatia;Croatia;Cuba;Cuba;Curaçao;Curaçao;Cyprus;Cyprus;Czech Republic;Czech Republic;Denmark;Denmark;Djibouti;Djibouti;Dominica;Dominica;Dominican Republic;Dominican Republic;Ecuador;Ecuador;Egypt;Egypt;El Salvador;El Salvador;Equatorial Guinea;Equatorial Guinea;Eritrea;Eritrea;Estonia;Estonia;Ethiopia;Ethiopia;Falkland Islands;Falkland Islands;Faroe Islands;Faroe Islands;Fiji;Fiji;Finland;Finland;France;France;French Guiana;French Guiana;French Polynesia;French Polynesia;French Southern Territories;French Southern Territories;Gabon;Gabon;Gambia;Gambia;Georgia;Georgia;Germany;Germany;Ghana;Ghana;Gibraltar;Gibraltar;Greece;Greece;Greenland;Greenland;Grenada;Grenada;Guadeloupe;Guadeloupe;Guam;Guam;Guatemala;Guatemala;Guernsey;Guernsey;Guinea;Guinea;Guinea-Bissau;Guinea-Bissau;Guyana;Guyana;Haiti;Haiti;Heard and McDonald Islands;Heard and McDonald Islands;Honduras;Honduras;Hong Kong;Hong Kong;Hungary;Hungary;Iceland;Iceland;India;India;Indonesia;Indonesia;Iran;Iran;Iraq;Iraq;Ireland;Ireland;Isle of Man;Isle of Man;Israel;Israel;Italy;Italy;Jamaica;Jamaica;Japan;Japan;Jersey;Jersey;Jordan;Jordan;Kazakhstan;Kazakhstan;Kenya;Kenya;Kiribati;Kiribati;Kuwait;Kuwait;Kyrgyzstan;Kyrgyzstan;Lao People\'s Democratic Republic;Lao People\'s Democratic Republic;Latvia;Latvia;Lebanon;Lebanon;Lesotho;Lesotho;Liberia;Liberia;Libya;Libya;Liechtenstein;Liechtenstein;Lithuania;Lithuania;Luxembourg;Luxembourg;Macau;Macau;Macedonia;Macedonia;Madagascar;Madagascar;Malawi;Malawi;Malaysia;Malaysia;Maldives;Maldives;Mali;Mali;Malta;Malta;Marshall Islands;Marshall Islands;Martinique;Martinique;Mauritania;Mauritania;Mauritius;Mauritius;Mayotte;Mayotte;Mexico;Mexico;Micronesia, Federated States of;Micronesia, Federated States of;Moldova;Moldova;Monaco;Monaco;Mongolia;Mongolia;Montenegro;Montenegro;Montserrat;Montserrat;Morocco;Morocco;Mozambique;Mozambique;Myanmar;Myanmar;Namibia;Namibia;Nauru;Nauru;Nepal;Nepal;New Caledonia;New Caledonia;New Zealand;New Zealand;Nicaragua;Nicaragua;Niger;Niger;Nigeria;Nigeria;Niue;Niue;Norfolk Island;Norfolk Island;North Korea;North Korea;Northern Mariana Islands;Northern Mariana Islands;Norway;Norway;Oman;Oman;Pakistan;Pakistan;Palau;Palau;Palestine, State of;Palestine, State of;Panama;Panama;Papua New Guinea;Papua New Guinea;Paraguay;Paraguay;Peru;Peru;Philippines;Philippines;Pitcairn;Pitcairn;Poland;Poland;Portugal;Portugal;Puerto Rico;Puerto Rico;Qatar;Qatar;Réunion;Réunion;Romania;Romania;Russian Federation;Russian Federation;Rwanda;Rwanda;Saint Barthélemy;Saint Barthélemy;Saint Helena;Saint Helena;Saint Kitts and Nevis;Saint Kitts and Nevis;Saint Lucia;Saint Lucia;Saint Vincent and the Grenadines;Saint Vincent and the Grenadines;Saint-Martin (France);Saint-Martin (France);Samoa;Samoa;San Marino;San Marino;Sao Tome and Principe;Sao Tome and Principe;Saudi Arabia;Saudi Arabia;Senegal;Senegal;Serbia;Serbia;Seychelles;Seychelles;Sierra Leone;Sierra Leone;Singapore;Singapore;Sint Maarten (Dutch part);Sint Maarten (Dutch part);Slovakia;Slovakia;Slovenia;Slovenia;Solomon Islands;Solomon Islands;Somalia;Somalia;South Africa;South Africa;South Georgia and the South Sandwich Islands;South Georgia and the South Sandwich Islands;South Korea;South Korea;South Sudan;South Sudan;Spain;Spain;Sri Lanka;Sri Lanka;St. Pierre and Miquelon;St. Pierre and Miquelon;Sudan;Sudan;Suriname;Suriname;Svalbard and Jan Mayen Islands;Svalbard and Jan Mayen Islands;Swaziland;Swaziland;Sweden;Sweden;Switzerland;Switzerland;Syria;Syria;Taiwan;Taiwan;Tajikistan;Tajikistan;Tanzania;Tanzania;Thailand;Thailand;The Netherlands;The Netherlands;Timor-Leste;Timor-Leste;Togo;Togo;Tokelau;Tokelau;Tonga;Tonga;Trinidad and Tobago;Trinidad and Tobago;Tunisia;Tunisia;Turkey;Turkey;Turkmenistan;Turkmenistan;Turks and Caicos Islands;Turks and Caicos Islands;Tuvalu;Tuvalu;Uganda;Uganda;Ukraine;Ukraine;United Arab Emirates;United Arab Emirates;United Kingdom;United Kingdom;United States;United States;United States Minor Outlying Islands;United States Minor Outlying Islands;Uruguay;Uruguay;Uzbekistan;Uzbekistan;Vanuatu;Vanuatu;Vatican;Vatican;Venezuela;Venezuela;Vietnam;Vietnam;Virgin Islands (British);Virgin Islands (British);Virgin Islands (U.S.);Virgin Islands (U.S.);Wallis and Futuna Islands;Wallis and Futuna Islands;Western Sahara;Western Sahara;Yemen;Yemen;Zambia;Zambia;Zimbabwe;Zimbabwe',
            array('variable' => $this->getVariableId('country_selected'), 'hint' => 'Comment (optional):','style' => 'wide_picker',
                'value' => $country
            ));

        return $this->getRow($cols,array('style' => 'list_container'));
    }

    public function citylist(){
        $mycountry = $this->getSavedVariable('country_selected');
        $mycity = $this->getSavedVariable('city_selected');
        $cities = $this->getListOfCities();
        $this->saveVariables();
        $this->loadVariableContent(true);
        $list = '';

        if(isset($cities[$mycountry])){
            foreach($cities[$mycountry] as $key => $value){
                $list .= $value .';' .$value .';';
            }

            $list2 = substr($list,0,-1);
        } else {
            $list2 = 'Whole country;Whole country';
        }

        $cols2[] = $this->getFieldlist($list2,
            array('variable' => $this->getVariableId('city_selected'), 'style' => 'wide_picker',
                'value' => $mycity
            ));

        return $this->getRow($cols2,array('style' => 'list_container'));
    }

    public function manualSave($id,$txt){
        $onclick3 = new StdClass();
        $onclick3->action = 'submit-form-content';
        $onclick3->id = $id;
        $btn['onclick'] = $onclick3;
        $btn['style'] = 'general_button_style_red';
        return $this->getText($txt, $btn);
    }

}