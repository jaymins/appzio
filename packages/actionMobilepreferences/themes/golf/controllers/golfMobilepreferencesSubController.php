<?php

Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');

class golfMobilepreferencesSubController extends MobilepreferencesController {


    public $mobileplacesobj;

    public $fontstyle = array(
        'font-style' => 'normal',
        'font-size' => '14'
    );

    public function golf(){
        if(strstr($this->menuid,'clubchoose_')){
            $id = str_replace('clubchoose_','',$this->menuid);
            $this->saveVariable('home_club',$id);
        }

/*        if(!$this->getSavedVariable('real_name') AND $this->getSavedVariable('name')){
            $this->saveVariable('real_name',$this->getSavedVariable('name'));
        }*/

        $data = new StdClass();
        $this->data->scroll[] = $this->getText($this->getSavedVariable('real_name'));

        $this->validateAndSave();

        $titlestyle['width'] = '60%';

        $output[] = $this->addTitle_imate('{#contact#}');

        $output[] = $this->addField_imate('real_name','{#name#}','{#your_real_name#}','name');
        $output[] = $this->addField_imate('phone','{#phone#}','{#your_phone#}','phone');
        $output[] = $this->addField_imate('email','{#email#}','{#your_email#}','email');
        $output[] = $this->getCheckboxImate('notify', '{#notifications#}', false, $this->fontstyle);

        $output[] = $this->addTitle_imate('{#golf_info_preferences#}');

        $output[] = $this->addHcpField();
        $output[] = $this->addClubField();

        $output[] = $this->addTitle_imate('{#search_filtering#}');

        $params['onclick'] = new StdClass();
        $params['onclick']->action = 'open-interstitial';
        $params['id'] = 'interstitial';
        $params['style'] = 'general_button_style_red';

        $columns2[] = $this->getText('{#distance#} ({#km#})',array('width'=>'100','text-align'=>'left')+$this->fontstyle);

        if(!isset($this->varcontent['distance'])){
            AeplayVariable::updateWithName($this->playid, 'distance' ,10000, $this->gid);
        }

        $output[] = $this->addSlider_imate('{#max_distance#}','filter_distance','10000','100','10000','10');
        $output[] = $this->addSlider_imate('{#min_hcp#}','filter_min_hcp','0','-7','54','1');
        $output[] = $this->addSlider_imate('{#max_hcp#}','filter_max_hcp','54','-7','54','1');

        $output[] = $this->getCheckboxImate('filter_by_availability', '{#only_with_matching_availability#}', false, $this->fontstyle);
        $output[] = $this->getCheckboxImate('men', '{#show_men#}', false, $this->fontstyle);
        $output[] = $this->getCheckboxImate('women', '{#show_women#}', false, $this->fontstyle);


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
        $options['onclick'] = $onclick1;

        $output[] = $this->addTitle_imate('{#reset#}');
        $output[] = $this->getText('{#reset_my_matches#}', $options);

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

        $this->data->scroll = $output;

        return true;
    }

    public function addHcpField(){

        $param = $this->getVariable('hcp');

        if($this->getSavedVariable('hcp')){
            $hcp = $this->getSavedVariable('hcp');
        } else {
            $hcp = '{#please_choose#}';
        }

        $onclick3 = new stdClass();
        $onclick3->action = 'open-tab';
        $onclick3->action_config = 3;
        $onclick3->back_button = 1;

        $col[] = $this->getText(strtoupper('{#hcp#} ({#your_handicap#})'),array('style' => 'form-field-titletext'));

        $row[] = $this->getText($hcp,array('variable' => $param,'hint' => '{#choose_your_home_club#}','style' => 'form-field-non-editable-textfield'));
        $row[] = $this->getImage('beak-icon.png',array('height' => '22','margin' => '0 20 0 0','opacity' => '0.6',
            'floating' => '1',
            'float' => 'right'
        ));
        $col[] = $this->getRow($row);
        $col[] = $this->getText('',array('style' => 'form-field-separator'));
        return $this->getColumn($col,array('style' => 'form-field-row','onclick' => $onclick3));

    }


