<?php

Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');

class golfMobileregister2SubController extends Mobileregister2Controller {


    public $mobileplacesobj;

    /** @var MobileloginModel */
    public $loginmodel;

    /* place selector, sorry for code duplication :( */

    public function tab2(){
        $this->data = new stdClass();
        $this->data->scroll[] = $this->getText('{#please_choose_your_home_club#}. {#you_can_use_the_search_above#}', array( 'style' => 'register-text-step-2'));

        $this->mobileplacesobj = new MobileplacesModel();
        $this->mobileplacesobj->playid = $this->playid;
        $this->mobileplacesobj->game_id = $this->gid;

        if($this->menuid == 'searchbox' AND isset($this->submitvariables['searchterm'])){
            $searchterm = $this->submitvariables['searchterm'];
            $wordlist = $this->mobileplacesobj->dosearch($searchterm);
            foreach($wordlist as $word){
                $this->setPlaceSimple($word);
            }
        } else {
            $this->data->scroll[] = $this->getText('{#places_close_by#}',array(
                'style' => 'register-text-step-2'
            ));

            $wordlist = $this->mobileplacesobj->dosearch('',8);
            foreach($wordlist as $word){
                $this->setPlaceSimple($word);
            }

        }

        if($this->getSavedVariable('home_club')){
            $col[] = $this->getTextbutton('{#cancel#}',array('id' => 'cancel','action' => 'open-tab', 'config' => 1,'width' => '49%'));
            $col[] = $this->getVerticalSpacer('2%');
            $col[] = $this->getTextbutton('{#none#}',array('id' => 'no-club','action' => 'open-tab', 'config' => 1,'width' => '49%'));

            $this->data->footer[] = $this->getRow($col);
        } else {
            $this->data->footer[] = $this->getTextbutton('{#cancel#}',array('id' => 'cancel','action' => 'open-tab', 'config' => 1));
        }


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

        if(!isset($data['logo']) OR $data['logo'] == 'dummylogo.png'){
            $data['logo'] = 'default-golf-logo.png';
        } else {
            $data['logo'] = basename($data['logo']);
        }

        $col[] = $this->getImage($data['logo'],array('width' => '15%','vertical-align' => 'middle','priority' => 9));
        $col[] = $this->getPlaceRowPart($data,'70%');
        $col[] = $this->getImage('round-select-icon.png',array('margin' => '7 4 0 0','opacity' => '0.6','width' => '40'));
        $this->data->scroll[] = $this->getRow($col,array('margin' => '0 15 2 15','padding' => '5 5 5 5','background-color' => '#ffffff',
            'vertical-align' => 'middle','onclick' => array($onclick1,$onclick2)));

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

        $openinfo = new StdClass();
        $openinfo->action = 'open-action';
        $openinfo->id = $id;
        $openinfo->action_config = $this->getConfigParam('detail_view');
        $openinfo->open_popup = 1;
        $openinfo->sync_open = 1;

        $row[] = $this->getText($data['name'],array('background-color' => '#ffffff','padding' => '3 5 3 5','color' => '#000000','font-size' => '12'));
        $row[] = $this->getText($data['county'],array('background-color' => '#ffffff','padding' => '0 5 3 5','color' => '#000000','font-size' => '11'));
        $row[] = $this->getText($data['city'].', ' .$distance,array('background-color' => '#ffffff','padding' => '0 5 3 5','color' => '#000000','font-size' => '11'));
        return $this->getColumn($row,array('width' => $width));
    }



