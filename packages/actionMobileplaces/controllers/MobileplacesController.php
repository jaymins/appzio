<?php

/*

    this is a dynamic article action, which is launched either by
    Apiaction.php (component)
    Updatevariables (api method)
    Refreshaction (api method)

    If its called by either api method, the object is passed on to Apiaction.php eventually.

    Either RenderData or DynamicUpdate are called

    It should return json which gets put into the layoutconfig

    Data saving on picture submit is little complicated, because we upload
    async from the client. So once user has submitted a photo, we launch
    an async process to deal with that and to eventually add it to the action.
    Process is not perfect, as we rely on temporary variable values that might
    get overwritten if user uploads two photos very quickly after one another.

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');
Yii::import('application.modules.aelogic.packages.actionMobilegolf.models.*');

class MobileplacesController extends ArticleController {

    public $data;
    public $theme;

    /* @var MobileplacesModel */
    public $mobileplacesobj;
    public $submitted = false;

    public $places_wantsee;
    public $places_havebeen;
    
    /* instead of using playid and gameid as usual, it is possible to associate
       everything to another play. This is to facilitate communication between
       two apps.
    */

    public $current_playid;
    public $current_gid;

    /* info about all the courses users has purchased */
    public $purchased_courses;
    public $has_discount = false;
    public $course_bought;


    public function tab1(){
        $this->sharedInit();
        $this->saveCheckboxes();

        if($this->menuid == 'savecomplete'){
            $this->complete();
        }

        switch($this->getConfigParam('mode')){
            case 'collection':
                $this->modeCollection();
                break;

            case 'search_only':
                $this->modeSearch();
                break;

            case 'individual_item':
                $this->modeItem();
                break;

            case 'choose_home':
                $this->chooseHome();
                break;

            default:
                $this->modeCollection();
                break;

        }

        return $this->data;
    }

    /* aka my places */
    public function tab3(){
        $this->sharedInit();
        $this->setHeader(3);

        $this->funnyHeader();
        $wordlist = $this->mobileplacesobj->getMyClubs();

        $this->data->scroll[] = $this->getText('{#places_ive_visited#}',array(
            'style' => 'places_toptext_grey'
        ));

        if(is_array($wordlist)){
            foreach($wordlist as $word){
                $this->setPlaceRow($word);
            }

            $wordlist = $this->mobileplacesobj->getMyWishlist();

            $this->data->scroll[] = $this->getText('{#places on my wishlist#}',array(
                'style' => 'places_toptext_grey'
            ));

            foreach($wordlist as $word){
                $this->setPlaceRow($word);
            }
        }

        return $this->data;
    }

    public function sharedInit(){
        $this->data = new stdClass();

        /* IMPORTANT: if faux_pid and faux_gid are set for the user, we will be using those to refer to places */
        $this->fakePlay();
        $this->loadMyPlaces();

        $this->mobileplacesobj = new MobileplacesModel();
        $this->mobileplacesobj->playid = $this->current_playid;
        $this->mobileplacesobj->game_id = $this->current_gid;

        $courses = json_decode($this->getSavedVariable('golf_course_purchases'),true);

        if(!empty($courses)){
            $this->purchased_courses = $courses;
        }

        if($this->getSavedVariable('purchase_discountgreenfee') OR $this->getSavedVariable('discount_discountandmap')){
            $this->has_discount = true;
        }

    }

    /* aka the search tab */
    public function tab2(){
        $this->sharedInit();
        $this->saveCheckboxes();

        $this->setHeader(2);
        $this->searchBox();

        if(isset($this->submitvariables['searchterm'])){
            //$this->funnyHeader();

            $searchterm = $this->submitvariables['searchterm'];
            $wordlist = $this->mobileplacesobj->dosearch($searchterm);

            foreach($wordlist as $word){
                $this->setPlaceRow($word);
            }

        }

        return $this->data;
    }

    /* different modes */

    public function modeCollection(){
        //$this->basket();
        $this->setHeader(1);

/*       if(!$this->menuid){
            $this->data->scroll[] = $this->getText('{#please_collect_places_2#}',array(
                'visibility' => 'delay','visibility_delay' => '1',
                'transition' => 'pop', 'time_to_live' => '5',
                'style' => 'places_toptext_wide'
            ));
        }*/

        $this->funnyHeader();
        $wordlist = $this->mobileplacesobj->dosearch('');

        foreach($wordlist as $word){
            $this->setPlaceRow($word);
        }
    }

    /* we save the check boxes here */
    public function saveCheckboxes(){
        foreach ($this->submitvariables AS $key=>$submitvar){
            if(stristr($key,'placevar_')){
                $varid = str_replace('placevar_','',$key);
                if($submitvar == 'been'){
                    $this->places_havebeen[$varid] = true;
                    unset($this->places_wantsee[$varid]);
                } elseif($submitvar == 'want') {
                    $this->places_wantsee[$varid] = true;
                    unset($this->places_havebeen[$varid]);
                } else {
                    if(isset($this->places_wantsee[$varid])){
                        unset($this->places_wantsee[$varid]);
                    }
                    if(isset($this->places_havebeen[$varid])){
                        unset($this->places_havebeen[$varid]);
                    }
                }
            }
        }

        if($this->playid == $this->current_playid){
            $this->saveVariable('places_wantsee',json_encode($this->places_wantsee));
            $this->saveVariable('places_havebeen',json_encode($this->places_havebeen));
        } else {
            $obj = Aeplay::model()->findByPk($this->current_playid);
            if(is_object($obj)){
                AeplayVariable::updateWithName($this->current_playid,'places_wantsee',json_encode($this->places_wantsee),$this->current_gid);
                AeplayVariable::updateWithName($this->current_playid,'places_havebeen',json_encode($this->places_havebeen),$this->current_gid);
            }
        }

    }

    /* plain search mode */
    public function modeSearch(){
        $this->data = new stdClass();
        $this->searchBox();

        if(isset($this->submitvariables['searchterm'])){
            $searchterm = $this->submitvariables['searchterm'];
            $wordlist = $this->mobileplacesobj->dosearch($searchterm);
            foreach($wordlist as $word){
                $this->setPlaceSimple($word);
            }
        } else {
            $this->data->scroll[] = $this->getText('{#places_close_by#}',array(
                'style' => 'places_toptext_grey'
            ));

            $wordlist = $this->mobileplacesobj->dosearch('',8);
            foreach($wordlist as $word){
                $this->setPlaceSimple($word);
            }

        }

        return $this->data;
    }


    public function chooseHome(){
        $this->data = new stdClass();
        $this->searchBox(false);

        if($this->menuid == 'searchbox' AND isset($this->submitvariables['searchterm']) AND strlen($this->submitvariables['searchterm']) < 3) {
            $this->data->scroll[] = $this->getText('{#write_at_least_three_letters_to_search#}',array(
                'style' => 'places_toptext'
            ));

        } elseif($this->menuid == 'searchbox' AND isset($this->submitvariables['searchterm']) AND strlen($this->submitvariables['searchterm']) > 2){
            $searchterm = $this->submitvariables['searchterm'];
            $wordlist = $this->mobileplacesobj->dosearch($searchterm);
            foreach($wordlist as $word){
                $this->setPlaceSimple($word);
            }
        } else {
            $this->data->scroll[] = $this->getText('{#places_close_by#}',array(
                'style' => 'places_toptext_grey'
            ));

            $wordlist = $this->mobileplacesobj->dosearch('',8);
            foreach($wordlist as $word){
                $this->setPlaceSimple($word);
            }

        }

        return $this->data;
    }

    public function handlePurchase(){

        $return = MobilegolfModel::handlePurchase($this->gid,$this->playid,$this->varcontent,$this->menuid);

        if($return){
            $this->data->scroll[] = $this->getText('{#your_purchase_was_succesful#}',array('style' => 'places_toptext_wide'));
        } else {
            $this->data->scroll[] = $this->getText('{#unfortunately_your_purhcase_failed#}',array('style' => 'places_toptext_wide_red'));
        }
    }



    public function modeItem(){

        /* place's id */
        if(is_numeric($this->menuid)){
            $placeinfo = MobileplacesModel::model()->findByPk($this->menuid);
            if(is_object($placeinfo)){
                $id = $this->menuid;
                Appcaching::setGlobalCache($this->playid.'-place',$id);
            }
        } else {
            $id = Appcaching::getGlobalCache($this->playid.'-place');

            if(!$id){
                $this->data->scroll[] = $this->getText('{#not_found#}');
                return true;
            }

            $placeinfo = MobileplacesModel::model()->findByPk($id);
        }

        if(stristr($this->menuid,'inapp-')){
            $this->handlePurchase();
        }

        if(!isset($placeinfo->name)){
            $this->data->scroll[] = $this->getText('{#not_found#}');
            return true;
        }

        if(!is_object($placeinfo)){
            return false;
        }

        if(!isset($placeinfo->logo) OR $placeinfo->logo == 'dummylogo.png'){
            $placeinfo->logo = 'default-golf-logo.png';
        }

        $top[] = $this->getImage($placeinfo->logo,array('height' => '80','vertical-align' => 'middle','imgheight' => '160','priority' => 9));
        $this->data->scroll[] = $this->getRow($top,array('background-color' => '#ffffff','text-align' => 'center'));

        $this->data->scroll[] = $this->getText($placeinfo->name,array('style' => 'places_toptext_wide'));
        if(stristr($this->menuid,'free-claim-code')){
            $this->data->scroll[] = $this->getDiscountCode($placeinfo);
        } else {
            $this->data->scroll[] = $this->getTopSwipe($placeinfo);
        }

        $info = json_decode($placeinfo->info);

        /*On golf partners will there be 4 offers:
        -20% Discount on green fees -> Free
        -50 Reduction -> € 5.99 (to be paid within the app)
        Interactive course map -> € 3.99 (to be paid within the app) (later one we should allow seeing these)
        -50% + Interactive map route -> € 7.99 (to be paid within the app)
        */

        if($this->getConfigParam('purchases_enabled')){
            if(isset($placeinfo->premium) AND $placeinfo->premium){
                $this->data->scroll[] = $this->getText('{#exclusive_offers#}',array('style' => 'places_toptext_wide'));
                $this->data->scroll[] = $this->getSpacer('10');

                /* not offering map packages if the maps don't exist */
                $holes = MobilegolfholeModel::model()->findAllByAttributes(array('place_id' => $placeinfo->id));

                if(!$this->has_discount){
                    $this->getDiscountItem('-20% {#discount_on_green_fee#}','{#free#}');
                   // $this->getDiscountItem('-50% {#discount_on_green_fee#}','€5.99');
                }

                if($holes > 2){
                    if(!$this->has_discount) {
                       // $this->getDiscountItem('-50% {#discount_on_green_fee#} + {#map#}', '€7.99');
                    }

                    /* check whether user has purchased this course */
                    if(!isset($this->purchased_courses[$placeinfo->id])){
                        $this->getDiscountItem('{#interactive_map_and_scorecard#}','€3.99');
                    } else {
                        $this->course_bought = true;
                    }
                }

                $this->data->scroll[] = $this->getSpacer('10');

            }
        }


        $this->data->scroll[] = $this->getText('{#green_fees#}',array('style' => 'places_toptext_wide'));
        $this->getPriceItem($info,'greenfee_low_season_week');
        $this->getPriceItem($info,'greenfee_low_season_weekend');
        $this->getPriceItem($info,'greenfee_mid_season');
        $this->getPriceItem($info,'greenfee_high_season_week');
        $this->getPriceItem($info,'greenfee_high_season_weekend');

        $this->setAddress($placeinfo);
        //$this->restorePurchases();


    }

    public function restorePurchases(){
        $this->data->scroll[] = $this->getText('{#restore_previous_purchase#}',array('style' => 'places_toptext_wide'));
        $this->data->scroll[] = $this->getText('{#if_purchased_before_use_this_to_restore#}',array('style' => 'place_price_left'));
        $onclick = new StdClass();
        $onclick->action = 'inapp-restore';
        $this->data->scroll[] = $this->getText('{#restore_purchases#}',array('style' => 'general_button_style_red','onclick' => $onclick));
    }


    public function setAddress($placeinfo){
        $this->data->scroll[] = $this->getText('{#directions#}',array('style' => 'places_toptext_wide'));
        $this->data->scroll[] = $this->getText($placeinfo->address .', '.$placeinfo->county .', ' .$placeinfo->city,array('style' => 'place_price_left'));

        if($placeinfo->country){
            $this->data->scroll[] = $this->getText($placeinfo->country,array('style' => 'place_price_left'));
        }

        if($placeinfo->lat AND $placeinfo->lon){
            $onclick = new StdClass();
            $onclick->action_config = 'http://maps.apple.com/?q=' .$placeinfo->lat .',' .$placeinfo->lon;
            $onclick->action = 'open-url';
            $this->data->scroll[] = $this->getText('{#open_in_maps#}',array('style' => 'general_button_style_red','onclick' => $onclick));
        }

    }

    public function getDiscountItem($text,$discount){
        Yii::import('application.modules.aelogic.packages.actionMobilegolf.models.*');

        if($this->has_discount AND !$this->course_bought){
            $col[] = $this->getText($text,array('style' => 'place_deal_left'));
            $col[] = $this->getText($discount,array('style' => 'place_deal_middle'));

            $txt = '{#claim#}';
            $col[] = $this->getText($txt,array('style' => 'place_deal_buy','onclick' => MobilegolfModel::purchaseButton($discount,$this->menuid)));
        } else {
            $col[] = $this->getText($text,array('style' => 'place_deal_left'));
            $col[] = $this->getText($discount,array('style' => 'place_deal_middle'));

            if($discount == '{#free#}'){
                $txt = '{#claim#}';
            } else {
                $txt = '{#buy#}';
            }

            $col[] = $this->getText($txt,array('style' => 'place_deal_buy','onclick' => MobilegolfModel::purchaseButton($discount,$this->menuid)));
        }

        $this->data->scroll[] = $this->getRow($col);


    }


    public function getPriceItem($info,$name){
        if(isset($info->$name) AND $info->$name){
            $col[] = $this->getText("{#$name#}",array('style' => 'place_price_left'));

            if($this->has_discount){
                $col[] = $this->getText('€'.($info->$name)*0.5,array('style' => 'place_price_right'));

            } else {
                $col[] = $this->getText($info->$name,array('style' => 'place_price_right'));

            }

            $this->data->scroll[] = $this->getRow($col);
        }
    }


    /* search box component */
    public function searchBox($header=true){
        $value = $this->getSubmitVariable('searchterm') ? $this->getSubmitVariable('searchterm') : '';
        $row[] = $this->getImage('search-icon-for-field.png',array('height' => '25'));
        $row[] = $this->getFieldtext($value,array('style' => 'example_searchbox_text',
            'hint' => '{#free_text_search#}','submit_menu_id' => 'searchbox','variable' => 'searchterm',
            //'suggestions' => MobileexampleAccessor::getInitialWordList(10),
            'id' => 'something','width' => '70%',
            'suggestions_style_row' => 'example_list_row','suggestions_text_style' => 'example_list_text',
            'submit_on_entry' => '1','activation' => 'initially'
        ));
        $col[] = $this->getRow($row,array('style' => 'example_searchbox','width' => '70%'));
        $col[] = $this->getTextbutton('Search',array('style' => 'example_searchbtn','id' => 'dosearch'));
        $this->data->header[] = $this->getRow($col,array('background-color' => $this->color_topbar));

        if($header == true){
            $this->funnyHeader();
        }
        $this->data->scroll[] = $this->getLoader('Loading',array('color' => '#000000','visibility' => 'onloading'));
    }



    public function basket(){

        if($this->submitted){
            $col[] = $this->getImage('bucket-shopping.png',array('width' => '30','margin' => '10 10 10 10'));
        } else {
            $col[] = $this->getImage('bucket-shopping.png',array('width' => '30','margin' => '10 10 10 10'));
        }

        $count = count($this->places_havebeen) + count($this->places_wantsee);

        $onclick = new StdClass();
        $onclick->action = 'open-tab';
        $onclick->action_config = 3;

        $col[] = $this->getText($count.' clubs collected',array('margin' => '10 10 10 10','color' => '#ffffff','onclick' => $onclick));
        $this->data->footer[] = $this->getRow($col,array('background-color' => '#238c23','text-align' => 'center'));
    }


    public function funnyHeader(){
        $col[] = $this->getText('{#i_ve_been_here#}',array('width' => '100%','background-color' => '#238c23','padding' => '5 10 5 5','color' => '#ffffff','font-size' => '14','text-align' => 'right','height' => '50'));
        $row[] = $this->getRow($col);
        unset($col);
        $col[] = $this->getText('{#i_want_to_visit#}',array('width' => '85%','background-color' => '#29a529','padding' => '5 10 5 5','color' => '#ffffff','font-size' => '14','text-align' => 'right','height' => '50'));
        $col[] = $this->getText('',array('width' => '15%','background-color' => '#238c23','padding' => '5 5 5 5','height' => '50'));
        $row[] = $this->getRow($col);
        unset($col);
        $row[] = $this->getText(' ',array('height' => '2','background-color' => '#ffffff'));
        $this->data->header[] = $this->getColumn($row);
    }


    public function setPlaceSimple($data){

        $logo = isset($data->logo) ? $data->logo : 'default-golf-logo.png';
        if(!is_object($data)){
            return false;
        }

        if(!isset($data->logo) OR $data->logo == 'dummylogo.png'){
            $data->logo = 'default-golf-logo.png';
        }

        $id = $data['id'];

        $openinfo = new stdClass();
        $openinfo->action = 'open-action';
        $openinfo->id = $id;
        $openinfo->action_config = $this->getConfigParam('detail_view');
        $openinfo->open_popup = 1;
        $openinfo->sync_open = 1;

        $col[] = $this->getImage($logo,array('width' => '15%','vertical-align' => 'middle','priority' => 9));
        $col[] = $this->getPlaceRowPart($data,'100%',$openinfo);
        $this->data->scroll[] = $this->getRow($col,array('margin' => '0 0 2 0'));
    }

    public function getPlaceRowPart($data,$width='55%',$onclick){
        $distance = round($data['distance'],0) .'km';

        if(isset($data['premium']) AND $data['premium']){
            $bgcolor = '#47c640';
            $color = '#ffffff';
        } else {
            $bgcolor = '#ffffff';
            $color = '#424242';
        }

        $row[] = $this->getText($data['name'],array('padding' => '3 5 3 5','color' => $color,'font-size' => '13'));
        $row[] = $this->getText($data['county'],array('padding' => '0 5 3 5','color' => $color,'font-size' => '12'));
        $row[] = $this->getText($data['city'].', ' .$distance,array('padding' => '0 5 3 5','color' => $color,'font-size' => '12'));
        return $this->getColumn($row,array('width' => $width,'onclick'=>$onclick,'background-color' => $bgcolor));
    }

    public function setPlaceRow($data){

        if(!is_array($data)){
            return false;
        }

        if(!isset($data['logo']) OR $data['logo'] == 'dummylogo.png'){
            $data['logo'] = 'default-golf-logo.png';
        } else {
            $data['logo'] = basename($data['logo']);
        }

        $id = $data['id'];
        $openinfo = new stdClass();
        $openinfo->action = 'open-action';
        $openinfo->id = $id;
        $openinfo->action_config = $this->getConfigParam('detail_view');
        $openinfo->open_popup = 1;
        $openinfo->sync_open = 1;

        if($data['county'] == 'Piémont'){
            $country = 'Piemonte.gif';
        } else {
            $country = $data['country'] ? $data['country'] : 'FR';
            $country = $country.'.png';
        }

        if(isset($data['premium']) AND $data['premium']){
            $hmm[] = $this->getImage('star-icon.png',array('height' => '19','vertical-align' => 'middle','margin' => '0 4 0 4'));
            $hmm[] = $this->getImage('star-icon.png',array('height' => '19','vertical-align' => 'middle','margin' => '0 4 0 4'));
            $hmm[] = $this->getImage('star-icon.png',array('height' => '19','vertical-align' => 'middle','margin' => '0 4 0 4'));
            $hmm[] = $this->getText('{#premium_club_with_offers#}',array('style' => 'place_offer_title'));
            $hmm[] = $this->getImage('star-icon.png',array('height' => '19','vertical-align' => 'middle','margin' => '0 4 0 4'));
            $hmm[] = $this->getImage('star-icon.png',array('height' => '19','vertical-align' => 'middle','margin' => '0 4 0 4'));
            $hmm[] = $this->getImage('star-icon.png',array('height' => '19','vertical-align' => 'middle','margin' => '0 4 0 4'));
            $row[] = $this->getRow($hmm,array('background-color' => '#47c640','padding' => '4 4 4 4','vertical-align' => 'middle'));

            $col[] = $this->getImage($data['logo'],array('width' => '15%','vertical-align' => 'middle', 'imgheight' => '250','priority' => 9,
                'onclick' => $openinfo));
            $id = $data['id'];
            $col[] = $this->getImage($country,array('width' => '5%'));
            $col[] = $this->getPlaceRowPart($data,'50%',$openinfo);
            $col[] = $this->getButtons($id,'want');
            $col[] = $this->getButtons($id,'been');
            $row[] = $this->getRow($col,array('margin' => '0 0 0 0'));
            $row[] = $this->getText('',array('style' => 'place_offer_title'));
            $row[] = $this->getSpacer(2);
            $this->data->scroll[] =  $this->getColumn($row,array('background-color' => '#ffffff'));

        } else {
            $row[] = $this->getText('{#premium_club_with_offers#}',array('width' => '100%'));
            $col[] = $this->getImage($data['logo'],array('width' => '15%','vertical-align' => 'middle','imgwidth' => '160', 'imgheight' => '160','priority' => 9,'imgcrop' => 'yes','onclick' => $openinfo));
            $id = $data['id'];

            $col[] = $this->getImage($country,array('width' => '4%','margin' => '2 2 2 2'));
            $col[] = $this->getPlaceRowPart($data,'50%',$openinfo);
            $col[] = $this->getButtons($id,'want');
            $col[] = $this->getButtons($id,'been');
            $this->data->scroll[] = $this->getRow($col,array('margin' => '0 0 2 0','background-color' => '#ffffff'));
        }
    }

    public function getDot($delay){
        return $this->getText('',array('width' => '10', 'height' => '10','background-color' => '#459a35','border-radius' => '5','margin' => '10 0 0 60','visibility_delay' => $delay,'visibility' => 'delay','transition' => 'pop'));
    }

    public function getButtons($id,$type='been'){

        $this->copyAssetWithoutProcessing('checkmarkicon.png');
        $this->copyAssetWithoutProcessing('just-a-dot.png');

        if($type == 'been'){
            $background = '#238c23';

            if(isset($this->places_havebeen[$id])){
                $selectstate = array('style' => 'selector_checkbox_selected','active' => '1','variable_value' => 'been','allow_unselect' => 1,'animation' => 'fade');
            } else {
                $selectstate = array('style' => 'selector_checkbox_selected','variable_value' => 'been','allow_unselect' => 1,'animation' => 'fade');
            }
        } else {
            $background = '#29a529';

            if(isset($this->places_wantsee[$id])){
                $selectstate = array('style' => 'selector_checkbox_selected','active' => '1','variable_value' => 'want','allow_unselect' => 1,'animation' => 'fade');
            } else {
                $selectstate = array('style' => 'selector_checkbox_selected','variable_value' => 'want', 'allow_unselect' => 1, 'animation' => 'fade');
            }
        }

        $btn1[] = $this->getText('',array('style'=>'selector_checkbox_unselected','variable' => 'placevar_'.$id,'selected_state' => $selectstate));
        return $this->getColumn($btn1,array('width' => '15%','background-color' => $background,'padding' => '5 5 5 5','text-align' => 'center'));

    }

    public function setHeader($tab=1){
        $this->data->header[] = $this->getTabs(array('tab1' => '{#near_by#}','tab2' => '{#search#}','tab3' => '{#my_clubs#}'));

        if($this->getConfigParam('complete_action')){
            $this->data->footer[] = $this->getTextbutton('{#continue#}',array('id' => 'savecomplete'));
        }
    }

    public function loadMyPlaces(){
        if($this->playid == $this->current_playid){
            $this->places_wantsee = json_decode($this->getSavedVariable('places_wantsee'),true);
            $this->places_havebeen = json_decode($this->getSavedVariable('places_havebeen'),true);
        } else {
            $vars = AeplayVariable::getArrayOfPlayvariables($this->current_playid);
            if(isset($vars['places_wantsee'])){
                $this->places_wantsee = json_decode($vars['places_wantsee'],true);
            }

            if(isset($vars['places_havebeen'])){
                $this->places_havebeen = json_decode($vars['places_havebeen'],true);
            }
        }
    }

    public function complete(){
        $this->data->scroll[] = $this->getSpacer('10');
        $this->data->scroll[] = $this->getLoader('{#saving#}');
        $this->data->scroll[] = $this->getSpacer('10');
        $this->data->onload[] = $this->getCompleteAction();
    }


    public function getDiscountCode($data){

        $items[] = $this->getText('{#show_this_code_to_get_a#} 20% {#discount#}',array('text-align' => 'center','font-size' => 25));
        $items[] = $this->getText(Helper::generateShortcode(),array('text-align' => 'center','font-size' => 25,'margin' => '15 0 0 0'));

        if(isset($items)){
            return $this->getColumn($items,array('height' => '200','vertical-align' => 'middle','padding' => '10 40 10 40'));
        }
    }


    public function getTopSwipe($data){
        $swipnavi['margin'] = '-35 0 0 0';
        $swipnavi['align'] = 'center';
        $counter = 1;
        $images = array();

        if($data->headerimage1){
            $images[] = basename($data->headerimage1);
        } else {
            $images[] = 'placeholder-court.jpg';
        }

        if($data->headerimage2){
            $images[] = basename($data->headerimage2);
        }

        if($data->headerimage3){
            $images[] = basename($data->headerimage3);
        }

        foreach ($images as $image){
            $scroll[] = $this->getImage($image,array('width' => '100%','vertical-align' => 'middle','imgwidth' => '750', 'imgheight' => '376','priority' => 9,'imgcrop' => 'yes'));
            $scroll[] = $this->getSwipeNavi(count($images),$counter,$swipnavi);
            $items[] = $this->getColumn($scroll);
            unset($scroll);
            $counter++;
        }

        if(isset($items)){
            return $this->getSwipearea($items);
        }
    }


}