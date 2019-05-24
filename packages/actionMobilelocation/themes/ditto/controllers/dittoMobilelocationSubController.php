<?php

class dittoMobilelocationSubController extends MobilelocationController {

    public function tab1(){

        $this->data = new StdClass();

        $textstyle['font-size'] = '14';
        $textstyle['text-align'] = 'center';
        $textstyle['margin'] = '10 0 10 0';
        $textstyle['color'] = '#ffffff';
        $textstyle['font-size'] = '24';
        $textstyle['text-align'] = 'center';
        $textstyle['margin'] = '0 0 3 0';

        $imgstyle['crop'] = 'round';
        $imgstyle['margin'] = '20 60 10 60';
        $imgstyle['text-align'] = 'center';
        $imgstyle['variable'] = $this->getVariableId( 'profilepic' );
        $imgstyle['priority'] = 9;

        if(isset($this->varcontent['profilepic'])){
            $profilepic = $this->varcontent['profilepic'];
        } else {
            $profilepic = 'anonymous2.png';
        }

        $this->data->scroll[] = $this->getImage($profilepic,$imgstyle);

        $textstyle['font-size'] = '24';
        $textstyle['text-align'] = 'center';
        $textstyle['margin'] = '0 0 3 0';

        if(isset($this->varcontent['city'])){
            $tr_lang = ( $this->appinfo->name == 'Rantevu' ? 'el' : $this->lang );
            $this->data->scroll[] = $this->getText(ThirdpartyServices::translateString( $this->varcontent['city'], 'en', $tr_lang ) ,$textstyle);
        } else {
            $this->data->scroll[] = $this->getText('Unable to locate properly :(',$textstyle);
        }

        $textstyle['font-size'] = '14';

        if(isset($this->varcontent['country'])){
            $tr_lang = ( $this->appinfo->name == 'Rantevu' ? 'el' : $this->lang );
            $this->data->scroll[] = $this->getText(ThirdpartyServices::translateString( $this->varcontent['country'], 'en', $tr_lang ),$textstyle);
        }

        $textstyle['margin'] = '0 0 15 0';

        if(isset($this->varcontent['lat']) AND isset($this->varcontent['lon'])){
            $this->data->scroll[] = $this->getText($this->varcontent['lat'] .', ' .$this->varcontent['lon'],$textstyle);
        }

        $textstyle['color'] = '#ba5678';

        if($this->menuid == 'update-location') {
            if(isset($this->varcontent['lat']) AND $this->varcontent['lat']){
                MobilelocationModel::geoTranslate($this->varcontent,$this->gid,$this->playid);
            }

            $onload = new StdClass();
            $onload->action = 'submit-form-content';
            $onload->id = 'location-refreshed';

            $this->data->onload[] = $onload;
        }

        if ( $this->menuid == 'location-refreshed' ) {
            $this->data->scroll[] = $this->getText('{#location_updated#}',$textstyle);
        }

        $buttonparams['onclick'] = new StdClass();
        $buttonparams['onclick']->action = 'ask-location';
        $buttonparams['onclick']->sync_open = 1;
        $buttonparams['onclick']->id = 'update-location';
        $buttonparams['style'] = 'button-update-location';

        $this->loadVariableContent(true);
        $this->getText($this->getSavedVariable('city'));

        $this->data->footer[] = $this->getText('{#update_my_location#}',$buttonparams);

        return $this->data;
    }

}