    public function setSex(){

        $this->data->scroll[] = $this->getText('{#what_is_your_sex#}', array( 'style' => 'register-text-step-2'));

        $selectstate_active = array('variable_value' => "man",'active' => 1, 'style' => 'selector_sex_selected','allow_unselect' => 1,'animation' => 'fade');
        $selectstate = array('variable_value' => "man",'style' => 'selector_sex_selected','allow_unselect' => 1,'animation' => 'fade');

        $selectstate_active_w = array('variable_value' => "woman",'active' => 1, 'style' => 'selector_sex_selected','allow_unselect' => 1,'animation' => 'fade');
        $selectstate_w = array('variable_value' => "woman",'style' => 'selector_sex_selected','allow_unselect' => 1,'animation' => 'fade');

        $gender = $this->getSavedVariable('gender');

        if($gender == 'man'){
            $col[] = $this->getText('{#man#}',array('variable' => 'gender', 'selected_state' => $selectstate_active,'style' => 'selector_sex'));
        } else {
            $col[] = $this->getText('{#man#}',array('variable' => 'gender', 'selected_state' => $selectstate,'style' => 'selector_sex'));
        }

        $col[] = $this->getVerticalSpacer('10%');

        if($gender == 'woman') {
            $col[] = $this->getText('{#woman#}',array('variable' => 'gender','selected_state' => $selectstate_active_w,'style' => 'selector_sex'));
        } else {
            $col[] = $this->getText('{#woman#}',array('variable' => 'gender','selected_state' => $selectstate_w,'style' => 'selector_sex'));
        }

        $this->data->scroll[] = $this->getRow($col,array('margin' => '4 40 4 40','text-align' => 'center'));

    }


    /* this is needed for special case where user is trying to register with facebook id which is already a user */
    public function initLoginModel(){
        $this->loginmodel = new MobileloginModel();
        $this->loginmodel->userid = $this->userid;
        $this->loginmodel->playid = $this->playid;
        $this->loginmodel->gid = $this->gid;
        $this->loginmodel->password = $this->getSavedVariable('password');
        $this->loginmodel->fbid = $this->getSavedVariable('fbid');
        $this->loginmodel->fbtoken = $this->getSavedVariable('fb_token');
        $this->loginmodel->password = $this->getSavedVariable('password');
    }



    /* this sets all the variables & resets branches */
    public function finishLogin($skipreg=false,$fblogin=false,$line=false,$tokenlogin=false){
        $this->data->scroll[] = $this->getText('{#logging_in#}',array( 'style' => 'register-text-step-2'));
        $this->addToDebug('Reg line:' .$line);

        AeplayBranch::closeBranch($this->getConfigParam('register_branch'),$this->playid);
        AeplayBranch::closeBranch($this->getConfigParam('login_branch'),$this->playid);
        AeplayBranch::activateBranch($this->getConfigParam('logout_branch'),$this->playid);
        $this->saveVariable('logged_in',1);

        $this->saveVariable('men',$this->getSavedVariable('1'));
        $this->saveVariable('women',$this->getSavedVariable('1'));

        if($fblogin OR $tokenlogin){
            $this->deleteVariable('fb_universal_login');
        }

        $complete = new StdClass();
        $complete->action = 'complete-action';
        $this->data->onload[] = $complete;

        $complete = new StdClass();
        $complete->action = 'list-branches';
        $this->data->onload[] = $complete;

    }


    

    /* standard registration stuff */

