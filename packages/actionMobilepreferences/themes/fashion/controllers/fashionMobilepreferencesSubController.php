<?php

Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');

class fashionMobilepreferencesSubController extends MobilepreferencesController {


    public $mobileplacesobj;
    public $countries;

    public $fontstyle = array(
        'font-style' => 'normal',
        'font-size' => '14'
    );

    public function fashion(){
        if(strstr($this->menuid,'clubchoose_')){
            $id = str_replace('clubchoose_','',$this->menuid);
            $this->saveVariable('home_club',$id);
        }

        $data = new stdClass();
        $emailerror = false;
        $websiteerror = false;

        if($this->menuid == '5555') {
            if(!$this->validateEmail($this->getSubmittedVariableByName('email'))){
                $emailerror = '{#use_a_correct_email#}';
            }

            if($this->getSavedVariable('role') == 'brand') {
                if (!$this->validateWebsite($this->getSubmittedVariableByName('website'))) {
                    $websiteerror = '{#please_provide_a_valid_website#}';
                }
            }

            if(!$emailerror AND !$websiteerror){
                $this->saveVariables();
            }
        }

        $titlestyle['width'] = '60%';
        $output[] = $this->getText('{#contact#}',array('style' => 'settings_title'));
        $output[] = $this->formkitField('real_name','{#name#}','{#your_real_name#}');
        $output[] = $this->formkitField('phone','{#phone#}','{#your_phone#}','phone');
        $output[] = $this->formkitField('email','{#email#}','{#your_email#}','email',$emailerror);

        if($this->getSavedVariable('role') == 'brand'){
            $output[] = $this->formkitField('company','{#company#}','{#name_of_your_company#}');
            $output[] = $this->formkitField('website','{#website#}','{#your_website#}','website',$websiteerror);
        }
        
        $output[] = $this->formkitCheckbox('notify','{#notifications#}');
        $output[] = $this->getText('{#filtering#}',array('style' => 'settings_title'));

        $params['onclick'] = new StdClass();
        $params['onclick']->action = 'open-interstitial';
        $params['id'] = 'interstitial';
        $params['style'] = 'general_button_style_red';

        $columns2[] = $this->getText('{#distance#} ({#km#})',array('width'=>'100','text-align'=>'left')+$this->fontstyle);

        if(!isset($this->varcontent['distance'])){
            AeplayVariable::updateWithName($this->playid, 'distance' ,10000, $this->gid);
        }

        if($this->getSavedVariable('role') == 'brand'){
            $output[] = $this->formkitSlider('{#minimum_follower_count#}','filter_followers','10000','5000','200000','20000');
        }
        
        $output[] = $this->formkitSlider('{#max_distance#}','filter_distance','10000','100','20000','500');

        $listparams['variable'] = 'filter_countries';
        $listparams['data'] = json_decode($this->getSavedVariable('filter_countries'),true);
        $listparams['title'] = '{#countries#}';
        $output[] = $this->getSelectorListField($listparams);

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

        $output[] = $this->getText('{#reset_my_matches#}', $options);

        if(isset($this->menuid) AND $this->menuid == '5555'){
            $data->footer[] = $this->getTextbutton('{#saved#}',array('id' => 5555));
        } else {
            $data->footer[] = $this->getTextbutton('{#save#}',array('id' => 5555));
        }

        $data->scroll = $output;

        $this->data = $data;
        return true;
    }

    public function tab2(){

        $countries = $this->getCountryCodes();

        /* saving array by using names as keys */
        foreach ($countries as $key=>$value){
            $countrylist[$key] = $key;
        }

        if(isset($countrylist)){
            $listparams['variable'] = 'filter_countries';
            $listparams['data'] = json_decode($this->getSavedVariable('filter_countries'),true);
            $listparams['title'] = '{#countries#}';
            $listparams['list_data'] = $countrylist;
            return $this->getSelectorListing($listparams);
        }

    }





}