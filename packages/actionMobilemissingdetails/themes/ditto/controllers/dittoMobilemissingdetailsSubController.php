<?php

class dittoMobilemissingdetailsSubController extends MobilemissingdetailsController {

    public function tab1(){

        $this->data = new StdClass();

        if(isset($this->varcontent['name'])){
            $name = $this->varcontent['name'];
            $first_name = explode( ' ', $name )[0];
        } else {
            $first_name = false;
        }

        $this->data->scroll[] = $this->getText( 'Hey '. $first_name .', it looks like we missed some of your details at the start. Complete them here to start finding your Ditto!', array( 'style' => 'missing-details-title' ) );

        $this->getMissingFields();

        if ( $this->show_error ) {
            $this->data->scroll[] = $this->getText( 'Oops, it seems that you\'ve missed some of the fields above.', array('style' => 'details-error') );
        }

        if ( isset($this->menuid) AND $this->menuid == 'submit-data' ) {
            $text = ( $this->show_error ? 'Submit details' : 'Thank you!' );
            $this->data->footer[] = $this->getTextbutton($text,array('id' => 'submit-data', 'style' => 'details-button'));
        } else {
            $this->data->footer[] = $this->getTextbutton('Submit details',array('id' => 'submit-data', 'style' => 'details-button'));
        }
        
        return $this->data;
    }

}