	public function golfPhase1(){


        /* handle case where user is already registered (fb connect) */

        if($this->getSavedVariable('fb_universal_login')){

            Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');
            $this->initLoginModel();
            $this->loginmodel->thirdpartyid = $this->getSavedVariable('fb_universal_login');
            $is_user = $this->loginmodel->loginWithThirdParty('facebook');
            if($is_user) {
                $this->playid = $is_user;
                $this->finishLogin(true, true, __LINE__);
                return true;
            }
        }

        if($this->social_authentication){
            $this->golfPhase2();
            return true;
        }

        if ( $this->getConfigParam( 'actionimage1' ) ) {
            $image_file = $this->getConfigParam( 'actionimage1' );
        } elseif ( $this->getImageFileName('reg-logo.png') ) {
            $image_file = 'reg-logo.png';
        }

        if(isset($image_file)){
            $this->data->scroll[] = $this->getImage( $image_file );
        }

        if($this->getSavedVariable('password') AND $this->menuid != 'mobilereg_do_registration'){

            if($this->menuid == 'create-new-user'){
                Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');
                $loginmodel = new MobileloginModel();
                $loginmodel->userid = $this->userid;
                $loginmodel->playid = $this->playid;
                $loginmodel->gid = $this->gid;
                $play = $loginmodel->newPlay();
                $this->playid = $play;

                $this->data->scroll[] = $this->getText('{#creating_new_account#}', array( 'style' => 'register-text-step-2'));

                $complete = new StdClass();
                $complete->action = 'complete-action';
                $this->data->onload[] = $complete;
                return true;
            } else {
                $this->data->scroll[] = $this->getSpacer('15');
                $this->data->scroll[] = $this->getText('{#are_you_sure#}', array( 'style' => 'register-text-step-2'));
                $this->data->scroll[] = $this->getTextbutton('‹ {#back_to_login#}', array(
                    'style' => 'register-text-step-2',
                    'id' => 'back',
                    'action' => 'open-branch',
                    'config' => $this->getConfigParam('login_branch'),
                ));

                $this->data->scroll[] = $this->getSpacer('15');
                $buttonparams2 = new StdClass();
                $buttonparams2->action = 'submit-form-content';
                $buttonparams2->id = 'create-new-user';
                $this->data->footer[] = $this->getText('{#create_a_new_account#}',array('style' => 'general_button_style_footer','onclick' => $buttonparams2));
                return true;
            }
        }

        $this->saveVariable('reg_phase',1);
        $this->data->scroll[] = $this->getSpacer('15');

        if($this->fblogin === false) {
            if ($this->getConfigParam('login_branch')) {
                $this->data->scroll[] = $this->getTextbutton('‹ {#back_to_login#}', array(
                    'style' => 'register-text-step-2',
                    'id' => 'back',
                    'action' => 'open-branch',
                    'config' => $this->getConfigParam('login_branch'),
                ));
            }
        }

        $this->data->scroll[] = $this->getSpacer('10');


        $regfields = $this->setRegFields();

        if($regfields === true){
            $this->saveRegData();
            $this->golfPhase2();
            return true;
        } else {
            $this->data->footer[] = $this->getHairline('#ffffff');
            $this->data->footer[] = $this->getTextbutton('{#register#}',array('style' => 'general_button_style_footer','id' => 'mobilereg_do_registration','submit_menu_id' => 'saver'));
        }

        return true;
    }

/*    public function golfPhasecomplete(){
        $this->finishUp();
        $complete = new StdClass();
        $complete->action = 'complete-action';
        $this->data->scroll[] = $this->getSpacer('100');
        $this->data->scroll[] = $this->getText('{#saving#}',array('style' => 'register-text-step-2'));
        $this->data->onload[] = $complete;
    }*/


    public function setDatePicker(){
        $this->data->scroll[] = $this->getText('{#choose_your_birthdate#}', array( 'style' => 'register-text-step-2'));

        $cols[] = $this->getFieldlist('0;{#choose#};01;{#month_jan#};02;{#month_feb#};03;{#month_mar#};04;{#month_apr#};05;{#month_may#};06;{#month_jun#};07;{#month_jul#};08;{#month_aug#};09;{#month_sep#};10;{#month_oct#};11;{#month_nov#};12;{#month_dec#}',
            array('variable' => $this->getVariableId('birth_month'),
            'value'  => $this->getSavedVariable('birth_month'),'hint' => 'Comment (optional):','style' => 'datepicker'));

        $cols[] = $this->getFieldlist('0;{#choose#};01;1;02;2;03;3;04;4;05;5;06;6;07;7;08;8;09;9;10;10;11;11;12;12;13;13;14;14;15;15;16;16;17;17;18;18;19;19;20;20;21;21;22;22;23;23;24;24;25;25;26;26;27;27;28;28;29;29;30;30;31;31',
            array('variable' => $this->getVariableId('birth_day'), 'hint' => 'Comment (optional):','style' => 'datepicker',
                'value'  => $this->getSavedVariable('birth_day')));

        $cols[] = $this->getFieldlist('0;{#choose#};1910;1910;1911;1911;1912;1912;1913;1913;1914;1914;1915;1915;1916;1916;1917;1917;1918;1918;1919;1919;1920;1920;1921;1921;1922;1922;1923;1923;1924;1924;1925;1925;1926;1926;1927;1927;1928;1928;1929;1929;1930;1930;1931;1931;1932;1932;1933;1933;1934;1934;1935;1935;1936;1936;1937;1937;1938;1938;1939;1939;1940;1940;1941;1941;1942;1942;1943;1943;1944;1944;1945;1945;1946;1946;1947;1947;1948;1948;1949;1949;1950;1950;1951;1951;1952;1952;1953;1953;1954;1954;1955;1955;1956;1956;1957;1957;1958;1958;1959;1959;1960;1960;1961;1961;1962;1962;1963;1963;1964;1964;1965;1965;1966;1966;1967;1967;1968;1968;1969;1969;1970;1970;1971;1971;1972;1972;1973;1973;1974;1974;1975;1975;1976;1976;1977;1977;1978;1978;1979;1979;1980;1980;1981;1981;1982;1982;1983;1983;1984;1984;1985;1985;1986;1986;1987;1987;1988;1988;1989;1989;1990;1990;1991;1991;1992;1992;1993;1993;1994;1994;1995;1995;1996;1996;1997;1997;1998;1998;1999;1999;2000;2000',
            array('variable' => $this->getVariableId('birth_year'), 'value' => $this->getSavedVariable('year'),'hint' => 'Comment (optional):','style' => 'datepicker',
            ));

        $this->data->scroll[] = $this->getRow($cols,array('style' => 'list_container'));

    }


