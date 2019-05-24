<?php

Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');

class matchswappMobileuserprofileSubController extends MobileuserprofileController {

    public $units;

    public function showProfileView(){

        $this->rewriteActionConfigField( 'background_color', '#f6f6f6' );

        $use_miles = $this->getConfigParam( 'use_miles' );
        $this->units = ( $use_miles ? 'miles' : 'km' );

        /* determine who's profile should we show */
        $this->initMobileMatching();
        $this->mobilematchingobj->initMatching($this->profileid);
        $this->doReporting();

        $vars = AeplayVariable::getArrayOfPlayvariables($this->profileid);

        $this->data->scroll[] = $this->getImageScroll($vars);

        $tr_lang = ( $this->appinfo->name == 'Rantevu' ? 'el' : $this->lang );

        $first_name = $this->getFirstName($vars);
        $city = isset($vars['city']) ? ThirdpartyServices::translateString( $vars['city'], 'en', $tr_lang ) : '{#hidden_city#}';
        $country = isset($vars['country']) ? ThirdpartyServices::translateString( $vars['country'], 'en', $tr_lang ) : '{#hidden_country#}';

        if(isset($this->varcontent['lat']) AND $this->varcontent['lat']
        AND isset($vars['lat']) AND isset($vars['lon'])
        ){
            $distance = Helper::getDistance( $this->varcontent['lat'], $this->varcontent['lon'], $vars['lat'], $vars['lon'], 'K' );
            $distance = round($distance, 1);
        } else {
            $distance = 'n/a';
        }

        // Rewrite the subject
        $this->rewriteActionField('subject', $first_name);

        $texparam['font-size'] = 14;

        $city = isset($vars['city']) ? $vars['city'] .', ' : '';
        $age = isset($vars['age']) ? ', ' . $vars['age'] : '';

        $piccount = $this->getPicCount($vars);

        $toolbar[] = $this->getColumn(array(
            $this->getRow(array(
                $this->getText($first_name, array( 'font-size' => 14, 'font-style' => 'bold' )),
                $this->getText($age, array( 'font-size' => 14 ))
            ), array( 'width' => '100%' )),
            $this->getText($city . $distance . ' ' . $this->units, array( 'width' => '100%', 'font-size' => 14 )),
        ), array(
            'width' => '70%',
            'vertical-align' => 'middle',
        ));

        $toolbar[] = $this->getColumn(array(
            $this->getRow(array(
                // $this->getImage('toolbar-friends.png', array('width' => '20', 'margin' => '0 5 0 5')),
                // $this->getText($this->mobilematchingobj->getNumberOfMatches($this->profileid), $texparam),
                $this->getImage('toolbar-photos.png', array('width' => '20', 'margin' => '0 5 0 5')),
                $this->getText($piccount, $texparam),
            ), array(
                'text-align' => 'right',
            )),
        ), array(
            'width' => '30%',
            'text-align' => 'right',
        ));

        $this->data->scroll[] = $this->getColumn(array(
            $this->getRow($toolbar, array(
                'margin' => '0 10 0 10',
                'height' => '45',
                'vertical-align' => 'middle',
            )),
        ), array(
            'background-color' => '#ffffff',
            // 'shadow-radius' => '10',
            // 'shadow-offset' => '10 0 10 0',
        ));

        $this->data->scroll[] = $this->getImage('stripe.png', array('width' => '100%', 'margin' => '0 0 10 0'));

        if ( isset($vars['profile_comment']) AND !empty($vars['profile_comment']) ) {
            $this->data->scroll[] = $this->getText('{#about#} ' . ucfirst($first_name), array( 'style' => 'matchswapp-profile-heading' ));
            $this->data->scroll[] = $this->getText($vars['profile_comment'], array( 'style' => 'matchswapp-profile-description' ));
        }

        $this->data->scroll[] = $this->getBtns();

        $ref_name = '';
        $cache_name = 'current-user-branch-' . $this->playid;
        $cached_value = Appcaching::getGlobalCache( $cache_name );

        if ( isset($_REQUEST['referring_branch']) ) {
            Appcaching::setGlobalCache( $cache_name, 'ref-my-matches' );
            $ref_name = 'ref-my-matches';
        } else if ( $cached_value ) {
            $ref_name = $cached_value;
        }

        $id = 'report';
        if ( $ref_name ) {
            $id = 'report|' . $ref_name;
        }

        $this->data->footer[] = $this->getTextbutton('{#report_user#}', array( 'id' => $id, 'style' => 'report-user-button' ));

        if ($this->menuid == 'report') {
            $this->data->footer[] = $this->getText( '{#user_reported#}', array( 'text-align' => 'center', 'padding' => '0 5 8 5', 'font-size' => '13' ));
        }

    }

    public function getPicCount($vars){
        $count = 2;
        $piccount = 1;

        while ($count < 10) {
            $n = 'profilepic' . $count;
            if (isset($vars[$n]) AND strlen($vars[$n]) > 2) {
                $piccount++;
            }
            $count++;
        }

        return $piccount;
    }

    public function getBtns(){
        $btn_no_img = 'btn2-no2.png';
        if ( $this->getConfigParam( 'actionimage5' ) ) {
            $btn_no_img = $this->getConfigParam( 'actionimage5' );
        }

        $btn_yes_img = 'btn3-yes2.png';
        if ( $this->getConfigParam( 'actionimage6' ) ) {
            $btn_yes_img = $this->getConfigParam( 'actionimage6' );
        }

        $onclick_no = new StdClass();
        $onclick_no->action = 'open-action';
        $onclick_no->id = 'no_' . $this->profileid;
        $onclick_no->action_config = $this->getActionidByPermaname( 'people' );
        $onclick_no->sync_open = 1;

        $onclick_yes = new StdClass();
        $onclick_yes->action = 'open-action';
        $onclick_yes->id = 'yes_' . $this->profileid;
        $onclick_yes->action_config = $this->getActionidByPermaname( 'people' );
        $onclick_yes->sync_open = 1;
        
        $column[] = $this->getImage($btn_no_img, array(
            'width' => '25%',
            'onclick' => $onclick_no,
        ));

        $column[] = $this->getImage($btn_yes_img, array(
            'width' => '25%',
            'onclick' => $onclick_yes,
        ));

        return $this->getRow($column, array(
            'text-align' => 'center'
        ));
    }

}