    public function addClubField(){

        $param = $this->getVariable('home_club');
        $content = $this->getSavedVariable('home_club');

        if($this->getSavedVariable('home_club')){
            $clubinfo = @MobileplacesModel::model()->findByPk($this->getSavedVariable('home_club'));
            if(isset($clubinfo->name)){
                $club_name = $clubinfo->name;
                $club_name = strtolower($club_name);
                $club_name = ucwords($club_name);
            }
        }

        if(!isset($club_name)){
            $club_name = '{#please_choose#}';
        }

        $onclick3 = new stdClass();
        $onclick3->action = 'open-tab';
        $onclick3->action_config = 2;
        $onclick3->back_button = 1;

        $col[] = $this->getText(strtoupper('{#home_club#}'),array('style' => 'form-field-titletext'));

        $row[] = $this->getText($club_name,array('variable' => $param,'hint' => '{#choose_your_home_club#}','style' => 'form-field-non-editable-textfield'));
        $row[] = $this->getImage('beak-icon.png',array('height' => '22','margin' => '0 20 0 0','opacity' => '0.6',
            'floating' => '1',
            'float' => 'right'
        ));
        $col[] = $this->getRow($row);
        $col[] = $this->getText('',array('style' => 'form-field-separator'));
        return $this->getColumn($col,array('style' => 'form-field-row','onclick' => $onclick3));

    }


    public function tab2(){

        $output[] = $this->getText(strtoupper('{#search_filtering#}'),array('style' => 'form-field-section-title'));

        $this->data = new stdClass();
        $this->data->scroll[] = $this->getText('{#please_choose_your_home_club#}. {#you_can_use_the_search_above#}', array( 'style' => 'register-text-step-2'));

        $this->mobileplacesobj = new MobileplacesModel();
        $this->mobileplacesobj->playid = $this->playid;
        $this->mobileplacesobj->game_id = $this->gid;

        $this->data->scroll[] = $this->getSpacer(9);
        $this->data->scroll[] = $this->getText('',array('style' => 'form-field-separator'));

        if($this->menuid == 'searchbox' AND isset($this->submitvariables['searchterm']) AND strlen($this->submitvariables['searchterm']) < 3) {
            $this->data->scroll[] = $this->getText('{#write_at_least_three_letters_to_search#}',array(
                'style' => 'register-text-step-2'
            ));

        } elseif($this->menuid == 'searchbox' AND isset($this->submitvariables['searchterm']) AND strlen($this->submitvariables['searchterm']) > 2){
            $searchterm = $this->submitvariables['searchterm'];
            $wordlist = $this->mobileplacesobj->dosearch($searchterm);
            foreach($wordlist as $word){
                $this->setPlaceSimple($word);
            }
        } else {

            $wordlist = $this->mobileplacesobj->dosearch('',8);
            foreach($wordlist as $word){
                $this->setPlaceSimple($word);
            }
        }

        $this->data->footer[] = $this->getTextbutton('{#cancel#}',array('id' => 'cancel','action' => 'open-tab', 'config' => 1));

        $this->searchBox();
        return $this->data;
    }

    public function setPlaceSimple($data){

        $onclick1 = new stdClass();
        $onclick1->action = 'submit-form-content';
        $onclick1->id = 'clubchoose_'.$data['id'];

        $onclick2 = new stdClass();
        $onclick2->action = 'open-tab';
        $onclick2->action_config = 1;

        if(!isset($data['logo']) OR $data['logo'] == 'dummylogo.png' OR $data['logo'] == ''){
            $data['logo'] = 'default-golf-logo.png';
        } else {
            $data['logo'] = basename($data['logo']);
        }

        $col[] = $this->getImage($data['logo'],array('width' => '15%','vertical-align' => 'middle','priority' => 9,'imgheight' => '250'));
        $col[] = $this->getPlaceRowPart($data,'70%');
        $col[] = $this->getImage('round-select-icon.png',array('opacity' => '0.5','margin' => '7 4 0 0','onclick' => array($onclick1,$onclick2)));
        $this->data->scroll[] = $this->getRow($col,array('margin' => '0 15 2 15','padding' => '5 5 5 5','background-color' => '#ffffff',
            'vertical-align' => 'middle'));

        $this->data->scroll[] = $this->getText('',array('style' => 'form-field-separator'));

    }