    public function setClub(){

        if ( !$this->getConfigParam('place_popup') ) {
            return false;
        }

        if(strstr($this->menuid,'clubchoose_')){
            $id = str_replace('clubchoose_','',$this->menuid);
            $this->saveVariable('home_club',$id);
        }

        if(strstr($this->menuid,'no-club')){
            $this->deleteVariable('home_club');
        }

        $onclick1 = new stdClass();
        $onclick1->action = 'submit-form-content';
        $onclick1->id = 'datasave';

        $onclick2 = new stdClass();
        $onclick2->action = 'open-tab';
        $onclick2->action_config = 2;

        $terms_label = '{#choose_your_home_club#}';

        if($this->getSavedVariable('home_club')){
            $clubinfo = @MobileplacesModel::model()->findByPk($this->getSavedVariable('home_club'));
            if(isset($clubinfo->name)){
                $terms_label = $clubinfo->name;
            }
        }

        $this->data->scroll[] = $this->getText($terms_label, array('style' => 'general_button_style_black','onclick' => array($onclick1,$onclick2)));
    }



    public function golfPhase2()
    {
        if (isset($this->menuid) AND $this->menuid == 'continue_to_3') {
            $this->golfPhase3();
            return true;
        }

        $cache = Appcaching::getGlobalCache('location-asked' . $this->playid);

        if (!$cache) {
            $menu2 = new StdClass();
            $menu2->action = 'ask-location';
            $this->data->onload[] = $menu2;
            Appcaching::setGlobalCache('location-asked' . $this->playid, true);
        }

        //$this->data->footer[] = $this->getText($this->getSavedVariable('lat'));
        //$this->data->scroll[] = $this->getText($this->menuid);

        if(!$this->social_authentication OR $this->menuid == 'continue_to_3' OR $this->menuid == 'ask-location' OR $this->menuid == 'save-variables'
        OR $this->menuid == 'saver-2'){
            $this->saveVariables();
            $this->loadVariableContent(true);
        }

        $this->setProfilePic();

        if (!$this->getSavedVariable('gender')) {
            $this->setSex();
        }

        $this->setPreferences();
        $this->data->scroll[] = $this->getSpacer('8');
        $this->setProfileComment();
        //$this->data->scroll[] = $this->getFieldWithIcon('hcp-icon3.png',$this->vars['hcp'],'{#HCP#} ({#golf_handicap#})',false,'text','saver-2','number');

        $this->setHcp();
        $this->data->scroll[] = $this->getSpacer('10');

        if (!$this->getSavedVariable('birth_year') OR !$this->getSavedVariable('birth_month') OR !$this->getSavedVariable('birth_day')) {
            $this->setDatePicker();
            $this->data->scroll[] = $this->getSpacer('10');
        }

        $this->setClub();
        $this->data->scroll[] = $this->getSpacer('3');
        $this->setTerms();

        $location = $this->getConfigParam('ask_for_location');
        $lat = $this->getSavedVariable('lat');
        $terms = $this->getSavedVariable('terms_accepted');
        $profilepic = $this->getSavedVariable('profilepic');
        $profilecomment = $this->getSavedVariable('profile_comment');
        $hcp = $this->getSavedVariable('hcp');
        $gender = $this->getSavedVariable('gender');

        $this->data->footer[] = $this->getHairline('#ffffff');

        if(!$lat AND $cache){
            $this->data->footer[] = $this->getText('There was an error locating you. If you disabled the location permission, go to settings and enable location for this app. ',array('style' => 'register-text-step-2'));
            $btnaction1 = new StdClass();
            $btnaction1->action = 'submit-form-content';
            $btnaction1->id = 'save-variables';

            $btnaction2 = new StdClass();
            $btnaction2->action = 'ask-location';
            $btnaction2->sync_upload = false;

            $buttonparams['onclick'] = array($btnaction1,$btnaction2);
            $buttonparams['style'] = 'general_button_style_footer';
            $buttonparams['id'] = 'dolocate';
            $this->data->footer[] = $this->getText('Locate me',$buttonparams);
            $error = true;
            $locationerror = true;
        }

        if($this->menuid == 'saver-2'){
            $error = false;

            if(!$profilecomment){
                $this->data->footer[] = $this->getText('{#please_add_a_profile_comment#}',array('style' => 'golf-error'));
                $error = true;
            }

            if(!$gender){
                $this->data->footer[] = $this->getText('{#please_choose_your_gender#}',array('style' => 'golf-error'));
                $error = true;
            }

            if (!$this->getSavedVariable('birth_year') OR !$this->getSavedVariable('birth_month') OR !$this->getSavedVariable('birth_day')) {
                $this->data->footer[] = $this->getText('{#please_choose_your_birthdate#}',array('style' => 'golf-error'));
                $error = true;
            }

            if(!$profilepic){
                $this->data->footer[] = $this->getText('{#please_add_a_profile_pic#}',array('style' => 'golf-error'));
                $error = true;
            }

            if(!$hcp){
                $this->data->footer[] = $this->getText('{#please_input_your_hcp#}',array('style' => 'golf-error'));
                $error = true;
            }

            if($hcp > 54){
                $this->data->footer[] = $this->getText('{#hcp_should_be_54_or_less#}',array('style' => 'golf-error'));
                $error = true;
            }

            if(!$terms){
                $this->data->footer[] = $this->getText('{#please_accept_the_terms#}',array('style' => 'golf-error'));
                $error = true;
            }

            if($error == false){
                $this->data->scroll = array();
                $this->data->scroll[] = $this->getSpacer('100');
                $this->data->scroll[] = $this->getText('{#saving#}...',array('style' => 'register-text-step-2'));
                $this->data->footer = array();
                $this->closeLogin();
                $this->finishUp();
                $complete = new StdClass();
                $complete->action = 'complete-action';
                $this->data->onload[] = $complete;
                return true;
            }
        }

        /* if there is location error, we will provide a locate button instead of this */
        if(!isset($locationerror)){
            $this->data->footer[] = $this->getTextbutton('{#continue#}',array('style' => 'general_button_style_footer','id' => 'saver-2'));
        }

        $this->data->scroll[] = $this->getSpacer(40);
        Appcaching::setGlobalCache( $this->playid .'-' .'registration',true);
        return true;
    }

