<?php

class dittoMobileuserprofileSubController extends MobileuserprofileController {

    public function showProfileView(){

        /* determine who's profile should we show */
        $this->initMobileMatching();
        $this->mobilematchingobj->initMatching($this->profileid);
        $this->doReporting();

        $vars = AeplayVariable::getArrayOfPlayvariables($this->profileid);
        $vars['user_play_id'] = $this->profileid;
        $this->data->scroll[] = $this->getImageScroll($vars);

        $this->getCurrentUserInfo( $vars );

        $this->profileCommentView( $vars );

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

        // $this->data->footer[] = $this->getText( $id );
        $this->data->footer[] = $this->getTextbutton('{#report_user#}', array( 'id' => 'report', 'style' => 'report-user-button' ));

        if ($this->menuid == 'report') {
            $this->data->footer[] = $this->getText( '{#user_reported#}', array( 'text-align' => 'center', 'padding' => '0 5 8 5', 'font-size' => '13' ));
        }

    }

    public function calculateDistance( $remote_lat, $remote_lng ) {
        $vars = AeplayVariable::getArrayOfPlayvariables($this->playid);

        $user_lat = $vars['lat'];
        $user_lng = $vars['lon'];

        return Helper::getDistance( $user_lat, $user_lng, $remote_lat, $remote_lng, 'K' );
    }

    public function getFirstName( $name ){
        
        if (!strstr($name, ' ')) {
            return $name;
        }

        $firstname = explode(' ', trim($name));
        $firstname = $firstname[0];

        return $firstname;
    }

    public function getCurrentUserInfo( $user_data, $play_id = false ) {

        $remote_user_play_id = $user_data['user_play_id'];

        $remote_user_vars = $this->getPlayVariables( $remote_user_play_id );

        if(isset($remote_user_vars['lat']) AND $remote_user_vars['lon']){
            $distance = $this->calculateDistance( $remote_user_vars['lat'], $remote_user_vars['lon'] );
            $distance = round($distance, 1) . ' KM';
        } else {
            $distance = 'distance unknown';
        }

        $age = isset($user_data['age']) ? $user_data['age'] : 'N/A';

        $name = isset($user_data['name']) ? $this->getFirstName( $user_data['name'] ) : 'Anonymous';
        $this->data->scroll[] = $this->getRow(array(
            $this->getText($name, array( 'style' => 'total-ratings-heading' )),
            $this->getText(', ' . $age, array( 'style' => 'total-ratings-heading' )),
        ), array( 'margin' => '15 0 5 0', 'text-align' => 'center', 'vertical-align' => 'middle' ));

        $this->data->scroll[] = $this->getRow(array(
            $this->getImage('icon-location.png', array( 'style' => 'total-ratings-results-image' )),
            $this->getText($distance, array( 'style' => 'total-ratings-results-text' )),
        ), array( 'margin' => '3 0 5 0', 'text-align' => 'center', 'vertical-align' => 'middle' ));

    }