    public function searchBox(){
        $value = $this->getSubmitVariable('searchterm') ? $this->getSubmitVariable('searchterm') : '';
        $row[] = $this->getImage('search-icon-for-field.png',array('height' => '25'));
        $row[] = $this->getFieldtext($value,array('style' => 'example_searchbox_text',
            'hint' => '{#free_text_search#}','submit_menu_id' => 'searchbox','variable' => 'searchterm',
            //'suggestions' => MobileexampleAccessor::getInitialWordList(10),
            'id' => 'something',
            'suggestions_style_row' => 'example_list_row','suggestions_text_style' => 'example_list_text',
            'submit_on_entry' => '1',
        ));
        $col[] = $this->getRow($row,array('style' => 'example_searchbox'));
        $col[] = $this->getTextbutton('Search',array('style' => 'example_searchbtn','id' => 'dosearch'));
        $this->data->header[] = $this->getRow($col,array('background-color' => $this->color_topbar));
        $this->data->scroll[] = $this->getLoader('Loading',array('color' => '#000000','visibility' => 'onloading'));
    }

    public function getPlaceRowPart($data,$width='55%'){
        $distance = round($data['distance'],0) .'km';
        $id = $data['id'];

        $openinfo = new stdClass();
        $openinfo->action = 'open-action';
        $openinfo->id = $id;
        $openinfo->action_config = $this->getConfigParam('detail_view');
        $openinfo->open_popup = 1;
        $openinfo->sync_open = 1;

        $row[] = $this->getText($data['name'],array('background-color' => '#ffffff','padding' => '3 5 3 5','color' => '#000000','font-size' => '12'));
        $row[] = $this->getText($data['county'],array('background-color' => '#ffffff','padding' => '0 5 3 5','color' => '#000000','font-size' => '11'));
        $row[] = $this->getText($data['city'].', ' .$distance,array('background-color' => '#ffffff','padding' => '0 5 3 5','color' => '#000000','font-size' => '11'));
        return $this->getColumn($row,array('width' => $width,'onclick'=>$openinfo));
    }

    public function tab3(){
        $this->data = new stdClass();

        if($this->menuid == 'savehcp'){
            $this->saveVariables();
        }

        $this->data->scroll[] = $this->getText(strtoupper(strtoupper('{#hcp#} ({#your_handicap#})')),array('style' => 'form-field-section-title'));
        $this->data->scroll[] = $this->getSpacer('100');
        if($this->getSavedVariable('country_selected') == 'Andorra' OR !$this->getSavedVariable('country_selected')){
            $hcp = $this->getSavedVariable('hcp');
        } else {
            $hcp = $this->getSavedVariable('hcp') ? $this->getSavedVariable('hcp') : '54';
        }

        $cols[] = $this->getFieldlist('-7;-7;-6;-6;-5;-5;-4;-4;-3;-3;-2;-2;-1;-1;0;0;1;1;2;2;3;3;4;4;5;5;6;6;7;7;8;8;9;9;10;10;11;11;12;12;13;13;14;14;15;15;16;16;17;17;18;18;19;19;20;20;21;21;22;22;23;23;24;24;25;25;26;26;27;27;28;28;29;29;30;30;31;31;32;32;33;33;34;34;35;35;36;36;37;37;38;38;39;39;40;40;41;41;42;42;43;43;44;44;45;45;46;46;47;47;48;48;49;49;50;50;51;51;52;52;53;53;54;54',
            array('variable' => $this->getVariableId('hcp'),
                'value' => $hcp,'width' => '100%'
            ));
        $this->data->scroll[] = $this->getRow($cols);

        $onclick1 = new StdClass();
        $onclick1->action = 'submit-form-content';
        $onclick1->id = 'savehcp';

        $onclick2 = new StdClass();
        $onclick2->action = 'open-tab';
        $onclick2->action_config = '1';

        $row[] = $this->getTextbutton('{#cancel#}',array('id' => 'cancel','action' => 'open-tab', 'config' => 1,'width' => '49%'));
        $row[] = $this->getVerticalSpacer('2%');
        $row[] = $this->getTextbutton('{#save#}',array('id' => 'savehcp','onclick' => array($onclick1,$onclick2),'width' => '49%'));
        $this->data->footer[] = $this->getRow($row);

        return $this->data;

    }



}