    public function setHcp(){
            $this->data->scroll[] = $this->getText('{#choose_your_handicap#} (HCP)', array( 'style' => 'register-text-step-2'));


            $cols[] = $this->getFieldlist('-7;-7;-6;-6;-5;-5;-4;-4;-3;-3;-2;-2;-1;-1;0;0;1;1;2;2;3;3;4;4;5;5;6;6;7;7;8;8;9;9;10;10;11;11;12;12;13;13;14;14;15;15;16;16;17;17;18;18;19;19;20;20;21;21;22;22;23;23;24;24;25;25;26;26;27;27;28;28;29;29;30;30;31;31;32;32;33;33;34;34;35;35;36;36;37;37;38;38;39;39;40;40;41;41;42;42;43;43;44;44;45;45;46;46;47;47;48;48;49;49;50;50;51;51;52;52;53;53;54;54',
            array('variable' => $this->getVariableId('hcp'),
                'style' => 'wide_picker', 'value' => $this->getSavedVariable('hcp',33)
            ));

            $this->data->scroll[] = $this->getRow($cols,array('style' => 'list_container'));


    }

    public function setTerms(){

        if ( !$this->getConfigParam('require_terms') ) {
            return false;
        }

        $onclick = new StdClass();
        $onclick->action = 'open-action';
        $onclick->id = $this->getConfigParam('terms_popup');
        $onclick->config = $this->getConfigParam('terms_popup');
        $onclick->action_config = $this->getConfigParam('terms_popup');
        $onclick->open_popup = '1';

        $terms_label = '{#review_terms_and_conditions#}';

        $columns[] = $this->getFieldonoff($this->getSavedVariable('terms_accepted'), array('margin' => '0 9 0 0', 'variable' => $this->getVariableId('terms_accepted')));
        $columns[] = $this->getText('{#i_approve_terms#}', array('style' => 'terms-hint'));
        $this->data->scroll[] = $this->getRow($columns, array('style' => 'radio_button_container'));
        $this->data->scroll[] = $this->getText($terms_label, array('style' => 'general_button_style_black','onclick' => $onclick));

    }