    public function showProfileEdit(){

        if ( $this->menuid == 'save-data' ) {
            $this->saveVariables();
            $this->loadVariableContent(true);
        }

        $tttst['margin'] = '10 24 10 24';
        $tttst['text-align'] = 'center';
        $tttst['font-size'] = '14';
        $tttst['color'] = '#ffffff';

        /* set the screen pixels */
        $width = $this->screen_width ? $this->screen_width : 320;
        $this->margin = 10;
        $total_margin = $this->margin . ' ' . $this->margin . ' ' . $this->margin . ' ' . $this->margin;

        /* top part */
        if ($this->menuid == 'del-images' ){
            $text[] = $this->getText('{#click_on_images_to_delete#}',array('vertical-align' => 'top', 'font-size' => '22', 'color' => '#ffffff'));
            $this->data->scroll[] = $this->getRow($text,array('margin' => $total_margin, 'vertical-align' => 'middle'));
            $this->deleting = true;
        }

        if (strstr($this->menuid,'imgdel-')) {
            $del = str_replace('imgdel-','',$this->menuid);
            $this->saveVariable($del,'');
            $this->loadVariableContent(true);
        }

        $onclick = new StdClass();
        $onclick->action = 'submit-form-content';

        if($this->menuid == 'del-images') {
            $onclick->id = 'cancel-del';
        } else {
            $onclick->id = 'del-images';
        }

        $delbtn[] = $this->getImage('del-photos.png', array('floating' => '1', 'float' => 'left', 'width' => '25', 'height' => '25', 'onclick' => $onclick));
        $this->data->scroll[] = $this->getRow($delbtn, array('margin' => '10 ' . $this->margin . ' -35 ' . $this->margin));

        $this->data->scroll[] = $this->getProfileImage('profilepic',true, 'round');

        $column[] = $this->getProfileImage('profilepic2');
        $column[] = $this->getVerticalSpacer( 21 );
        $column[] = $this->getProfileImage('profilepic3');
        $column[] = $this->getVerticalSpacer( 21 );
        $column[] = $this->getProfileImage('profilepic4');
        $column[] = $this->getVerticalSpacer( 21 );
        $column[] = $this->getProfileImage('profilepic5');

        $this->data->scroll[] = $this->getSpacer( 15 );
        $this->data->scroll[] = $this->getRow($column,array('margin' => '0 ' .$this->margin .' 0 ' .$this->margin));

        $real_name = $this->getVariable('real_name');
                
        $location = $this->getVariable('city');
        if ( $this->getVariable('country') ) {
            $location .= ', ' . $this->getVariable('country');
        }

        $this->data->scroll[] = $this->getText($real_name, array( 'style' => 'user-profile-heading' ));

        $txt_var_id = $this->getVariableId('profile_comment');

        $user_text = $this->getVariable('profile_comment');
        if ( empty($user_text) ) {
            $user_text = '';
        }

        $this->data->scroll[] = $this->getFieldtextarea($user_text, array( 'variable' => $txt_var_id, 'style' => 'user-profile-text' ) );

        $this->data->footer[] = $this->getText('{#saving_can_take_a_while#}',$tttst);

        if($this->menuid == 'save-data'){
            $this->initMobileMatching();
            $this->mobilematchingobj->turnUserToItem(false,__FILE__);
            $this->data->footer[] = $this->getTextbutton('{#saved#}',array('style' => 'profile-save-button','id' => 'save-data'));
        } else {
            $this->data->footer[] = $this->getTextbutton('{#save#}',array('style' => 'profile-save-button','id' => 'save-data'));
        }

    }

    public function profileCommentView($vars){
        $textareastyle['margin'] = '10 24 10 20';
        $textareastyle['text-align'] = 'center';
        $textareastyle['font-size'] = '12';
        $textareastyle['color'] = '#ffffff';

        $textareastyle['variable'] = $this->getVariableId('profile_comment');
        if(isset($vars['profile_comment'])){
            $this->data->scroll[] = $this->getText($vars['profile_comment'],$textareastyle);
        }
    }

    public function getProfileImage($name, $mainimage = false, $crop = false){

        if($mainimage){
            $params['imgwidth'] = '600';
            $params['imgheight'] = '600';
            $params['margin'] = '20 80 0 80';
        } else {
            $params['width'] = '20%';
            $params['imgwidth'] = '600';
            $params['imgheight'] = '600';
        }

        $params['imgcrop'] = 'yes';
        $params['crop'] = ( $crop ? $crop : 'yes' );
        $params['defaultimage'] = 'profile-add-photo-grey.png';

        if($this->deleting AND $this->getSavedVariable($name) AND strlen($this->getSavedVariable($name)) > 2){
            $params['opacity'] = '0.6';
            $params['onclick'] = new StdClass();
            $params['onclick']->action = 'submit-form-content';
            $params['onclick']->id = 'imgdel-'.$name;
        } else {
            $params['onclick'] = new StdClass();
            $params['onclick']->action = 'upload-image';
            $params['onclick']->max_dimensions = '600';
            $params['onclick']->variable = $this->getVariableId($name);
            $params['onclick']->action_config = $this->getVariableId($name);
            $params['onclick']->sync_upload = true;
        }

        $params['variable'] = $this->getVariableId($name);
        $params['config'] = $this->getVariableId($name);
        $params['debug'] = 1;
        $params['fallback_image'] = 'selecting-image.png';
        $params['priority'] = '9';

        return $this->getImage($this->getVariable($name),$params);
    }
    
}