    public function finishUp(){

        $this->beforeFinishRegistration();

        if ( !$this->getConfigParam('require_match_entry') ) {
            return false;
        }

        if(!$this->getSavedVariable('gender')){
            $this->saveVariable('gender','man');
        }

        if(!$this->getSavedVariable('real_name') AND $this->getSavedVariable('name')){
            $this->saveVariable('real_name',$this->getSavedVariable('name'));
        }
        
        $this->initMobileMatching();
        $this->mobilematchingobj->turnUserToItem(false,__FILE__);

        Yii::import('application.modules.aelogic.packages.actionMobilelocation.models.*');
        MobilelocationModel::geoTranslate($this->varcontent,$this->gid,$this->playid);

    }


    public function golfPhase3(){

        $this->saveVariables();

        $terms = $this->getSavedVariable('approve_terms');
        $lat = $this->getSavedVariable('lat');

        $this->saveVariable('reg_phase',3);
        $lon = $this->getSavedVariable('lon');

        $btnaction1 = new StdClass();
        $btnaction1->action = 'submit-form-content';
        $btnaction1->id = 'save-variables';

        $btnaction2 = new StdClass();
        $btnaction2->action = 'ask-location';
        $btnaction2->sync_upload = false;

        $buttonparams['onclick'] = array($btnaction1,$btnaction2);
        $buttonparams['style'] = 'general_button_style_footer';
        $buttonparams['id'] = 'dolocate';
        $this->data->scroll[] = $this->getSpacer(200);

        if(!$lat){
            if(isset($this->menuid) AND $this->menuid == 'dolocate'){
                $this->data->scroll[] = $this->getText('Something went wrong with the location.', array( 'style' => 'register-text-step-2'));
            } else {
                $this->data->scroll[] = $this->getText('Registration requires your location information. Otherwise the app won\'t work.', array( 'style' => 'register-text-step-2'));
            }
            $this->data->footer[] = $this->getHairline('#ffffff');
            $this->data->footer[] = $this->getText('Locate me now to continue', $buttonparams);
        } else {
            /* we do this only if the matching action is present within the app */

            $this->data->scroll[] = $this->getText('How wonderful, all required information is now set up. Please be respectful.', array( 'style' => 'register-text-step-2'));
            $this->generalComplete();
        }
    }


    public function setRegFields(){
        $error = false;
        $error2 = false;
        $error3 = false;
        $error4 = false;
        $error5 = false;
        $error6 = false;
        $twitter = false;


        if($this->getSavedVariable('twitter_token') AND $this->getSavedVariable('twitter_token_secret')){
            $twitter = true;
            $this->data->scroll[] = $this->getText("{#you_are_connected_with_twitter#}",array('style' => 'register-text-step-2'));
            $error = $this->checkForError('real_name','{#please_input_first_and_last_name#}');
            $this->data->scroll[] = $this->getFieldWithIcon('login-persona-icon.png',$this->vars['real_name'],'{#name#}',$error);
        } elseif ($this->fblogin === false AND $this->getConfigParam('facebook_enabled')) {
            $this->data->scroll[] = $this->getFacebookSignInButton('fb-login');

            if ($this->getConfigParam('collect_name',1)) {
                $realname = $this->getVariable('real_name');
                $name = $this->getVariable('name');

                if ($realname AND !$name) {
                    $this->saveVariable('name',$realname);
                }

                $error = $this->checkForError('real_name','{#please_input_first_and_last_name#}');
                $this->data->scroll[] = $this->getFieldWithIcon('login-persona-icon.png',$this->vars['real_name'],'{#name#}',$error);
            }
        } elseif ($this->getConfigParam('facebook_enabled')) {
            $this->data->scroll[] = $this->getText("{#you_are_connected_with_facebook#}",array('style' => 'register-text-step-2'));
        } else {
            $this->collectName();
        }

        // $this->data->scroll[] = $this->getFieldWithIcon('icon-surname.png',$this->vars['surname'],'Surname',false,'text');

        if(!$this->social_authentication OR !$this->getConfigParam('simple_social')) {
            if ($this->getConfigParam('show_email', 1)) {
                $error3 = $this->checkForError('email', '{#input_valid_email#}', '{#email_already_exists#}');
                $this->data->scroll[] = $this->getFieldWithIcon('icon_email.png', $this->getVariableId('email'), '{#email#}', $error3, 'text', false, 'email');
            }
        }

        if ($this->getConfigParam('collect_phone')) {
            $error4 = $this->checkForError('phone','{#please_input_a_valid_phone#}');
            $this->data->scroll[] = $this->getFieldWithIcon('phone-icon-register.png',$this->vars['phone'],'{#phone#} ({#with_country_code#}',$error4);
        }

        if ( $this->getConfigParam('collect_address') ) {
            $this->data->scroll[] = $this->getFieldWithIcon('icon-address.png',$this->vars['address'],'{#address#}',false,'text');
        }

        if(!$this->social_authentication OR !$this->getConfigParam('simple_social')) {
            if ($this->getConfigParam('collect_password') AND $twitter == false) {
                $error5 = $this->checkForError('password_validity', '{#at_least#} 4 {#characters#}');
                $error6 = $this->checkForError('password_match', '{#passwords_do_not_match#}');
                $this->data->scroll[] = $this->getFieldWithIcon('icon_pw.png', $this->fields['password1'], '{#password#}', $error5, 'password');
                $this->data->scroll[] = $this->getFieldWithIcon('icon_pw.png', $this->fields['password2'], '{#password_again#}', $error6, 'password', 'mobilereg_do_registration');
            }
        }

        $this->data->scroll[] = $this->getSpacer('5');

        if (!$error AND !$error2 AND !$error3 AND !$error4 AND !$error5 AND !$error6 AND $this->menuid == 'mobilereg_do_registration') {
            $this->saveVariable('reg_phase',2);
            unset($this->data->scroll);
            $this->data->scroll = array();
            // Need to investigate why ..
            unset($this->data->footer);
            return true;
        }

        return false;
    }

    public function setProfilePic() {
        if ( !$this->getConfigParam('require_photo') ) {
            return false;
        }

        if(isset($this->varcontent['fb_image']) AND $this->varcontent['fb_image']){
            $pic = $this->varcontent['fb_image'];
            $txt = '{#change_photo#}';
        } elseif(isset($this->varcontent['profilepic']) AND $this->varcontent['profilepic']) {
            $pic = $this->varcontent['profilepic'];
            $txt = '{#change_photo#}';
        } else {
            $pic = 'small-filmstrip.png';
            $txt = '{#add_a_photo#}';
        }

        //$this->data->scroll[] = $this->getImage($pic,array('variable' => $this->getVariableId('profilepic'),'imgwidth' => '600','imgheight' => '600','imgcrop'=>'yes'));

        $width = $this->screen_width/3;
        $this->data->scroll[] = $this->getImage($pic,array('variable' => $this->getVariableId('profilepic'),
            'imgwidth' => '600','imgheight' => '600','crop'=>'round','height' => $width, 'width' => $width,'priority' => 9,
            'margin' => "30 $width 0 $width"
        ));

        if($pic == 'small-filmstrip.png' AND isset($this->menuid) AND ($this->menuid == 5555 OR $this->menuid == 55556)){
            $this->data->scroll[] = $this->getText('{#uploading#} ...', array( 'style' => 'uploading_text'));
        }

        $this->data->scroll[] = $this->getText('{#add_a_profile_pic#}', array( 'style' => 'register-text-step-2'));

        $this->data->scroll[] = $this->getTextbutton($txt, array(
                'variable' => $this->getVariableId( 'profilepic' ),
                'action' => 'upload-image',
                'sync_upload'=>true,
                'max_dimensions' => '600',
                'style' => 'general_button_style_black' ,
                'id' => $this->getVariableId( 'profilepic' )
            )
        );
    }
    
}